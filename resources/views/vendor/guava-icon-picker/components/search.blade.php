@php
    use function Filament\Support\generate_loading_indicator_html;
@endphp

@props(['field', 'sets', 'searchPrompt', 'isDropdown' => false])

<x-filament::section compact @class([
    'absolute w-full top-full left-0 mt-2 z-10' => $isDropdown,
]) x-bind="dropdownMenu" x-cloak x-init="set = '{{ $sets->first()?->getId() }}';">
    <div class="flex flex-col gap-4 @container">
        <x-filament::input.wrapper>
            <x-filament::input
                x-on:input.debounce.500ms="
                    const val = $event.target.value + ' bold';
                    const deferId = deferLoadingState();
                    if (val.length) {
                        resultsVisible = [];
                        resultsIndex = 0;
                        results = fuse.search(val).map(r => r.item);
                        addSearchResultsChunk();
                    } else {
                        resetSearchResults();
                    }
                    clearTimeout(deferId);
                    isLoading = false;
                "
                :placeholder="$searchPrompt"></x-filament::input>
        </x-filament::input.wrapper>

        <x-guava-icon-picker::searching :field="$field" />
        <x-guava-icon-picker::no-results-found :field="$field" />

        {{ $field->getSearchResultsViewComponent() }}
    </div>
</x-filament::section>
