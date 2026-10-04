<x-filament-panels::page>
    <div class="space-y-4">
        <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Rekapitulasi Iuran Pembangunan (Sekali Bayar)</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Memantau seluruh invoice iuran pembangunan satu kali per kegiatan. Iuran ini tidak berulang setiap bulan meskipun pembangunan berlangsung lama.
            </p>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
