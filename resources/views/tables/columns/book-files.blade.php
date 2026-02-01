@php
    use Illuminate\Support\Facades\Storage;

    // تحدد الأيقونة واللون حسب العمود
    $icon = '';
    $colorClass = 'text-gray-400';
    $url = null;

    if ($column === 'pdf_read') {
        $icon =
            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>'; // heroicon-o-document-text بدل المثال
        if ($record->pdf_read) {
            $colorClass = 'text-blue-500';
            $url = Storage::disk('s3-private')->temporaryUrl($record->pdf_read, now()->addMinutes(5));
        }
    } elseif ($column === 'pdf_download') {
        $icon =
            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-6-4v6m0 0l-3-3m3 3l3-3"/></svg>'; // heroicon-o-arrow-down-tray
        if ($record->pdf_download) {
            $colorClass = 'text-green-500';
            $url = Storage::disk('s3-private')->temporaryUrl($record->pdf_download, now()->addMinutes(5));
        }
    }
@endphp

@if ($url)
    <a href="{{ $url }}" target="_blank" class="{{ $colorClass }}">
        {!! $icon !!}
    </a>
@else
    <span class="text-gray-300">{!! $icon !!}</span>
@endif
