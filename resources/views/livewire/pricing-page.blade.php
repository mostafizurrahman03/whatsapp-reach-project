<div>
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4" x-data="{
                period: 'monthly',
                prices: {
                    basic: { monthly: 19, yearly: 190 },
                    standard: { monthly: 49, yearly: 490 },
                    pro: { monthly: 99, yearly: 990 },
                }
            }">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Pricing Plans</h1>
                <p class="text-gray-600 mt-3">Choose a plan that fits your business needs.</p>
            </div>

            <!-- Billing Toggle -->
            <div class="mt-6 flex items-center justify-center gap-2">
                <button type="button"
                        @click="period = 'monthly'"
                        :class="period === 'monthly' ? 'bg-[#0F7B71] text-white' : 'bg-white text-gray-700'"
                        class="px-4 py-2 rounded-lg border border-gray-200 shadow-sm hover:shadow transition text-sm font-semibold">
                    Monthly
                </button>
                <button type="button"
                        @click="period = 'yearly'"
                        :class="period === 'yearly' ? 'bg-[#0F7B71] text-white' : 'bg-white text-gray-700'"
                        class="px-4 py-2 rounded-lg border border-gray-200 shadow-sm hover:shadow transition text-sm font-semibold">
                    Yearly
                </button>
                <span class="ml-3 text-xs px-2 py-1 rounded-full bg-[#0F7B71]/10 text-[#0F7B71] font-semibold" x-show="period === 'yearly'">Save 2 months</span>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <!-- Basic -->
                <div class="relative rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm hover:shadow-md transition p-6 flex flex-col">
                    <!-- Top header -->
                    <div class="mb-5">
                        <div class="rounded-xl bg-[#0F7B71] text-white text-center px-4 py-3">
                            <div class="text-sm font-bold uppercase tracking-wide">Basic</div>
                            <a href="{{ route('contact') }}" class="text-xs underline underline-offset-2 opacity-90 hover:opacity-100">30 days free trial</a>
                        </div>
                    </div>
                    <div class="mb-5 text-center">
                        <span class="text-4xl font-extrabold text-gray-900" x-text="period === 'monthly' ? prices.basic.monthly : prices.basic.yearly"></span>
                        <span class="text-sm text-gray-500" x-text="period === 'monthly' ? '/month' : '/year'"></span>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-2 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#0F7B71]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Send messages
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#0F7B71]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Connect device
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#0F7B71]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Bulk send
                        </li>
                    </ul>
                    <a href="{{ route('filament.user.auth.register') }}" class="mt-auto inline-flex items-center justify-center rounded-lg border border-[#0F7B71] text-[#0F7B71] bg-white px-5 py-2.5 font-semibold shadow hover:bg-[#0F7B71] hover:text-white transition">Get Started</a>
                </div>

                <!-- Standard (Most Popular) -->
                <div class="relative rounded-2xl bg-white ring-2 ring-[#0F7B71] shadow-md transition p-6 flex flex-col">
                    <!-- Top header -->
                    <div class="mb-5">
                        <div class="rounded-xl bg-[#0F7B71] text-white text-center px-4 py-3">
                            <div class="text-sm font-bold uppercase tracking-wide">Standard</div>
                            <a href="{{ route('contact') }}" class="text-xs underline underline-offset-2 opacity-90 hover:opacity-100">30 days free trial</a>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">For growing teams</p>
                    <div class="mb-5 text-center">
                        <span class="text-4xl font-extrabold text-gray-900" x-text="period === 'monthly' ? prices.standard.monthly : prices.standard.yearly"></span>
                        <span class="text-sm text-gray-500" x-text="period === 'monthly' ? '/month' : '/year'"></span>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-2 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#128C7E]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Everything in Basic
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#128C7E]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Broadcast to segmented lists
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#128C7E]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Templates & scheduling
                        </li>
                    </ul>
                    <a href="{{ route('filament.user.auth.register') }}" class="mt-auto inline-flex items-center justify-center rounded-lg border border-[#0F7B71] text-[#0F7B71] bg-white px-5 py-2.5 font-semibold shadow hover:bg-[#0F7B71] hover:text-white transition">Get Started</a>
                </div>

                <!-- Pro -->
                <div class="relative rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm hover:shadow-md transition p-6 flex flex-col">
                    <!-- Top header -->
                    <div class="mb-5">
                        <div class="rounded-xl bg-[#0F7B71] text-white text-center px-4 py-3">
                            <div class="text-sm font-bold uppercase tracking-wide">Pro</div>
                            <a href="{{ route('contact') }}" class="text-xs underline underline-offset-2 opacity-90 hover:opacity-100">30 days free trial</a>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">For enterprises</p>
                    <div class="mb-5 text-center">
                        <span class="text-4xl font-extrabold text-gray-900" x-text="period === 'monthly' ? prices.pro.monthly : prices.pro.yearly"></span>
                        <span class="text-sm text-gray-500" x-text="period === 'monthly' ? '/month' : '/year'"></span>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-2 mb-6">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#128C7E]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Everything in Standard
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#128C7E]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Priority support
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#128C7E]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            Advanced automation
                        </li>
                    </ul>
                    <a href="{{ route('filament.user.auth.register') }}" class="mt-auto inline-flex items-center justify-center rounded-lg border border-[#0F7B71] text-[#0F7B71] bg-white px-5 py-2.5 font-semibold shadow hover:bg-[#0F7B71] hover:text-white transition">Get Started</a>
                </div>
            </div>
        </div>
    </section>
</div>
