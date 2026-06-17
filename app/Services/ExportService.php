<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Carbon;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    public function export(
        Service $service,
        string $format,
        bool $includeHidden = false,
        ?string $startDate = null,
        ?string $endDate = null
    ): BinaryFileResponse {
        $fields = $service->fields()
            ->when(! $includeHidden, fn ($query) => $query->where('visible', true))
            ->ordered()
            ->get();

        $extension = $format === 'csv' ? 'csv' : 'xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_').'.'.$extension;

        $writer = $format === 'csv' ? new CsvWriter : new XlsxWriter;
        $writer->openToFile($tempFile);

        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field->label.($field->is_identifier ? ' (Key)' : '');
        }
        $headers[] = 'Imported At';

        $writer->addRow(Row::fromValues($headers));
        $query = $service->records()->orderBy('created_at', 'asc');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $query->chunk(500, function ($records) use ($writer, $fields) {
            foreach ($records as $record) {
                $rowValues = [];

                foreach ($fields as $field) {
                    $rowValues[] = $record->data[$field->name] ?? null;
                }

                $rowValues[] = $record->created_at->toDateTimeString();
                $writer->addRow(Row::fromValues($rowValues));
            }
        });

        $writer->close();

        $filename = str($service->name)->slug().'-export-'.now()->format('Ymd-His').'.'.$extension;

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
