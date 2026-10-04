<x-filament-panels::page>
    <div class="space-y-6">
        @php $summary = $this->summary; @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tunggakan Iuran Bulanan</span>
                    <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full dark:bg-blue-950 dark:text-blue-300">
                        {{ $summary['monthly_count'] }} Tagihan
                    </span>
                </div>
                <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">
                    Rp{{ number_format($summary['monthly_total'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Total tagihan rutin bulanan yang belum terbayar</div>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tunggakan Iuran Pembangunan</span>
                    <span class="px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full dark:bg-amber-950 dark:text-amber-300">
                        {{ $summary['construction_count'] }} Tagihan
                    </span>
                </div>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-2">
                    Rp{{ number_format($summary['construction_total'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Total iuran pembangunan satu kali yang belum lunas</div>
            </div>

            <div class="p-6 bg-red-50 rounded-xl shadow-sm border border-red-200 dark:bg-red-950/40 dark:border-red-900">
                <div class="text-sm font-medium text-red-800 dark:text-red-300">Total Seluruh Tunggakan Warga</div>
                <div class="text-2xl font-extrabold text-red-700 dark:text-red-300 mt-2">
                    Rp{{ number_format($summary['grand_total'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-red-600 dark:text-red-400 mt-1">Total sisa kewajiban seluruh warga</div>
            </div>
        </div>

        <div class="space-y-4">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
