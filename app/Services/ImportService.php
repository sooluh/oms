<?php

namespace App\Services;

use App\Enums\FieldType;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\ServiceRecord;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Collection;
use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ImportService
{
    private function detectDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return ',';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if ($firstLine === false) {
            return ',';
        }

        $delimiters = [';', ',', "\t", '|'];
        $counts = [];

        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($firstLine, $delimiter);
        }

        arsort($counts);

        return key($counts);
    }

    /**
     * @return array{headers: array<int, string>, rows: array<int, array<int, string>>}
     */
    public function parse(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $options = new CsvOptions;
            $options->FIELD_DELIMITER = $this->detectDelimiter($filePath);
            $reader = new CsvReader($options);
        } else {
            $reader = new XlsxReader;
        }

        $reader->open($filePath);

        $headers = [];
        $rows = [];
        $isFirstRow = true;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = [];
                foreach ($row->getCells() as $cell) {
                    $cells[] = $this->castCellValueToString($cell->getValue());
                }

                if ($isFirstRow) {
                    $headers = $cells;
                    $isFirstRow = false;
                } else {
                    $rows[] = $cells;
                }
            }
        }

        $reader->close();

        $rows = array_values(array_filter($rows, fn (array $row): bool => ! empty(array_filter($row, fn ($cell): bool => $cell !== ''))));

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string>>  $rows
     * @return array{
     *     rows: array<int, array{data: array<string, string>, errors: array<string, string>}>,
     *     summary: array{total: int, valid: int, invalid: int}
     * }
     */
    public function validate(Service $service, array $headers, array $rows): array
    {
        $fields = $service->fields()->ordered()->get();
        $importableFields = $fields->reject(fn ($field): bool => $field->type === FieldType::Attachment)->values();
        $identifierFields = $service->identifierFields()->get();

        $headerFieldMap = [];
        $headerErrors = [];

        foreach ($headers as $index => $header) {
            $field = $importableFields->get($index);

            if ($field !== null) {
                $headerFieldMap[$index] = $field;
            }
        }

        if (count($headers) < $importableFields->count()) {
            $headerErrors[] = 'Excel has '.count($headers).' columns, but service has '.$importableFields->count().' importable fields. ';
            $headerErrors[] = 'Columns must match fields in order: '.$importableFields->pluck('name')->implode(', ').'. Attachment fields are skipped during import.';
        }

        if (count($headers) > $importableFields->count()) {
            $headerErrors[] = 'Excel has '.count($headers).' columns ('.implode(', ', $headers).'), but service only has '.$importableFields->count().' importable fields.';
        }

        $validatedRows = [];

        foreach ($rows as $rowIndex => $row) {
            $data = [];
            $rowErrors = [];

            foreach ($headerFieldMap as $colIndex => $field) {
                $value = $row[$colIndex] ?? '';
                $data[$field->name] = $value;

                if ($field->required && trim($value) === '') {
                    $rowErrors[$field->name] = "{$field->label} is required.";
                }

                if (trim($value) !== '') {
                    if ($field->type === FieldType::Date && ! $this->isValidDate($value)) {
                        $rowErrors[$field->name] = "{$field->label} must be a valid date (YYYY-MM-DD).";
                    }

                    if ($field->type === FieldType::Boolean && ! in_array($value, ['0', '1', 0, 1, 'true', 'false', 'yes', 'no'], true)) {
                        $rowErrors[$field->name] = "{$field->label} must be a boolean value (0/1, yes/no, true/false).";
                    }
                }
            }

            $validatedRows[] = [
                'row_number' => $rowIndex + 2,
                'data' => $data,
                'errors' => $rowErrors,
            ];
        }

        $identifierValues = [];
        foreach ($identifierFields as $idField) {
            $seen = [];
            $colValues = [];

            foreach ($validatedRows as $ri => $vr) {
                $val = trim($vr['data'][$idField->name] ?? '');
                if ($val === '') {
                    continue;
                }

                $colValues[] = $val;

                if (isset($seen[$val])) {
                    $identifierValues[] = "Duplicate {$idField->label} value '{$val}' found in rows ".($seen[$val] + 2).' and '.($ri + 2).'.';
                } else {
                    $seen[$val] = $ri;
                }
            }

            if (! empty($colValues)) {
                $column = 'identifier_'.$idField->identifier_order;
                $existing = ServiceRecord::where('service_id', $service->id)
                    ->whereIn($column, $colValues)
                    ->pluck($column)
                    ->toArray();

                foreach ($existing as $existingVal) {
                    $identifierValues[] = "{$idField->label} value '{$existingVal}' already exists in the database.";
                }
            }
        }

        $validCount = count(array_filter($validatedRows, fn (array $vr): bool => empty($vr['errors'])));
        $invalidCount = count($validatedRows) - $validCount;

        return [
            'header_errors' => $headerErrors,
            'rows' => $validatedRows,
            'identifier_conflicts' => $identifierValues,
            'summary' => [
                'total' => count($validatedRows),
                'valid' => $validCount,
                'invalid' => $invalidCount,
            ],
        ];
    }

    /**
     * @param  Collection<int, ServiceField>  $identifierFields
     * @param  array<int, array{data: array<string, string>}>  $validatedRows
     * @return int Number of rows imported
     */
    public function import(Service $service, $identifierFields, array $validatedRows): int
    {
        $imported = 0;

        foreach ($validatedRows as $validatedRow) {
            $data = $validatedRow['data'];

            $record = [
                'service_id' => $service->id,
                'identifier_1' => null,
                'identifier_2' => null,
                'identifier_3' => null,
                'identifier_4' => null,
                'identifier_5' => null,
                'data' => $data,
            ];

            foreach ($identifierFields as $idField) {
                $column = 'identifier_'.$idField->identifier_order;
                $record[$column] = $data[$idField->name] ?? null;
            }

            ServiceRecord::create($record);
            $imported++;
        }

        return $imported;
    }

    private function castCellValueToString(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if ($value instanceof DateInterval) {
            return $value->format('%d days %H:%I:%S');
        }

        return (string) $value;
    }

    private function isValidDate(string $value): bool
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return true;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];

        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat($format, $value);
            if ($d !== false && $d->format($format) === $value) {
                return true;
            }
        }

        return false;
    }
}
