<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WhatsApp Automation' }}</title>
    @vite('resources/css/app.css') {{-- Tailwind --}}
    @livewireStyles
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">

<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#0F7B71] text-white font-bold">WR</span>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900 group-hover:text-[#0F7B71] transition-colors">Whats-Reach</span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden sm:flex items-center gap-6">
                <ul class="flex items-center gap-6 text-lg font-medium">
                    <li>
                        <a href="{{ route('whats-app-integration') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('whats-app-integration') ? 'text-[#0F7B71]' : 'text-gray-700 hover:text-[#0F7B71]' }}">Whatsapp Integration</a>
                    </li>
                    <li>
                        <a href="{{ route('features') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('features') ? 'text-[#0F7B71]' : 'text-gray-700 hover:text-[#0F7B71]' }}">Features</a>
                    </li>
                    <li>
                        <a href="{{ route('pricing') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('pricing') ? 'text-[#0F7B71]' : 'text-gray-700 hover:text-[#0F7B71]' }}">Pricing</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('contact') ? 'text-[#0F7B71]' : 'text-gray-700 hover:text-[#0F7B71]' }}">Contact Us</a>
                    </li>
                </ul>
                <a href="{{ route('filament.user.auth.register') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0F7B71] px-3.5 py-2 text-white text-lg font-semibold shadow hover:bg-[#0F7B71]/90 transition-colors">
                    Get Started
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <!-- Mobile Menu (pure HTML details/summary) -->
            <details class="sm:hidden relative">
                <summary class="list-none inline-flex items-center justify-center h-10 w-10 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </summary>
                <div class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg py-2">
                    <a href="{{ route('features') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('features') ? 'text-[#0F7B71]' : 'text-gray-700 hover:bg-gray-50' }}">Features</a>
                    <a href="{{ route('pricing') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('pricing') ? 'text-[#0F7B71]' : 'text-gray-700 hover:bg-gray-50' }}">Pricing</a>
                    <a href="{{ route('contact') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('contact') ? 'text-[#0F7B71]' : 'text-gray-700 hover:bg-gray-50' }}">Contact</a>
                    <div class="px-4 pt-2">
                        <a href="{{ route('features') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-[#0F7B71] px-3 py-2 text-white text-sm font-semibold hover:bg-[#0F7B71]/90">Start</a>
                    </div>
                </div>
            </details>
        </div>
    </div>
</nav>

<main>
    {{ $slot }}
</main>



<footer class="bg-gray-900 text-gray-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#0F7B71] text-white font-bold text-md">WR</span>
                    <span class="font-extrabold text-xl text-white">Whats-Reach</span>
                </div>
                <p class="text-sm text-gray-400">WhatsApp bulk messaging made simple, reliable, and scalable for modern teams.</p>
                <!-- Socials -->
                <div class="mt-4">
                    <h4 class="text-xs font-semibold text-white/80 mb-2 hover:text-white">Follow us</h4>
                    <div class="flex items-center gap-3">
                        <a href="#" class="text-gray-400 hover:text-white" aria-label="Twitter">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.633 7.997c.013.18.013.36.013.54 0 5.518-4.2 11.875-11.875 11.875A11.8 11.8 0 0 1 2 19.61c.257.03.5.04.77.04a8.38 8.38 0 0 0 5.2-1.79 4.19 4.19 0 0 1-3.91-2.9c.26.04.52.07.79.07.38 0 .77-.05 1.12-.15A4.18 4.18 0 0 1 3.3 11.7v-.05c.56.31 1.2.5 1.88.52a4.18 4.18 0 0 1-1.87-3.48c0-.77.2-1.47.56-2.08a11.9 11.9 0 0 0 8.63 4.37 4.72 4.72 0 0 1-.1-.96 4.18 4.18 0 0 1 7.24-2.86 8.23 8.23 0 0 0 2.65-1.01 4.2 4.2 0 0 1-1.84 2.31 8.37 8.37 0 0 0 2.4-.66 8.98 8.98 0 0 1-2.12 2.19Z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white" aria-label="Facebook">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.9v-7h-2.2V12h2.2V9.8c0-2.18 1.3-3.38 3.3-3.38.96 0 1.97.17 1.97.17v2.17h-1.11c-1.1 0-1.44.68-1.44 1.37V12h2.45l-.39 2.9h-2.06v7A10 10 0 0 0 22 12"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white" aria-label="LinkedIn">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 6.5A2.44 2.44 0 1 1 2.06 6.5a2.44 2.44 0 0 1 4.88 0ZM2.38 21.5h4.75V8.81H2.38V21.5ZM9.12 8.81V21.5h4.75v-6.35c0-1.68.32-3.3 2.4-3.3 2.05 0 2.08 1.92 2.08 3.41v6.24H23V14.6c0-3.4-.73-6.02-4.7-6.02-1.91 0-3.19 1.05-3.72 2.05h-.05V8.81H9.12Z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white" aria-label="GitHub">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.48 2 2 6.58 2 12.26c0 4.52 2.87 8.35 6.84 9.71.5.1.68-.22.68-.5v-1.93c-2.78.62-3.37-1.2-3.37-1.2-.46-1.2-1.12-1.53-1.12-1.53-.92-.64.07-.62.07-.62 1.02.07 1.56 1.07 1.56 1.07.9 1.58 2.36 1.13 2.94.87.09-.67.35-1.13.63-1.39-2.22-.26-4.55-1.14-4.55-5.08 0-1.13.39-2.06 1.03-2.78-.1-.26-.45-1.32.1-2.75 0 0 .85-.28 2.8 1.06A9.42 9.42 0 0 1 12 7.06c.86 0 1.73.12 2.54.35 1.95-1.34 2.8-1.06 2.8-1.06.55 1.43.2 2.49.1 2.75.64.72 1.03 1.65 1.03 2.78 0 3.95-2.34 4.81-4.57 5.07.36.32.68.93.68 1.88V21.5c0 .28.18.61.69.5A10.26 10.26 0 0 0 22 12.26C22 6.58 17.52 2 12 2Z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white" aria-label="YouTube">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 7.2s-.23-1.64-.94-2.36c-.9-.94-1.92-.95-2.38-1C16.92 3.5 12 3.5 12 3.5h-.01s-4.92 0-8.17.34c-.46.06-1.47.06-2.38 1C.73 5.56.5 7.2.5 7.2S.27 9.13.27 11.06v1.86c0 1.93.23 3.86.23 3.86s.23 1.64.94 2.36c.91.94 2.1.91 2.63 1.02 1.9.18 8 .34 8 .34s4.92 0 8.17-.34c.46-.06 1.48-.06 2.38-1 .71-.72.94-2.36.94-2.36s.23-1.93.23-3.86v-1.86c0-1.93-.23-3.86-.23-3.86ZM9.75 14.5v-6l6 3-6 3Z"/></svg>
                        </a>
                        <a href="https://wa.me/1234567890" target="_blank" rel="noopener" class="text-gray-400 hover:text-white" aria-label="WhatsApp">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.94 11.94 0 0 0 12.06 0C5.56 0 .29 5.27.29 11.77c0 2.07.54 4.06 1.58 5.82L0 24l6.59-1.72a11.74 11.74 0 0 0 5.47 1.39h.01c6.5 0 11.77-5.27 11.77-11.77 0-3.14-1.22-6.09-3.32-8.18ZM12.07 21.3h-.01a9.5 9.5 0 0 1-4.84-1.33l-.35-.21-3.91 1.02 1.05-3.81-.23-.39a9.5 9.5 0 0 1-1.45-5.06c0-5.25 4.27-9.52 9.52-9.52 2.54 0 4.93.99 6.72 2.78a9.45 9.45 0 0 1 2.79 6.74c0 5.25-4.27 9.52-9.52 9.52Zm5.49-7.14c-.3-.15-1.78-.88-2.05-.98-.27-.1-.47-.15-.68.15-.2.3-.78.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.46-.88-.78-1.47-1.74-1.64-2.04-.17-.3-.02-.47.13-.62.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.68-1.65-.93-2.27-.24-.58-.48-.5-.68-.5-.17 0-.37 0-.57 0s-.52.07-.8.37c-.27.3-1.05 1.03-1.05 2.51s1.07 2.92 1.22 3.12c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.64.71.23 1.36.2 1.87.12.57-.08 1.78-.72 2.03-1.41.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35Z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Product -->
            <div>
                <h3 class="text-sm font-semibold text-white mb-3 text-xl">Product</h3>
                <ul class="space-y-2 text-sm">
                    <li><a class="relative transition duration-300 hover:text-[#6BBFA9] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-[#0F7B71] after:rounded-full hover:after:w-full after:transition-all after:duration-300"
                    href="{{ route('features') }}">Features</a></li>
                    <li><a class="relative transition duration-300 hover:text-[#6BBFA9] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-[#0F7B71] after:rounded-full hover:after:w-full after:transition-all after:duration-300" href="{{ route('pricing') }}">Pricing</a></li>
                    <li><a class="relative transition duration-300 hover:text-[#6BBFA9] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-[#0F7B71] after:rounded-full hover:after:w-full after:transition-all after:duration-300" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <!-- Resources -->
            <div>
                <h3 class="text-sm font-semibold text-white mb-3 text-xl">Resources</h3>
                <ul class="space-y-2 text-sm">
                    <li><a class="relative transition duration-300 hover:text-[#6BBFA9] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-[#0F7B71] after:rounded-full hover:after:w-full after:transition-all after:duration-300" href="#">Docs</a></li>
                    <li><a class="relative transition duration-300 hover:text-[#6BBFA9] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-[#0F7B71] after:rounded-full hover:after:w-full after:transition-all after:duration-300" href="#">Guides</a></li>
                    <li><a class="relative transition duration-300 hover:text-[#6BBFA9] after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-[2px] after:bg-[#0F7B71] after:rounded-full hover:after:w-full after:transition-all after:duration-300" href="#">Support</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div>
                <h3 class="text-sm font-semibold text-white mb-3 text-xl">Get in touch</h3>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white" href="mailto:support@example.com">support@example.com</a></li>
                    <li><a class="hover:text-white" href="https://wa.me/1234567890" target="_blank" rel="noopener">WhatsApp</a></li>
                    <li><span class="hover:text-white">Dhaka, Bangladesh</span></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 border-t border-white/10 pt-6 text-sm text-gray-400 flex flex-col sm:flex-row items-center justify-between relative">
            <p class="text-center sm:absolute sm:left-1/2 sm:-translate-x-1/2">&copy; {{ date('Y') }} Whats-Reach. All rights reserved.</p>
            <div class="flex gap-4 mt-3 sm:mt-0 sm:ml-auto">
                <a href="#" class="hover:text-white">Privacy</a>
                <a href="#" class="hover:text-white">Terms</a>
            </div>
        </div>
    </div>
</footer>
@stack('scripts')

@livewireScripts
</body>
</html>
