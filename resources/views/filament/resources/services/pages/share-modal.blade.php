<div class="flex flex-col items-center justify-center p-4 space-y-6 text-center" x-data="{
    copied: false,
    url: '{{ $url }}',
    copyLink() {
        navigator.clipboard.writeText(this.url).then(() => {
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        });
    },
    downloadQR() {
        const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?ecc=H&format=png&size=500x500&data=' + encodeURIComponent(this.url);
        fetch(qrUrl)
            .then(res => res.blob())
            .then(blob => {
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = '{{ $service->slug }}-qrcode.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
    }
}">
    <div class="p-6 bg-white border border-gray-100 rounded-2xl shadow-sm dark:border-gray-700">
        <img src="https://api.qrserver.com/v1/create-qr-code/?ecc=H&format=svg&size=250x250&data={{ urlencode($url) }}"
            alt="QR Code" class="w-48 h-48 mx-auto rounded-lg" id="service-qr-code" />
    </div>

    <div class="space-y-1">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $service->name }}
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Scan the QR code to access the service directly or share the link below.
        </p>
    </div>

    <div class="w-full max-w-md">
        <div class="flex items-center space-x-2">
            <div class="relative grow">
                <input type="text" readonly :value="url"
                    class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
            </div>
            <button type="button" @click="copyLink()"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-primary-600 rounded-xl hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-600">
                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
            </button>
        </div>
    </div>

    <div class="flex items-center justify-center space-x-3 w-full">
        <button type="button" @click="downloadQR()"
            class="inline-flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            <x-filament::icon icon="phosphor-download-simple-bold" class="w-4 h-4" />
            <span>Download PNG</span>
        </button>
    </div>
</div>
