<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg bg-gray-50 p-4 text-center">
            <span class="text-2xl font-bold text-gray-900">{{ $data['summary']['total'] }}</span>
            <p class="mt-1 text-sm text-gray-500">Total Rows</p>
        </div>
        <div class="rounded-lg bg-success-50 p-4 text-center">
            <span class="text-2xl font-bold text-success-600">{{ $data['summary']['valid'] }}</span>
            <p class="mt-1 text-sm text-success-700">Valid Rows</p>
        </div>
        <div class="rounded-lg bg-danger-50 p-4 text-center">
            <span class="text-2xl font-bold text-danger-600">{{ $data['summary']['invalid'] }}</span>
            <p class="mt-1 text-sm text-danger-700">Invalid Rows</p>
        </div>
    </div>

    @if (!empty($data['header_errors']))
        <div class="rounded-lg border border-danger-200 bg-danger-50 p-4">
            <h4 class="text-sm font-semibold text-danger-800">Column Mapping Errors</h4>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-danger-700">
                @foreach ($data['header_errors'] as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!empty($data['identifier_conflicts']))
        <div class="rounded-lg border border-warning-200 bg-warning-50 p-4">
            <h4 class="text-sm font-semibold text-warning-800">Identifier Conflicts</h4>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-warning-700">
                @foreach ($data['identifier_conflicts'] as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!empty($data['rows']))
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Status</th>
                        @if (!empty($data['rows'][0]['data']))
                            @foreach (array_keys($data['rows'][0]['data']) as $header)
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ $header }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($data['rows'] as $row)
                        @php $hasErrors = !empty($row['errors']); @endphp
                        <tr class="{{ $hasErrors ? 'bg-danger-50' : 'hover:bg-gray-50' }}">
                            <td class="whitespace-nowrap px-3 py-2 text-gray-500">{{ $row['row_number'] }}</td>
                            <td class="whitespace-nowrap px-3 py-2">
                                @if ($hasErrors)
                                    <span class="inline-flex items-center gap-1 text-danger-600"
                                        title="{{ implode(', ', $row['errors']) }}">
                                        <x-filament::icon icon="phosphor-x-circle-bold" class="h-4 w-4" />
                                        <span class="text-xs">Error</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-success-600">
                                        <x-filament::icon icon="phosphor-check-circle-bold" class="h-4 w-4" />
                                        <span class="text-xs">Valid</span>
                                    </span>
                                @endif
                            </td>
                            @foreach ($row['data'] as $key => $value)
                                <td
                                    class="whitespace-nowrap px-3 py-2 {{ isset($row['errors'][$key]) ? 'text-danger-600 font-medium' : 'text-gray-900' }}">
                                    {{ $value ?: '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
