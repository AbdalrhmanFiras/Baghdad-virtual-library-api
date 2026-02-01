@php
    use Illuminate\Support\Facades\Storage;
    $url = $record->image ? Storage::disk('s3-private')->temporaryUrl($record->image->url, now()->addMinutes(5)) : null;
@endphp

@if ($url)
    <div style="position: relative; display: inline-block;" class="group">
        <!-- Thumbnail (Always Small) -->
        <img src="{{ $url }}"
            style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; display: block; margin-top: 5px; margin-bottom: 5px;"
            alt="Cover">

        <!-- Hover Popup (Hidden by default, shown on hover) -->
        <!-- Using inline JS to ensure it works even if Tailwind group-hover fails -->
        <div style="position: absolute; top: 0; right: 100%; margin-right: 10px; z-index: 9999; display: none; width: max-content; pointer-events: none;"
            class="hover-popup">
            <img src="{{ $url }}"
                style="height: 200px; width: auto; max-width: 300px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);"
                alt="Cover Full">
        </div>

        <script>
            // Simple inline script to handle hover if CSS fails or for robustness
            document.currentScript.parentElement.addEventListener('mouseenter', function() {
                this.querySelector('.hover-popup').style.display = 'block';
            });
            document.currentScript.parentElement.addEventListener('mouseleave', function() {
                this.querySelector('.hover-popup').style.display = 'none';
            });
        </script>
    </div>
@endif
