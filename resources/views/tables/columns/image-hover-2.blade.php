@php
    $url = $record->image
        ? \Illuminate\Support\Facades\Storage::disk('s3-private')->temporaryUrl(
            $record->image->url,
            now()->addMinutes(10),
        )
        : null;
@endphp

@if ($url)
    <div style="display: inline-block;">
        <img src="{{ $url }}" class="hover-trigger-{{ $record->id }}"
            style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; cursor: pointer; display: block;"
            alt="Thumbnail">

        <script>
            (function() {
                const trigger = document.querySelector('.hover-trigger-{{ $record->id }}');
                let popup = document.getElementById('popup-{{ $record->id }}');

                if (!popup) {
                    popup = document.createElement('div');
                    popup.id = 'popup-{{ $record->id }}';
                    /* CHANGES: 
                       1. Removed translateX(-100%) so it flows to the right.
                       2. Changed margin-left to positive 15px to create a gap.
                       3. Kept translateY(-25%) to keep it vertically aligned.
                    */
                    popup.style.cssText =
                        'position: fixed; z-index: 999999; display: none; pointer-events: none; transform: translateY(-25%); margin-left: 75px;';

                    popup.innerHTML =
                        `<img src="{{ $url }}" style="max-height: 40vh; width: auto; max-width: 250px; border-radius: 8px; box-shadow: 0 15px 30px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.3); background: #000;">`;
                    document.body.appendChild(popup);
                }

                trigger.addEventListener('mouseenter', () => {
                    const rect = trigger.getBoundingClientRect();
                    popup.style.top = rect.top + 'px';
                    popup.style.left = rect.left + 'px';
                    popup.style.display = 'block';
                });

                trigger.addEventListener('mouseleave', () => {
                    popup.style.display = 'none';
                });
            })();
        </script>
    </div>
@endif
