<div class="py-20 max-w-6xl mx-auto px-6">

    <!-- Header -->
    <h1 class="text-4xl md:text-5xl font-extrabold text-center mb-3">
        <span class="text-gray-900">Whats-Reach</span> 
        <span class="text-[#0F7B71]">Features</span>
    </h1>

    <p class="text-center text-gray-600 max-w-2xl mx-auto mb-12">
        WhatsApp bulk SMS sending service — fast, reliable, and scalable for your campaigns.
    </p>

    <!-- Feature Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach ($features as $feature)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-100 p-6">

                <!-- Icon -->
                <div class="w-12 h-12 rounded-lg bg-[#0F7B71]/5 text-[#0F7B71] 
                    flex items-center justify-center text-2xl mb-4">

                    @php
                        // Dynamic icon, default 💬
                        $icon = $feature->icon ?? '💬';
                    @endphp

                    @if(str_starts_with($icon, 'heroicon') || str_contains($icon, 'fa-'))
                        <!-- Class-based icon -->
                        <i class="{{ $icon }}" aria-hidden="true"></i>
                    @else
                        <!-- Emoji or plain text -->
                        <span class="text-2xl">{{ $icon }}</span>
                    @endif

                </div>

                <!-- Title -->
                <h2 class="text-lg font-semibold mb-2">{{ $feature->title }}</h2>

                <!-- Short Description -->
                <p class="text-gray-600 mb-3">{{ $feature->short_description }}</p>

                <!-- Dynamic Bullet Points -->
                @if(is_array($feature->items) && count($feature->items))
                    <ul class="text-sm text-gray-600 space-y-1">
                        @foreach ($feature->items as $item)
                            <li class="flex items-start gap-2">
                                <span class="text-[#0F7B71]">✔</span> {{ $item['value'] ?? '' }}
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        @endforeach

    </div>

    <!-- CTA Button -->
    <div class="text-center mt-12">
        <a href="#"
           class="inline-flex items-center gap-2 px-6 py-3 bg-[#0F7B71] hover:bg-[#0F7B71]/90 
                  text-white font-medium rounded-lg shadow transition-colors">
            Try Whats-Reach Now
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>

</div>
