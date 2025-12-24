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
                    @if ($plan['status']) <!-- Only active plans -->
                        <div class="relative rounded-2xl ring-1 shadow-sm hover:shadow-md transition p-6 flex flex-col
                            {{ $plan['is_popular'] ? 'bg-[#0F7B71] text-white ring-2 ring-[#0F7B71]' : 'bg-white text-gray-900 ring-gray-200' }}">
                            
                            <!-- Header -->
                            <div class="mb-5">
                                <div class="rounded-xl px-4 py-3 text-center
                                    {{ $plan['is_popular'] ? 'bg-[#0FAF95] text-white' : 'bg-[#0F7B71] text-white' }}">
                                    <div class="text-sm font-bold uppercase tracking-wide">
                                        {{ $plan['name'] }}
                                    </div>
                                    @if (!$plan['is_popular'])
                                        <a href="{{ route('contact') }}"
                                           class="text-xs underline underline-offset-2 opacity-90 hover:opacity-100">
                                            <!-- 30 days free trial -->
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-5 text-center">
                                <span class="text-4xl font-extrabold {{ $plan['is_popular'] ? 'text-white' : 'text-gray-900' }}">
                                    {{ (int)$plan['package_price'] }} <!-- Decimal → Integer -->
                                </span>
                                <span class="text-sm {{ $plan['is_popular'] ? 'text-white/80' : 'text-gray-500' }}">
                                    /package
                                </span>
                            </div>

                            <!-- Features -->
                           <ul class="text-sm space-y-2 mb-6 {{ $plan['is_popular'] ? 'text-white/70' : 'text-gray-700' }}">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full
                                        {{ $plan['is_popular'] ? 'bg-white/80 text-[#0F7B71]/80' : 'bg-[#D6F8E8] text-[#0F7B71]' }}">
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
                               class="mt-auto inline-flex items-center justify-center rounded-lg border px-5 py-2.5 font-semibold shadow
                                {{ $plan['is_popular'] ? 'border-white bg-white text-[#0F7B71] hover:bg-white/90 hover:text-[#0F7B71]' : 'border-[#0F7B71] bg-white text-[#0F7B71] hover:bg-[#0F7B71] hover:text-white' }}">
                                Get Started
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </section>
</div>
