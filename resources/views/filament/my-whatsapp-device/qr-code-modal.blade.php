<div class="p-6 text-center">
    @if ($qrCodeImage)
        <p class="mb-4 text-gray-700">
            Scan this QR code with your <strong>WhatsApp app</strong> to connect your device.
        </p>

        {{-- Check if the $qrCodeImage already has the data:image/png;base64 prefix --}}
        @php
            $imgSrc = str_starts_with($qrCodeImage, 'data:image/png;base64,') 
                      ? $qrCodeImage 
                      : 'data:image/png;base64,' . $qrCodeImage;
        @endphp

        <img 
            src="{{ $imgSrc }}" 
            alt="WhatsApp QR Code" 
            class="mx-auto rounded-lg shadow-lg border border-gray-200 p-2 bg-white"
        />
    @else
        <div class="text-red-500 font-semibold">
            Failed to load QR code. Please try again later.
        </div>
    @endif
</div>










