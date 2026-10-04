<div class="space-y-4">
    <div class="relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 shadow-inner">
        <a href="{{ asset('images/denah-del-mattappa.png') }}" target="_blank" rel="noopener noreferrer" class="group block relative" title="Klik untuk membuka ukuran penuh di tab baru">
            <img
                src="{{ asset('images/denah-del-mattappa.png') }}"
                alt="Denah Perumahan Del Mattappa Residence"
                class="w-full h-auto max-h-[70vh] object-contain mx-auto transition-transform duration-300 group-hover:scale-[1.02]"
                loading="lazy"
            >
            <div class="absolute bottom-3 right-3 bg-slate-900/80 hover:bg-slate-900 text-white text-xs px-3 py-1.5 rounded-lg backdrop-blur-sm flex items-center gap-1.5 shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>Buka Ukuran Penuh</span>
            </div>
        </a>
    </div>

    {{-- Ringkasan Blok Kawasan --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
        <div class="p-2.5 rounded-lg bg-teal-50 dark:bg-teal-950/40 border border-teal-200/80 dark:border-teal-800/60">
            <div class="font-bold text-teal-900 dark:text-teal-200">Blok A (10 Unit)</div>
            <div class="text-teal-700 dark:text-teal-400 mt-0.5">A1 - A10</div>
        </div>
        <div class="p-2.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60">
            <div class="font-bold text-blue-900 dark:text-blue-200">Blok B (14 Unit)</div>
            <div class="text-blue-700 dark:text-blue-400 mt-0.5">B1 - B14</div>
        </div>
        <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60">
            <div class="font-bold text-emerald-900 dark:text-emerald-200">Blok C (20 Unit)</div>
            <div class="text-emerald-700 dark:text-emerald-400 mt-0.5">C1 - C20 (tanpa C13)</div>
        </div>
        <div class="p-2.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-200/80 dark:border-purple-800/60">
            <div class="font-bold text-purple-900 dark:text-purple-200">Blok D (17 Unit)</div>
            <div class="text-purple-700 dark:text-purple-400 mt-0.5">D1 - D17 (tanpa D13)</div>
        </div>
    </div>

    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between pt-1">
        <span>Lokasi: Lalabata Rilau - Soppeng</span>
        <span>Developer: PT. Del Mapparenta Properti</span>
    </div>
</div>
