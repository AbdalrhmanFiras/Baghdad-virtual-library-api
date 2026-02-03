@if ($imageUrl)
    <img src="{{ $imageUrl }}" alt="Cover Image" style="max-width: 250px; border-radius: 8px; border: 1px solid #ccc;">
@else
    <span style="color: #888;">لا توجد صورة</span>
@endif
