<x-filament-panels::page>
    <div class="space-y-6">
        @php $summary = $this->summary; @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penerimaan Iuran Bulanan</div>
                <div class="text-2xl font-bold text-teal-700 dark:text-teal-400 mt-2">
                    Rp{{ number_format($summary['monthly'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Akumulasi penerimaan iuran rutin warga</div>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penerimaan Iuran Pembangunan</div>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-2">
                    Rp{{ number_format($summary['construction'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Akumulasi iuran pembangunan satu kali</div>
            </div>

            <div class="p-6 bg-teal-50 rounded-xl shadow-sm border border-teal-200 dark:bg-teal-950/40 dark:border-teal-900">
                <div class="text-sm font-medium text-teal-800 dark:text-teal-300">Total Seluruh Penerimaan Kas</div>
                <div class="text-2xl font-extrabold text-teal-900 dark:text-teal-200 mt-2">
                    Rp{{ number_format($summary['total'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-teal-700 dark:text-teal-400 mt-1">Total kas masuk dari semua jenis iuran</div>
            </div>
        </div>

        {{-- Rekonsiliasi Kas Fisik vs Bank / QRIS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-5 bg-amber-50/70 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-900/60 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider block">Kas Tunai Fisik (Di Tangan Bendahara)</span>
                    <div class="text-xl font-black text-amber-900 dark:text-amber-200 mt-1">
                        Rp{{ number_format($summary['cash'], 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-amber-700 dark:text-amber-400">Total uang tunai fisik yang wajib ada di dompet kas RT</span>
                </div>
                <div class="p-3 bg-amber-200/60 dark:bg-amber-900/50 rounded-lg text-amber-800 dark:text-amber-200 font-extrabold text-xs">
                    Kas Tunai
                </div>
            </div>

            <div class="p-5 bg-sky-50/70 dark:bg-sky-950/30 rounded-xl border border-sky-200 dark:border-sky-900/60 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-sky-800 dark:text-sky-300 uppercase tracking-wider block">Kas Non-Tunai (Rekening Bank & QRIS)</span>
                    <div class="text-xl font-black text-sky-900 dark:text-sky-200 mt-1">
                        Rp{{ number_format($summary['bank'], 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-sky-700 dark:text-sky-400">Saldo mutasi transfer bank BRI & QRIS kas</span>
                </div>
                <div class="p-3 bg-sky-200/60 dark:bg-sky-900/50 rounded-lg text-sky-800 dark:text-sky-200 font-extrabold text-xs">
                    Bank / QRIS
                </div>
            </div>
        </div>

        <div class="space-y-4">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
