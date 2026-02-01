@php
    $rating = $record->rating;
    $fullStars = floor($rating);
    $partial = $rating - $fullStars;
    $emptyStars = 5 - ceil($rating);
@endphp

<div class="flex items-center">
    {{-- النجوم الخلفية الرمادية --}}
    <div class="relative flex">
        @for ($i = 0; $i < 5; $i++)
            <span class="text-gray-300 text-lg">★</span>
        @endfor

        {{-- النجوم الصفراء حسب التقييم --}}
        <div class="absolute top-0 left-0 flex overflow-hidden" style="width: {{ ($rating / 5) * 100 }}%">
            @for ($i = 0; $i < 5; $i++)
                <span class="text-yellow-400 text-lg">★</span>
            @endfor
        </div>
    </div>
    <span class="ml-2 text-sm text-gray-600">({{ $rating }})</span>
</div>
