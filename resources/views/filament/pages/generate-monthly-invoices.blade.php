<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Periode Selector Form (Filament 5 Schema) --}}
        {{ $this->form }}

        {{-- Notice Banner --}}
        <div class="p-4 bg-blue-50 border-l-4 border-blue-600 rounded-r-lg dark:bg-blue-950/30 dark:border-blue-500">
            <div class="flex items-start">
                <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 shrink-0" />
                <div class="text-sm text-blue-900 dark:text-blue-200">
                    <strong class="font-semibold">Aturan Tagihan Bulanan:</strong>
                    Tagihan ini hanya menghitung iuran rutin bulanan untuk rumah aktif.
                    <span class="underline font-medium">Iuran pembangunan tidak dimasukkan ke dalam proses ini</span> karena bersifat sekali bayar per kegiatan dan dikelola terpisah di menu Pembangunan.
                </div>
            </div>
        </div>

        {{-- Preview Box --}}
        @php $preview = $this->previewData; @endphp
        <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800 space-y-6">
            <div class="flex items-center justify-between border-b pb-4 dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pratinjau Tagihan Periode {{ $preview['period_label'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Estimasi tagihan yang akan diterbitkan untuk seluruh warga aktif.</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $preview['pending_to_generate'] > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300' }}">
                        {{ $preview['pending_to_generate'] > 0 ? $preview['pending_to_generate'] . ' rumah belum diterbitkan' : 'Semua tagihan sudah terbit' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Rumah Aktif</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $preview['total_households'] }} Rumah</div>
                    <div class="text-xs text-gray-500 mt-2">
                        Terbit: {{ $preview['already_generated_count'] }} | Belum: {{ $preview['pending_to_generate'] }}
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Rumah Dihuni (Rp{{ number_format($preview['occupied_rate'], 0, ',', '.') }}/bln)</div>
                    <div class="text-2xl font-bold text-teal-700 dark:text-teal-400 mt-1">{{ $preview['occupied_count'] }} Rumah</div>
                    <div class="text-xs text-teal-600 dark:text-teal-400 mt-2 font-medium">
                        Subtotal: Rp{{ number_format($preview['occupied_total'], 0, ',', '.') }}
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Rumah Belum Dihuni (Rp{{ number_format($preview['unoccupied_rate'], 0, ',', '.') }}/bln)</div>
                    <div class="text-2xl font-bold text-amber-700 dark:text-amber-400 mt-1">{{ $preview['unoccupied_count'] }} Rumah</div>
                    <div class="text-xs text-amber-600 dark:text-amber-400 mt-2 font-medium">
                        Subtotal: Rp{{ number_format($preview['unoccupied_total'], 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="p-4 bg-teal-50 border border-teal-200 rounded-lg dark:bg-teal-950/40 dark:border-teal-900 flex justify-between items-center">
                <div>
                    <span class="text-sm font-semibold text-teal-900 dark:text-teal-200">Total Nominal Tagihan Rutin Periode Ini:</span>
                    <p class="text-xs text-teal-700 dark:text-teal-400">Tidak termasuk iuran pembangunan satu kali.</p>
                </div>
                <div class="text-2xl font-extrabold text-teal-800 dark:text-teal-300">
                    Rp{{ number_format($preview['grand_total'], 0, ',', '.') }}
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button
                    wire:click="generateInvoices"
                    wire:loading.attr="disabled"
                    type="button"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-teal-700 hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 disabled:opacity-50 transition shadow-sm"
                >
                    <span wire:loading.remove>
                        Generate Tagihan Bulanan ({{ $preview['period_label'] }})
                    </span>
                    <span wire:loading>
                        Memproses Pembuatan Tagihan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
