<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use App\Models\Service;
use App\Models\ServiceRecord;
use App\Services\CryptoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::active()->ordered()->get();

        return view('pages.home', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        $identifiers = $service->identifierFields()->get();

        $cryptoService = app(CryptoService::class);

        return view('pages.search', compact('service', 'identifiers', 'cryptoService'));
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

        $rules = [];

        if ($service->search_mode === 'OR') {
            $rules['search'] = ['required', 'string', 'max:255'];
        } else {
            foreach ($identifiers as $idField) {
                $fieldRules = ['required', 'max:255'];

                if ($idField->type === 'date') {
                    $fieldRules[] = 'date';
                } elseif ($idField->type === 'number') {
                    $fieldRules[] = 'numeric';
                } else {
                    $fieldRules[] = 'string';
                }

                $rules['identifiers.'.$idField->identifier_order] = $fieldRules;
            }
        }

        $validator = Validator::make($formData, $rules);

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

        if ($service->search_mode === 'OR') {
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
            : ['found' => true, 'fields' => $visibleFields->map(fn ($field) => [
                'label' => $field->label,
                'value' => $result->first()->data[$field->name] ?? '-',
            ])];

        $encrypted = $crypto->encryptResponse($responseData, $aesKey);

        return response($encrypted, 200, ['Content-Type' => 'application/octet-stream']);
    }
}
