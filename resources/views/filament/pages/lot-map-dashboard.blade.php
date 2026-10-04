<x-filament-panels::page>
    <div class="space-y-6">
        @php $data = $this->mapData; @endphp

        {{-- Metrik Ringkasan Kavling --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Kavling</span>
                <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $data['total_lots'] }} Unit</div>
                <span class="text-[11px] text-gray-400">Blok A, B, C, D</span>
            </div>

            <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800">
                <span class="text-xs font-semibold text-teal-600 dark:text-teal-400 uppercase tracking-wider block">Warga Terdaftar</span>
                <div class="text-2xl font-black text-teal-700 dark:text-teal-300 mt-1">{{ $data['registered_count'] }} Rumah</div>
                <span class="text-[11px] text-teal-600/70">Telah memiliki data KK</span>
            </div>

            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl shadow-xs border border-emerald-200 dark:border-emerald-800">
                <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider block">Lunas Bulan Ini</span>
                <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">{{ $data['paid_count'] }} Rumah</div>
                <span class="text-[11px] text-emerald-600/80">Tagihan selesai</span>
            </div>

            <div class="p-4 bg-rose-50 dark:bg-rose-950/40 rounded-xl shadow-xs border border-rose-200 dark:border-rose-800">
                <span class="text-xs font-semibold text-rose-800 dark:text-rose-300 uppercase tracking-wider block">Belum Lunas</span>
                <div class="text-2xl font-black text-rose-700 dark:text-rose-300 mt-1">{{ $data['unpaid_count'] }} Rumah</div>
                <span class="text-[11px] text-rose-600/80">Perlu ditagih</span>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl shadow-xs border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Kavling Belum Terdata</span>
                <div class="text-2xl font-black text-gray-700 dark:text-gray-300 mt-1">{{ $data['unregistered_count'] }} Unit</div>
                <span class="text-[11px] text-gray-500">Kavling kosong / baru</span>
            </div>
        </div>

        {{-- Legenda Warna Status --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-xs border border-gray-200 dark:border-gray-800 flex flex-wrap items-center gap-4 text-xs font-semibold">
            <span class="text-gray-500 uppercase tracking-wider">Keterangan Status:</span>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span>Lunas Bulan Ini</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span>Bayar Sebagian</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span>Menunggak / Belum Bayar</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                <span>Terdaftar (Belum Diterbitkan Tagihan)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-gray-500">Belum Terdaftar / Kosong</span>
            </div>
            <div class="flex items-center gap-1.5 text-amber-700 dark:text-amber-400">
                <span>🔨</span>
                <span>Renovasi / Pembangunan Aktif</span>
            </div>
        </div>

        {{-- Visual Grids per Blok --}}
        @foreach($data['blocks'] as $blockName => $lots)
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xs border border-gray-200 dark:border-gray-800 p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <span class="px-3 py-1 rounded-lg bg-teal-700 text-white font-black text-sm">
                            BLOK {{ $blockName }}
                        </span>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base">
                            Kawasan Blok {{ $blockName }} ({{ count($lots) }} Kavling)
                        </h3>
                    </div>
                    <span class="text-xs text-gray-500">
                        {{ collect($lots)->filter(fn($l) => $l['household'] !== null)->count() }} Terisi / {{ count($lots) }} Unit
                    </span>
                </div>

                {{-- Grid of Lots --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    @foreach($lots as $lot)
                        @php
                            $hh = $lot['household'];
                            $color = $lot['status_color'];
                        @endphp

                        @if($hh)
                            <a href="{{ \App\Filament\Resources\Households\HouseholdResource::getUrl('view', ['record' => $hh->id]) }}"
                               class="group block p-3.5 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md
                               @if($color === 'emerald') bg-emerald-50/50 hover:bg-emerald-100/60 border-emerald-300 dark:bg-emerald-950/20 dark:border-emerald-800/80
                               @elseif($color === 'amber') bg-amber-50/50 hover:bg-amber-100/60 border-amber-300 dark:bg-amber-950/20 dark:border-amber-800/80
                               @elseif($color === 'rose') bg-rose-50/50 hover:bg-rose-100/60 border-rose-300 dark:bg-rose-950/20 dark:border-rose-800/80
                               @elseif($color === 'sky') bg-sky-50/50 hover:bg-sky-100/60 border-sky-300 dark:bg-sky-950/20 dark:border-sky-800/80
                               @else bg-gray-50 hover:bg-gray-100 border-gray-200 dark:bg-gray-800/50 dark:border-gray-700
                               @endif">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-black text-sm text-gray-900 dark:text-white group-hover:text-teal-700 dark:group-hover:text-teal-300">
                                        {{ $lot['code'] }}
                                    </span>
                                    <div class="flex items-center gap-1">
                                        @if($lot['has_active_construction'])
                                            <span title="Ada Proyek Pembangunan / Renovasi Aktif" class="text-xs">🔨</span>
                                        @endif
                                        <span class="w-2.5 h-2.5 rounded-full
                                            @if($color === 'emerald') bg-emerald-500
                                            @elseif($color === 'amber') bg-amber-500
                                            @elseif($color === 'rose') bg-rose-500
                                            @elseif($color === 'sky') bg-sky-500
                                            @else bg-gray-400
                                            @endif"></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="font-bold text-xs text-gray-800 dark:text-gray-200 truncate" title="{{ $hh->head_of_family }}">
                                        {{ $hh->head_of_family }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 flex justify-between">
                                        <span>{{ $hh->occupancy_status->getLabel() }}</span>
                                        @if($hh->monthly_fee_override)
                                            <span class="font-bold text-amber-700 dark:text-amber-400">Khusus</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @else
                            <a href="{{ \App\Filament\Resources\Households\HouseholdResource::getUrl('create') }}"
                               class="group block p-3.5 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-400 bg-gray-50/40 dark:bg-gray-800/20 hover:bg-teal-50/30 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-semibold text-sm text-gray-400 dark:text-gray-500 group-hover:text-teal-700 dark:group-hover:text-teal-300">
                                        {{ $lot['code'] }}
                                    </span>
                                    <span class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                </div>
                                <div class="mt-2 text-center py-1">
                                    <span class="text-[11px] text-gray-400 group-hover:text-teal-700 dark:group-hover:text-teal-300 font-medium">
                                        + Daftarkan
                                    </span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
