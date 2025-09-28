@props(['items' => null])

@php
    // Default items (you can override by passing :items)
    $items = $items ?? [
        [
            'title' => 'WhatsApp Single Messaging',
            'desc'  => 'Send one-to-one messages with personalization, quick replies and delivery insights.',
            'icon'  => 'chat',
        ],
        [
            'title' => 'Bulk Campaigns',
            'desc'  => 'Upload CSV, personalize with variables and schedule thousands of messages at scale.',
            'icon'  => 'bulk',
        ],
        [
            'title' => 'Media & File Attachments',
            'desc'  => 'Share images, PDFs, videos and more with reliable delivery and clean previews.',
            'icon'  => 'attachment',
        ],
    ];
@endphp

<div class="max-w-6xl mx-auto p-6 bg-white/5">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-[#0F7B71]/10 text-[#0F7B71] text-xs font-semibold">Our Solutions</span>
            <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">Solutions</h2>
            <p class="text-sm text-gray-500 mt-1">Choose a solution that fits your organisation — from startups to enterprises.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <a href="#" class="text-sm px-4 py-2 rounded-md border border-[#0F7B71]/30 text-[#0F7B71] hover:bg-[#0F7B71]/10 transition-colors">Explore all</a>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach($items as $item)
            <div class="group flex items-start gap-4 p-3 sm:p-2">
                    <div class="flex-shrink-0">
                        {{-- Simple icon switch (inline SVG) --}}
                        @switch($item["icon"] ?? 'briefcase')
                            @case('chat')
                                <div class="bg-[#0F7B71]/10 p-2 rounded-lg">
                                    <svg class="w-10 h-10 text-[#0F7B71]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                @break
                            @case('chip')
                                <svg class="w-10 h-10 p-2 bg-[#0F7B71]/10 text-[#0F7B71] rounded-lg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M7 7h10v10H7z" stroke="currentColor" stroke-width="1.2"/>
                                </svg>
                                @break
                            @default
                                <svg class="w-10 h-10 p-2 bg-[#0F7B71]/10 text-[#0F7B71] rounded-lg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6H4v12h16V6z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M8 10h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                        @endswitch
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[#0F7B71] transition-colors">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-500 mt-2">{{ $item['desc'] }}</p>

                        <div class="mt-3">
                            <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-2 rounded-md text-[#0F7B71] bg-[#0F7B71]/5 hover:bg-[#0F7B71]/10 transition-colors">
                                Learn more
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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

{{-- Usage examples --}}
{{-- 1) Anonymous (default items): --}}
{{-- <x-solutions-card /> --}}

{{-- 2) Pass custom items from controller/view: --}}
{{--
@php
$my = [
    ['title'=>'Custom A','desc'=>'Custom desc A','icon'=>'chip'],
    ['title'=>'Custom B','desc'=>'Custom desc B','icon'=>'building-office'],
    ['title'=>'Custom C','desc'=>'Custom desc C','icon'=>'briefcase'],
];
@endphp
<x-solutions-card :items="$my" />
--}}
