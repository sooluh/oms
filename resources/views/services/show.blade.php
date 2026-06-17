@extends('layouts.public')

@section('title', $service->name)

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="text-center">
            <div class="flex items-center justify-center gap-3">
                @if ($service->icon)
                    <x-icon :name="$service->icon" class="h-8 w-8 text-indigo-600" />
                @endif
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    {{ $service->name }}
                </h1>
            </div>
            @if ($service->description)
                <p class="mt-2 text-gray-600">{{ $service->description }}</p>
            @endif
        </div>

        <!-- Search Form -->
        <div class="mt-10 rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Cari Data Anda</h2>
            <p class="mt-1 text-sm text-gray-500">
                Masukkan data identifier untuk mencari informasi Anda.
            </p>

            <form class="mt-6 space-y-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Identifier</label>
                    <input type="text" id="search" name="search"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           placeholder="Masukkan NIS, NISN, atau identifier lainnya">
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Cari
                </button>
            </form>
        </div>

        <!-- Result placeholder -->
        <div class="mt-6 rounded-lg border border-gray-200 p-8 text-center text-sm text-gray-400">
            Masukkan identifier di atas lalu klik "Cari" untuk melihat hasil.
        </div>
    </div>
@endsection
