@props(['items' => null])

@php
    // Default ReachWave solutions
    $items = $items ?? [
        [
            'title' => 'WhatsApp Messaging',
            'desc'  => 'Send one-to-one and bulk WhatsApp messages using approved templates, personalization and delivery reports.',
            'icon'  => 'chat',
        ],
        [
            'title' => 'Bulk SMS & Voice',
            'desc'  => 'Run high-volume SMS (masking & non-masking) and voice broadcasting campaigns with full control.',
            'icon'  => 'bulk',
        ],
        [
            'title' => 'Automation & Media',
            'desc'  => 'Automate replies, schedule campaigns and send images, PDFs, videos and documents seamlessly.',
            'icon'  => 'attachment',
        ],
    ];
@endphp

<section id="solutions" class="py-14 bg-gradient-to-b from-white to-[#0F7B71]/5">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Header --}}
        <div class="mb-12 text-center">
            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-[#0F7B71]/10 text-[#0F7B71] text-xs font-semibold ring-1 ring-[#0F7B71]/20">
                ReachWave Platform
            </span>
            <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                One Platform. All Channels.
            </h2>
            <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-2xl mx-auto">
                ReachWave helps businesses communicate at scale using WhatsApp, SMS, voice and automation — fast, secure and reliable.
            </p>
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                <div class="group rounded-2xl border border-gray-200/70 bg-white p-6 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-start gap-4">

                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            @switch($item['icon'])
                                @case('chat')
                                    <div class="bg-[#0F7B71]/10 p-3 rounded-xl ring-1 ring-[#0F7B71]/15">
                                        <svg class="w-8 h-8 text-[#0F7B71]" viewBox="0 0 24 24" fill="none">
                                            <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </div>
                                    @break

                                @case('bulk')
                                    <div class="bg-[#0F7B71]/10 p-3 rounded-xl ring-1 ring-[#0F7B71]/15">
                                        <svg class="w-8 h-8 text-[#0F7B71]" viewBox="0 0 24 24" fill="none">
                                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M7 8h10M7 12h10M7 16h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    @break

                                @case('attachment')
                                    <div class="bg-[#0F7B71]/10 p-3 rounded-xl ring-1 ring-[#0F7B71]/15">
                                        <svg class="w-8 h-8 text-[#0F7B71]" viewBox="0 0 24 24" fill="none">
                                            <path d="M21 12l-9 9a5 5 0 01-7-7l9-9a3 3 0 014 4l-9 9a1 1 0 01-2-2l8-8"
                                                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    @break
                            @endswitch
                        </div>

                        {{-- Content --}}
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[#0F7B71] transition-colors">
                                {{ $item['title'] }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ $item['desc'] }}
                            </p>

                            <a href="#"
                               class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#0F7B71] hover:gap-2.5 transition-all">
                                Learn more
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
