<x-filament-panels::page>
    <div class="space-y-6">
        @php $data = $this->ledgerData; @endphp

        {{-- Filter Bulan --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <label for="month-select" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pilih Periode Kas:</label>
                <select id="month-select" wire:model.live="selectedMonth" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm font-medium focus:ring-teal-500 focus:border-teal-500">
                    @foreach($this->monthOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Data mutasi diperbarui secara langsung (real-time).
            </div>
        </div>

        {{-- 4 Kartu Ringkasan Buku Kas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Saldo Awal Kas</span>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                    Rp{{ number_format($data['opening_balance'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-gray-400 mt-1 block">Saldo sisa sebelum periode ini</span>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800">
                <span class="text-xs font-semibold text-teal-700 dark:text-teal-400 uppercase tracking-wider block">Penerimaan (Debet)</span>
                <div class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-1">
                    + Rp{{ number_format($data['total_debit'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-teal-600/70 mt-1 block">Kas masuk iuran warga</span>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800">
                <span class="text-xs font-semibold text-rose-700 dark:text-rose-400 uppercase tracking-wider block">Pengeluaran (Kredit)</span>
                <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
                    - Rp{{ number_format($data['total_credit'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-rose-600/70 mt-1 block">Operasional, kebersihan, dll.</span>
            </div>

            <div class="p-5 bg-teal-50/70 dark:bg-teal-950/40 rounded-xl shadow-xs border border-teal-200 dark:border-teal-800/80">
                <span class="text-xs font-extrabold text-teal-900 dark:text-teal-200 uppercase tracking-wider block">Saldo Kas Berjalan</span>
                <div class="text-2xl font-black text-teal-800 dark:text-teal-200 mt-1">
                    Rp{{ number_format($data['closing_balance'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-teal-700 dark:text-teal-300 mt-1 block">Posisi saldo kas saat ini</span>
            </div>
        </div>

        {{-- Tabel Jurnal Mutasi Kas Umum --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white text-base">Jurnal Mutasi Kas Umum</h3>
                <span class="text-xs text-gray-500">{{ count($data['ledger']) }} Baris Transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="py-3 px-4 text-center" style="width: 45px;">No</th>
                            <th class="py-3 px-4" style="width: 100px;">Tanggal</th>
                            <th class="py-3 px-4" style="width: 130px;">No. Referensi</th>
                            <th class="py-3 px-4">Uraian / Keterangan Transaksi</th>
                            <th class="py-3 px-4 text-right" style="width: 130px;">Debet (Masuk)</th>
                            <th class="py-3 px-4 text-right" style="width: 130px;">Kredit (Keluar)</th>
                            <th class="py-3 px-4 text-right" style="width: 140px;">Saldo Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium">
                        {{-- Baris Saldo Awal --}}
                        <tr class="bg-gray-50/50 dark:bg-gray-800/20">
                            <td class="py-2.5 px-4 text-center text-gray-400">-</td>
                            <td class="py-2.5 px-4 text-gray-400">-</td>
                            <td class="py-2.5 px-4 font-mono text-xs text-gray-400">SALDO-AWAL</td>
                            <td class="py-2.5 px-4 font-bold text-teal-700 dark:text-teal-400">SALDO AWAL KAS PERIODE INI</td>
                            <td class="py-2.5 px-4 text-right text-gray-400">-</td>
                            <td class="py-2.5 px-4 text-right text-gray-400">-</td>
                            <td class="py-2.5 px-4 text-right font-bold text-teal-700 dark:text-teal-400">
                                Rp{{ number_format($data['opening_balance'], 0, ',', '.') }}
                            </td>
                        </tr>

                        @forelse($data['ledger'] as $idx => $row)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition">
                                <td class="py-3 px-4 text-center text-gray-500">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $row['date_formatted'] }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-xs text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $row['ref'] }}</td>
                                <td class="py-3 px-4">
                                    <div class="text-gray-900 dark:text-white">{{ $row['description'] }}</div>
                                    @if($row['type'] === 'in')
                                        <span class="inline-flex px-2 py-0.5 mt-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            {{ $row['method'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 mt-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            Kas Operasional RT
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ $row['debit'] > 0 ? 'text-teal-600 dark:text-teal-400' : 'text-gray-400' }} whitespace-nowrap">
                                    {{ $row['debit'] > 0 ? 'Rp' . number_format($row['debit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ $row['credit'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-400' }} whitespace-nowrap">
                                    {{ $row['credit'] > 0 ? 'Rp' . number_format($row['credit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-black text-gray-900 dark:text-white whitespace-nowrap">
                                    Rp{{ number_format($row['balance'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    Belum ada transaksi mutasi kas tercatat pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
