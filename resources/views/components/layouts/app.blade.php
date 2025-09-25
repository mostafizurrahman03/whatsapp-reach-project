<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WhatsApp Automation' }}</title>
    @vite('resources/css/app.css') {{-- Tailwind --}}
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900">

<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-green-600 text-white font-bold">WR</span>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900 group-hover:text-green-700 transition-colors">Whats-Reach</span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden sm:flex items-center gap-6">
                <ul class="flex items-center gap-6 text-lg font-medium">
                    <li>
                        <a href="{{ route('features') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('features') ? 'text-green-700' : 'text-gray-700 hover:text-green-700' }}">Whatsapp Integrations</a>
                    </li>
                    <li>
                        <a href="{{ route('features') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('features') ? 'text-green-700' : 'text-gray-700 hover:text-green-700' }}">Features</a>
                    </li>
                    <li>
                        <a href="{{ route('pricing') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('pricing') ? 'text-green-700' : 'text-gray-700 hover:text-green-700' }}">Pricing</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="px-1.5 py-1 rounded transition-colors {{ request()->routeIs('contact') ? 'text-green-700' : 'text-gray-700 hover:text-green-700' }}">Contact Us</a>
                    </li>
                </ul>
                <a href="{{ route('features') }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3.5 py-2 text-white text-lg font-semibold shadow hover:bg-green-700 transition-colors">
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
                    <a href="{{ route('features') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('features') ? 'text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">Features</a>
                    <a href="{{ route('pricing') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('pricing') ? 'text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">Pricing</a>
                    <a href="{{ route('contact') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('contact') ? 'text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">Contact</a>
                    <div class="px-4 pt-2">
                        <a href="{{ route('features') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-green-600 px-3 py-2 text-white text-sm font-semibold hover:bg-green-700">Start</a>
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
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-green-600 text-white font-bold">WR</span>
                    <span class="font-extrabold text-white">Whats-Reach</span>
                </div>
                <p class="text-sm text-gray-400">WhatsApp bulk messaging made simple, reliable, and scalable for modern teams.</p>
            </div>
            <!-- Product -->
            <div>
                <h3 class="text-sm font-semibold text-white mb-3">Product</h3>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white" href="{{ route('features') }}">Features</a></li>
                    <li><a class="hover:text-white" href="{{ route('pricing') }}">Pricing</a></li>
                    <li><a class="hover:text-white" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <!-- Resources -->
            <div>
                <h3 class="text-sm font-semibold text-white mb-3">Resources</h3>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white" href="#">Docs</a></li>
                    <li><a class="hover:text-white" href="#">Guides</a></li>
                    <li><a class="hover:text-white" href="#">Support</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div>
                <h3 class="text-sm font-semibold text-white mb-3">Get in touch</h3>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white" href="mailto:support@example.com">support@example.com</a></li>
                    <li><a class="hover:text-white" href="https://wa.me/1234567890" target="_blank" rel="noopener">WhatsApp</a></li>
                    <li><span class="text-gray-400">City, Country</span></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 border-t border-white/10 pt-6 text-sm text-gray-400 flex flex-col sm:flex-row items-center justify-between">
            <p>&copy; {{ date('Y') }} Whats-Reach. All rights reserved.</p>
            <div class="flex gap-4 mt-3 sm:mt-0">
                <a href="#" class="hover:text-white">Privacy</a>
                <a href="#" class="hover:text-white">Terms</a>
            </div>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
