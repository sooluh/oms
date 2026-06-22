<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\ServiceRecord;
use App\Settings\AppSettings;
use Database\Seeders\DemoServiceSeeder;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function reset(Request $request)
    {
        if (! config('app.is_demo')) {
            return response()->json(['message' => 'Demo mode is not enabled.'], 403);
        }

        $secret = config('app.demo_secret');
        $key = $request->header('X-Demo-Secret') ?? $request->query('key');

        if (empty($secret) || $key !== $secret) {
            return response()->json(['message' => 'Unauthorized. Invalid secret key.'], 401);
        }

        SearchLog::query()->delete();
        ServiceRecord::query()->delete();
        ServiceField::query()->delete();
        Service::query()->delete();

        (new DemoServiceSeeder)->run();
        app(AppSettings::class)->resetToDefaults();

        return response()->json(['message' => 'Demo data reset successfully.']);
    }
}
