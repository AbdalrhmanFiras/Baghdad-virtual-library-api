@php
    $url = $record->image 
        ? \Illuminate\Support\Facades\Storage::disk('s3-private')->temporaryUrl($record->image->url, now()->addMinutes(10)) 
        : null;
@endphp

@if ($url)
    <div x-data="{ open: false, x: 0, y: 0 }" style="display: inline-block;">
        <img src="{{ $url }}"
            @mousemove="
                x = $event.clientX; 
                y = $event.clientY; 
                open = true;
            "
            @mouseleave="open = false"
            style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; cursor: pointer;" 
            alt="Thumbnail">

        <template x-teleport="body">
            <div x-show="open" 
                 x-cloak
                 x-transition.opacity
                 style="position: fixed; z-index: 999999; pointer-events: none; display: none;"
                 :style="`top: ${y}px; left: ${x - 20}px; transform: translate(-100%, -50%);`"
            >
                <img src="{{ $url }}"
                    style="max-height: 80vh; width: auto; max-width: 500px; object-fit: contain; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.2); background: #000;"
                    alt="Preview">
            </div>
        </template>
    </div>
@endif
