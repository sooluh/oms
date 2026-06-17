@extends('layouts.public')

@section('title', $settings->school_name)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-section sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <a href="{{ $settings->school_website }}" target="_blank" rel="nofollow noopener noreferrer"
                class="inline-block rounded-pill bg-surface-card px-4 py-1.5 text-[13px] font-medium text-muted transition-colors hover:text-ink">
                {{ $settings->school_name }}
            </a>
            <h1 class="mt-6 text-4xl font-semibold leading-tight tracking-tight text-ink sm:text-5xl lg:text-6xl">
                {{ $settings->title }}
            </h1>
            <p class="mt-4 text-lg text-muted">
                {{ $settings->subtitle }}
            </p>
        </div>
    </div>

    @if ($services->isEmpty())
        <div class="mx-auto max-w-7xl px-4 pb-section sm:px-6 lg:px-8">
            <div class="mx-auto max-w-md text-center">
                <div class="rounded-xl border border-dashed border-hairline bg-surface-card p-16">
                    <x-phosphor-archive-bold class="mx-auto h-12 w-12 text-muted-soft" />
                    <p class="mt-4 text-muted">No services available yet.</p>
                </div>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 pb-section sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $index => $service)
                    @php
                        $palette = [
                            [
                                'bg' => 'var(--color-brand-pink)',
                                'text' => '#ffffff',
                                'muted' => 'rgba(255,255,255,0.7)',
                                'desc' => '#ffffff',
                            ],
                            [
                                'bg' => 'var(--color-brand-lavender)',
                                'text' => '#0a0a0a',
                                'muted' => 'rgba(10,10,10,0.6)',
                                'desc' => '#3a3a3a',
                            ],
                            [
                                'bg' => 'var(--color-brand-peach)',
                                'text' => '#0a0a0a',
                                'muted' => 'rgba(10,10,10,0.6)',
                                'desc' => '#3a3a3a',
                            ],
                            [
                                'bg' => 'var(--color-brand-teal)',
                                'text' => '#ffffff',
                                'muted' => 'rgba(255,255,255,0.7)',
                                'desc' => '#ffffff',
                            ],
                            [
                                'bg' => 'var(--color-brand-ochre)',
                                'text' => '#0a0a0a',
                                'muted' => 'rgba(10,10,10,0.6)',
                                'desc' => '#3a3a3a',
                            ],
                            [
                                'bg' => 'var(--color-surface-card)',
                                'text' => '#0a0a0a',
                                'muted' => 'rgba(10,10,10,0.6)',
                                'desc' => '#3a3a3a',
                            ],
                        ];
                        $colors = $palette[$index % count($palette)];
                    @endphp

                    <a href="{{ route('service.show', $service->slug) }}"
                        class="group relative overflow-hidden rounded-xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                        style="background-color: {{ $colors['bg'] }};">

                        @if ($service->icon)
                            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg"
                                style="background-color: rgba(255,255,255,0.2);">
                                <x-icon :name="$service->icon" class="h-6 w-6" style="color: {{ $colors['text'] }};" />
                            </div>
                        @endif

                        <h3 class="text-xl font-semibold leading-tight" style="color: {{ $colors['text'] }}">
                            {{ $service->name }}
                        </h3>

                        @if ($service->description)
                            <p class="mt-2 text-sm leading-relaxed line-clamp-2" style="color: {{ $colors['desc'] }}">
                                {{ $service->description }}
                            </p>
                        @endif

                        <div class="mt-6 flex items-center gap-1.5 text-sm font-medium transition-all group-hover:gap-2"
                            style="color: {{ $colors['muted'] }}">
                            Check Now
                            <x-phosphor-arrow-right-bold class="h-4 w-4" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
