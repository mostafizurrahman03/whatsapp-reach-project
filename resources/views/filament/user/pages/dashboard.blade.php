<x-filament-panels::page>
    <!-- <div class="mb-6">
        <h1 class="text-2xl font-bold">Hello User</h1>
        <p class="text-gray-600">Welcome to the dashboard</p>
    </div> -->

    <!-- Render dashboard widgets -->
    <x-filament-widgets::widgets :widgets="$this->getWidgets()" />
</x-filament-panels::page>
