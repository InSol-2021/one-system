<div class="fixed inset-0 bg-white bg-opacity-90 z-50 flex items-center justify-center">
    <div class="flex flex-col items-center space-y-4">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Loading' }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $slot ?? 'Please wait while we load your data...' }}</p>
        </div>
    </div>
</div>
