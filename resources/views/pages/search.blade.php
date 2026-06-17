@php
    use App\Enums\FieldType;
    use App\Enums\SearchMode;
@endphp

@extends('layouts.public')

@section('title', $service->name)

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-section sm:px-6 lg:px-8">

        <div class="text-center">
            <div class="inline-flex items-center justify-center gap-3">
                @if ($service->icon)
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-pink/10 text-brand-pink">
                        <x-icon :name="$service->icon" class="h-5 w-5" />
                    </div>
                @endif
                <h1 class="text-display-sm font-semibold leading-tight tracking-tight text-ink">
                    {{ $service->name }}
                </h1>
            </div>
            @if ($service->description)
                <p class="mt-2 text-muted">{{ $service->description }}</p>
            @endif
        </div>

        @if ($identifiers->isEmpty())
            <div class="mt-10 rounded-xl bg-surface-card p-8 text-center">
                <p class="text-sm text-muted">This service has not set up search identifiers yet.</p>
            </div>
        @elseif (! $isOpen)
            <div class="mt-10 rounded-xl border border-dashed border-hairline bg-surface-card/50 p-8 text-center">
                <x-phosphor-clock-countdown-bold class="mx-auto h-10 w-10 text-muted-soft" />
                @php
                    $message = 'This service is not currently open for searches.';

                    if ($service->start_date && $service->start_date->isFuture()) {
                        $message = 'This service will be available starting ' . $service->start_date->format('F j, Y') . '.';
                    } elseif ($service->end_date && $service->end_date->isPast()) {
                        $message = 'This service was closed on ' . $service->end_date->format('F j, Y') . ' and is no longer available.';
                    }
                @endphp
                <p class="mt-3 text-sm text-muted">{{ $message }}</p>
            </div>
        @else
            <div class="mt-10 rounded-xl border border-hairline bg-surface-card p-8">
                <div id="error-container"></div>

                <form id="search-form"
                    data-public-key="{{ $cryptoService->getPublicKeySpkiBase64() }}"
                    data-search-url="{{ route('service.search', $service->slug) }}"
                    data-search-mode="{{ $service->search_mode->value }}"
                    class="space-y-5">

                    @if ($service->search_mode === SearchMode::Or)
                        <div>
                            <label for="search" class="block text-sm font-medium text-body-strong">
                                Enter {{ $identifiers->pluck('label')->implode(' or ') }}
                            </label>
                            <input type="text" id="search" name="search"
                                class="mt-1.5 block w-full rounded-md border border-hairline bg-canvas px-4 py-2.5 text-sm text-ink placeholder-muted-soft outline-none transition-all focus:border-ink focus:ring-1 focus:ring-ink"
                                placeholder="Enter {{ $identifiers->pluck('label')->implode(' or ') }}" autocomplete="off">
                        </div>
                    @else
                        @foreach ($identifiers as $identifier)
                            <div>
                                <label for="identifier_{{ $identifier->identifier_order }}"
                                    class="block text-sm font-medium text-body-strong">
                                    {{ $identifier->label }}
                                </label>

                                @if ($identifier->type === FieldType::Boolean)
                                    <select id="identifier_{{ $identifier->identifier_order }}"
                                        name="identifiers[{{ $identifier->identifier_order }}]"
                                        class="mt-1.5 block w-full rounded-md border border-hairline bg-canvas px-4 py-2.5 text-sm text-ink outline-none transition-all focus:border-ink focus:ring-1 focus:ring-ink">
                                        <option value="1">{{ $identifier->true_label ?? 'Yes' }}</option>
                                        <option value="0">{{ $identifier->false_label ?? 'No' }}</option>
                                    </select>
                                @else
                                    <input type="{{ $identifier->type === FieldType::Date ? 'date' : 'text' }}"
                                        id="identifier_{{ $identifier->identifier_order }}"
                                        name="identifiers[{{ $identifier->identifier_order }}]"
                                        class="mt-1.5 block w-full rounded-md border border-hairline bg-canvas px-4 py-2.5 text-sm text-ink placeholder-muted-soft outline-none transition-all focus:border-ink focus:ring-1 focus:ring-ink"
                                        placeholder="Enter {{ $identifier->label }}" autocomplete="off">
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <button type="submit" id="search-btn"
                        class="inline-flex w-full items-center justify-center rounded-md bg-ink px-6 py-2.5 text-sm font-semibold text-on-primary transition-all hover:bg-body-strong focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2">
                        Search
                    </button>
                </form>
            </div>

            <div id="results-container" class="mt-6">
                <div class="rounded-xl border border-dashed border-hairline bg-surface-card/50 p-8 text-center text-sm text-muted-soft">
                    Enter your data above and click "Search" to view results.
                </div>
            </div>
        @endif
    </div>

    <template id="tmpl-results">
        <div class="rounded-xl border border-hairline bg-surface-card p-8">
            <div class="space-y-4" data-fields></div>
        </div>
    </template>

    <template id="tmpl-field">
        <div>
            <span class="text-sm font-medium text-muted"><span data-label></span></span>
            <p class="mt-0.5 text-base text-ink"><span data-value></span></p>
        </div>
    </template>

    <template id="tmpl-field-link">
        <div>
            <span class="text-sm font-medium text-muted"><span data-label></span></span>
            <p class="mt-0.5 text-base text-ink"><a data-value target="_blank" rel="nofollow noopener" class="text-brand-pink underline underline-offset-2 hover:no-underline"></a></p>
        </div>
    </template>

    <template id="tmpl-field-attachment">
        <div>
            <span class="text-sm font-medium text-muted"><span data-label></span></span>
            <p class="mt-0.5 text-base text-ink"><a data-value target="_blank" rel="nofollow noopener" class="inline-flex items-center gap-1.5 text-brand-pink underline underline-offset-2 hover:no-underline"></a></p>
        </div>
    </template>

    <template id="tmpl-not-found">
        <div class="rounded-xl border border-hairline bg-surface-card p-8 text-center">
            <x-phosphor-x-circle-bold class="mx-auto h-10 w-10 text-muted-soft" />
            <p class="mt-3 text-muted" data-message></p>
        </div>
    </template>

    <template id="tmpl-error">
        <div class="mt-4 rounded-lg bg-error/10 p-4 text-sm text-error font-medium"></div>
    </template>
@endsection
