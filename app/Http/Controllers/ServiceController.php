<?php

namespace App\Http\Controllers;

use App\Enums\FieldType;
use App\Enums\SearchMode;
use App\Models\SearchLog;
use App\Models\Service;
use App\Models\ServiceRecord;
use App\Services\CryptoService;
use App\Settings\AppSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(AppSettings $settings)
    {
        $services = Service::active()->ordered()->get();

        return view('pages.home', compact('services', 'settings'));
    }

    public function show(string $slug)
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        $identifiers = $service->identifierFields()->get();
        $cryptoService = app(CryptoService::class);
        $isOpen = $service->isWithinDateRange();

        return view('pages.search', compact('service', 'identifiers', 'cryptoService', 'isOpen'));
    }

    public function search(Request $request, string $slug)
    {
        /** @var CryptoService $crypto */
        $crypto = app(CryptoService::class);

        try {
            $decrypted = $crypto->decryptRequest($request->getContent());
        } catch (\RuntimeException $e) {
            return response()->json(['error' => 'Decryption failed.'], 400);
        }

        $formData = $decrypted['data'];
        $aesKey = $decrypted['aes_key'];

        $service = Service::active()->where('slug', $slug)->firstOrFail();
        $identifiers = $service->identifierFields()->get();

        if (! $service->isWithinDateRange()) {
            $encrypted = $crypto->encryptResponse([
                'message' => 'This service is not currently open for searches.',
            ], $aesKey);

            return response($encrypted, 403, ['Content-Type' => 'application/octet-stream']);
        }

        $rules = [];

        if ($service->search_mode === SearchMode::Or) {
            $rules['search'] = ['required', 'string', 'max:255'];
        } else {
            foreach ($identifiers as $idField) {
                $fieldRules = ['required', 'max:255'];

                if ($idField->type === FieldType::Date) {
                    $fieldRules[] = 'date';
                } elseif ($idField->type === FieldType::Boolean) {
                    $fieldRules[] = 'in:0,1';
                } else {
                    $fieldRules[] = 'string';
                }

                $rules['identifiers.'.$idField->identifier_order] = $fieldRules;
            }
        }

        $validator = Validator::make($formData, $rules, attributes: $identifiers->mapWithKeys(fn ($f) => [
            'identifiers.'.$f->identifier_order => $f->label,
        ])->toArray());

        if ($validator->fails()) {
            $encrypted = $crypto->encryptResponse([
                'errors' => $validator->errors()->toArray(),
            ], $aesKey);

            return response($encrypted, 422, ['Content-Type' => 'application/octet-stream']);
        }

        $validated = $validator->validated();

        $ip = $request->ip();

        if ($service->max_attempt && $service->attempt_duration) {
            $lockoutTime = now()->subMinutes($service->attempt_duration);

            $failedAttempts = SearchLog::where('service_id', $service->id)
                ->where('ip_address', $ip)
                ->where('is_success', false)
                ->where('created_at', '>=', $lockoutTime)
                ->count();

            if ($failedAttempts >= $service->max_attempt) {
                $lastFailed = SearchLog::where('service_id', $service->id)
                    ->where('ip_address', $ip)
                    ->where('is_success', false)
                    ->where('created_at', '>=', $lockoutTime)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $diffInMinutes = $lastFailed
                    ? ceil($service->attempt_duration - now()->diffInMinutes($lastFailed->created_at))
                    : $service->attempt_duration;

                $diffInMinutes = max(1, $diffInMinutes);

                $encrypted = $crypto->encryptResponse([
                    'lockout' => "Too many failed search attempts. Access temporarily blocked. Please try again in {$diffInMinutes} minute(s).",
                ], $aesKey);

                return response($encrypted, 429, ['Content-Type' => 'application/octet-stream']);
            }
        }

        $visibleFields = $service->fields()->where('visible', true)->ordered()->get();

        $query = ServiceRecord::query()->where('service_id', $service->id);

        if ($service->search_mode === SearchMode::Or) {
            $searchValue = $validated['search'];
            $inputValue = $searchValue;

            $query->where(function (Builder $q) use ($identifiers, $searchValue) {
                foreach ($identifiers as $idField) {
                    $column = 'identifier_'.$idField->identifier_order;
                    $q->orWhere($column, $searchValue);
                }
            });
        } else {
            $inputValue = json_encode($validated['identifiers']);

            foreach ($identifiers as $idField) {
                $column = 'identifier_'.$idField->identifier_order;
                $value = $validated['identifiers'][$idField->identifier_order];

                $query->where($column, $value);
            }
        }

        $result = $query->limit(10)->get();

        SearchLog::create([
            'service_id' => $service->id,
            'input_value' => $inputValue,
            'is_success' => $result->isNotEmpty(),
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
        ]);

        $responseData = $result->isEmpty()
            ? ['found' => false, 'message' => 'Data not found. Please check your identifier(s) and try again.']
            : ['found' => true, 'fields' => $visibleFields->filter(fn ($field) => ! is_null($result->first()->data[$field->name] ?? null))->values()->map(fn ($field) => [
                'label' => $field->label,
                'value' => $field->type === FieldType::Attachment
                    ? (($val = $result->first()->data[$field->name] ?? null) ? Storage::disk('public')->url($val) : null)
                    : ($result->first()->data[$field->name] ?? null),
                'type' => $field->type->value,
                'true_label' => $field->true_label,
                'false_label' => $field->false_label,
            ])];

        $encrypted = $crypto->encryptResponse($responseData, $aesKey);

        return response($encrypted, 200, ['Content-Type' => 'application/octet-stream']);
    }
}
