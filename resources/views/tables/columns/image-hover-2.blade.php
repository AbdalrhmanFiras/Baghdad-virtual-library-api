@php
    $url = $record->image ? \Illuminate\Support\Facades\Storage::disk('s3-private')->temporaryUrl($record->image->url, now()->addMinutes(10)) : null;
@endphp

@if ($url)
    <div x-data="{ open: false, top: 0, left: 0 }" class="relative flex items-center">
        <img src="{{ $url }}"
             @mouseenter="
                open = true;
                const rect = $el.getBoundingClientRect();
                top = rect.top;
                left = rect.right + 10;
             "
             @mouseleave="open = false"
             class="cursor-pointer rounded-md object-cover shadow-sm transition-transform hover:scale-105"
             style="width: 50px; height: 50px;"
             alt="Thumbnail">

        <template x-teleport="body">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="fixed z-[1000] pointer-events-none"
                 :style="`top: ${top}px; left: ${left}px;`"
                 style="display: none;">
                <img src="{{ $url }}"
                     class="rounded-lg shadow-2xl border-2 border-white/20 max-w-[350px] max-h-[350px] object-contain bg-gray-900"
                     alt="Preview">
            </div>
        </template>
    </div>
@endif
