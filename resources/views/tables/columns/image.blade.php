@if ($url)
    <img src="{{ $url }}" alt="Cover Image" style="max-width: 250px; border-radius: 8px; border: 1px solid #ccc;">
@else
    <span style="color: #888;">no image untill now</span>
@endif
