<div class="py-1">
    <img src="{{ Storage::disk('s3-private')->temporaryUrl($record->image->url, now()->addMinutes(5)) }}"
        class="h-6 w-6 object-cover rounded" />
</div>
