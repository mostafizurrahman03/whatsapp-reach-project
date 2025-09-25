<div>
    <section class="py-20 bg-gray-50 text-center">
        <h1 class="text-4xl font-bold mb-6">Pricing Plans</h1>
        <p class="text-gray-600 mb-10">Choose a plan that fits your business needs.</p>

        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-6">
            <!-- Example Card 1 -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-2">Basic</h2>
                <p class="text-gray-500 mb-4">For small businesses</p>
                <p class="text-3xl font-bold mb-4">19<span class="text-base font-normal">/month</span></p>
                <ul class="text-gray-600 mb-4 space-y-2">
                    <li>Send messages</li>
                    <li>Connect device</li>
                    <li>Bulk send</li>
                </ul>
                <a href="{{ route('filament.user.auth.login') }}" class="bg-green-600 text-white px-4 py-2 rounded">Get Started</a>
            </div>

            <!-- Example Card 2 -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-2">Standard</h2>
                <p class="text-gray-500 mb-4">For growing teams</p>
                <p class="text-3xl font-bold mb-4">49<span class="text-base font-normal">/month</span></p>
                <ul class="text-gray-600 mb-4 space-y-2">
                    <li>Send messages</li>
                    <li>Connect device</li>
                    <li>Bulk send</li>
                </ul>
                <a href="{{ route('home') }}" class="bg-green-600 text-white px-4 py-2 rounded">Get Started</a>
            </div>

            <!-- Example Card 3 -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-2">Pro</h2>
                <p class="text-gray-500 mb-4">For enterprises</p>
                <p class="text-3xl font-bold mb-4">99<span class="text-base font-normal">/month</span></p>
                <ul class="text-gray-600 mb-4 space-y-2">
                    <li>Send messages</li>
                    <li>Connect device</li>
                    <li>Bulk send</li>
                    <li>Priority Support</li>
                </ul>
                <a href="{{ route('home') }}" class="bg-green-600 text-white px-4 py-2 rounded">Get Started</a>
            </div>
        </div>
    </section>
</div>
