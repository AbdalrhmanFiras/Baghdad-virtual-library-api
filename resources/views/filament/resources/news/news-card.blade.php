<div class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
    @if($getRecord()->image)
        <div class="relative h-32 overflow-hidden">
            <img 
                src="{{ $getRecord()->image_url }}" 
                alt="{{ $getRecord()->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        </div>
    @endif
    
    <div class="p-3">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1.5 line-clamp-2 leading-tight">
            {{ $getRecord()->title }}
        </h3>
        
        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-2 leading-relaxed">
            {!! Str::limit(strip_tags($getRecord()->description ?? ''), 100) !!}
        </p>
        
        <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
            <span class="text-xs text-gray-500 dark:text-gray-500 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $getRecord()->created_at?->diffForHumans() }}
            </span>
        </div>
    </div>
</div>
