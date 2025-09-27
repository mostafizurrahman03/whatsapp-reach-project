<div class="relative overflow-hidden">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-green-50 via-white to-emerald-50">
        <div class="max-w-7xl mx-auto px-6 py-16 sm:py-24 grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Whats-Reach</span>
                <h1 class="mt-4 text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900">
                    WhatsApp Bulk Messaging<br>
                    <span class="text-green-600">Fast. Reliable. Scalable.</span>
                </h1>
                <p class="mt-5 text-lg text-gray-600 max-w-xl">
                    Reach thousands on WhatsApp with personalized campaigns, automation, and real-time insights — all in one place.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('features') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold shadow">
                        Get Started
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-gray-300 hover:border-gray-400 text-gray-800 font-semibold bg-white">
                        Talk to Sales
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l.8-5A8.98 8.98 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </a>
                </div>
                <div class="mt-8 flex items-center gap-6 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-green-500"></span> Trusted delivery
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span> GDPR-friendly
                    </div>
                </div>
            </div>
            <!-- Mock UI Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center justify-between pb-4 border-b">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-green-600 text-white flex items-center justify-center font-bold">WR</div>
                        <div>
                            <div class="font-semibold">Campaign: Promo Week</div>
                            <div class="text-xs text-gray-500">Scheduled · 10:00 AM</div>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Active</span>
                </div>
                <div class="grid sm:grid-cols-3 text-center divide-y sm:divide-y-0 sm:divide-x mt-4">
                    <div class="py-3">
                        <div class="text-2xl font-extrabold text-gray-900">12.4k</div>
                        <div class="text-xs text-gray-500">Sent</div>
                    </div>
                    <div class="py-3">
                        <div class="text-2xl font-extrabold text-green-600">96%</div>
                        <div class="text-xs text-gray-500">Delivered</div>
                    </div>
                    <div class="py-3">
                        <div class="text-2xl font-extrabold text-emerald-600">82%</div>
                        <div class="text-xs text-gray-500">Read</div>
                    </div>
                </div>
                <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                    “Personalized offers boosted our response rate. Setup was simple and delivery was instant.”
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Features -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-2xl mb-3">💬</div>
                <h3 class="font-semibold mb-1">Bulk Messaging</h3>
                <p class="text-gray-600 text-sm">Send thousands with CSV import, variables, and media.</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-2xl mb-3">⚡</div>
                <h3 class="font-semibold mb-1">Automation</h3>
                <p class="text-gray-600 text-sm">Schedule flows, auto-replies, and reminders.</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-2xl mb-3">📊</div>
                <h3 class="font-semibold mb-1">Analytics</h3>
                <p class="text-gray-600 text-sm">Track delivery, reads, and campaign performance.</p>
            </div>
        </div>
    </section>
    <x-benefits-section />
    <x-customers-love />
    <x-faq />
</div>
