@extends('layouts.public')

@section('title', 'Layanan')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Layanan Pengecekan
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                Pilih layanan untuk mengecek informasi Anda.
            </p>
        </div>

        @if ($services->isEmpty())
            <div class="mt-12 text-center">
                <div class="mx-auto max-w-md rounded-lg border-2 border-dashed border-gray-300 p-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 15.75V7.5" />
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada layanan tersedia.</p>
                </div>
            </div>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <a href="{{ route('services.show', $service->slug) }}"
                       class="group rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md hover:border-indigo-200">
                        <div class="flex items-start gap-4">
                            @if ($service->icon)
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100">
                                    <x-icon :name="$service->icon" class="h-6 w-6" />
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600">
                                    {{ $service->name }}
                                </h3>
                                @if ($service->description)
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                        {{ $service->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
