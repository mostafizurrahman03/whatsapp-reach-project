<div>
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">

            <!-- Heading -->
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">
                    Pricing Packages
                </h1>
                <p class="text-gray-600 mt-3">
                    Choose a package that fits your business needs.
                </p>
            </div>

            <!-- Pricing Cards -->
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($plans as $plan)
                    <div class="relative rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm hover:shadow-md transition p-6 flex flex-col">

                        <!-- Header -->
                        <div class="mb-5">
                            <div class="rounded-xl bg-[#0F7B71] text-white text-center px-4 py-3">
                                <div class="text-sm font-bold uppercase tracking-wide">
                                    {{ $plan['name'] }}
                                </div>
                                <a href="{{ route('contact') }}"
                                   class="text-xs underline underline-offset-2 opacity-90 hover:opacity-100">
                                    <!-- 30 days free trial -->
                                </a>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mb-5 text-center">
                            <span class="text-4xl font-extrabold text-gray-900">
                                {{ $plan['monthly_price'] }}
                            </span>
                            <span class="text-sm text-gray-500">
                                /package
                            </span>
                        </div>

                        <!-- Features -->
                        <ul class="text-sm text-gray-700 space-y-2 mb-6">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#D6F8E8] text-[#0F7B71]">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-3.5 w-3.5"
                                             viewBox="0 0 20 20"
                                             fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                    {{ $feature['feature'] }}
                                </li>
                            @endforeach
                        </ul>

                        <!-- Button -->
                        <a href="{{ route('filament.user.auth.register') }}"
                           class="mt-auto inline-flex items-center justify-center rounded-lg border border-[#0F7B71] text-[#0F7B71] bg-white px-5 py-2.5 font-semibold shadow hover:bg-[#0F7B71] hover:text-white transition">
                            Get Started
                        </a>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
</div>







