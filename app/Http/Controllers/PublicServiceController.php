<?php

namespace App\Http\Controllers;

use App\Models\Service;

class PublicServiceController extends Controller
{
    public function index()
    {
        $services = Service::active()->ordered()->get();

        return view('welcome', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        return view('services.show', compact('service'));
    }
}
