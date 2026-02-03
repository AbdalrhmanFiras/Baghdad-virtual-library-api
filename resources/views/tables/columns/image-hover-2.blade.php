@php
    use Illuminate\Support\Facades\Storage;
    $url = $record->image ? Storage::disk('s3-private')->temporaryUrl($record->image->url, now()->addMinutes(5)) : null;
@endphp
@if ($url)
    <div style="display: inline-block;">
        <img src="{{ $url }}" class="hover-trigger-{{ $record->id }}"
            style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; display: block; margin: 5px 0;"
            alt="Thumbnail">

        <div id="popup-{{ $record->id }}"
            style="position: fixed; z-index: 999999; display: none; pointer-events: none;">
            <img src="{{ $url }}"
                style="height: 200px; width: auto; border-radius: 8px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); border: 1px solid rgba(255,255,255,0.1);"
                alt="Preview">
        </div>

        <script>
            (function() {
                const trigger = document.querySelector('.hover-trigger-{{ $record->id }}');
                const popup = document.getElementById('popup-{{ $record->id }}');

                trigger.addEventListener('mouseenter', () => {
                    // Get the exact position of the thumbnail on the screen
                    const rect = trigger.getBoundingClientRect();

                    // Position the popup to the right of the thumbnail
                    popup.style.top = rect.top + 'px';
                    popup.style.left = (rect.right + 15) + 'px';
                    popup.style.display = 'block';
                });

                trigger.addEventListener('mouseleave', () => {
                    popup.style.display = 'none';
                });
            })();
        </script>
    </div>
@endif
