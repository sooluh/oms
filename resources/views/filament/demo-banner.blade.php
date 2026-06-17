@php
    $timezone = config('app.timezone', 'UTC');
@endphp

<style>
    body {
        margin-top: 2.75rem;
    }

    .fi-body-has-topbar .fi-sidebar {
        @media (width >=64rem) {
            top: calc(4rem + 2.75rem);
        }
    }

    .fi-body-has-topbar .fi-sidebar {
        @media (width >=64rem) {
            height: calc(100dvh - 4rem - 2.75rem);
        }
    }

    .fi-topbar-ctn,
    .fi-no-database.fi-modal-slide-over > .fi-modal-window-ctn {
        top: 2.75rem !important;
    }

    .fi-no {
        top: calc(2.75rem + 1rem) !important;
    }
</style>

<div
    class="fixed inset-x-0 top-0 z-9999 flex items-center justify-center gap-x-2 border-b border-white/5 bg-gray-950 px-4 py-3 text-sm text-white shadow-lg">
    <x-phosphor-info-bold class="h-3 w-3 shrink-0 text-warning" />
    <span>
        <span class="font-semibold">Demo Mode</span>
        <span class="mx-1 text-white/50">&mdash;</span>
        All demo data is automatically reset daily at
        <span class="font-medium">00:00 {{ $timezone }}</span>.
    </span>
</div>
