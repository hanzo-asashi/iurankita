<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IuranKita - Sistem Administrasi dan Pembayaran Iuran Warga</title>
    <meta name="description" content="IuranKita membantu pengelola perumahan mengelola iuran bulanan, iuran pembangunan satu kali, pembayaran, tunggakan, dan laporan dalam satu sistem yang sederhana.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo/49-favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ showTop: false }" @scroll.window="showTop = (window.pageYOffset > 400)" class="bg-[#F8FAFC] dark:bg-slate-950 text-[#0F172A] dark:text-slate-100 font-sans antialiased selection:bg-teal-500 selection:text-white transition-colors duration-300">
    @php
        $setting = \App\Models\AppSetting::current();
    @endphp

    {{-- Top Announcement Bar --}}
    <div class="relative z-50 bg-gradient-to-r from-teal-950 via-teal-900 to-emerald-950 text-teal-100 text-xs sm:text-sm py-2.5 px-4 text-center font-medium border-b border-teal-800/80 shadow-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>
                <strong>Aturan Pembangunan:</strong> Satu kegiatan pembangunan, satu kali iuran. Bukan iuran bulanan!
            </span>
        </div>
    </div>

    {{-- Navbar with Glassmorphism --}}
    <nav x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 bg-white/85 dark:bg-slate-900/85 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <a href="#beranda" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('images/logo/49-light.png') }}" alt="IuranKita" class="logo-light h-9 sm:h-11 w-auto transition-transform duration-300 group-hover:scale-105">
                        <img src="{{ asset('images/logo/49-dark.png') }}" alt="IuranKita" class="logo-dark h-9 sm:h-11 w-auto transition-transform duration-300 group-hover:scale-105">
                        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60">
                            v1.0
                        </span>
                    </a>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-1 lg:gap-2 text-sm font-semibold text-slate-600 dark:text-slate-300">
                    <a href="#beranda" class="px-3 py-1.5 rounded-lg hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 transition-all">Beranda</a>
                    <a href="#cek-tagihan" class="px-3 py-1.5 rounded-lg text-teal-800 dark:text-teal-300 bg-teal-100/70 dark:bg-teal-950/60 hover:bg-teal-200/80 dark:hover:bg-teal-900/60 font-bold transition-all">Cek Tagihan</a>
                    <a href="#denah" class="px-3 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 font-semibold transition-all">Denah</a>
                    <a href="#tentang" class="px-3 py-1.5 rounded-lg hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 transition-all">Fitur</a>
                    <a href="#jenis-iuran" class="px-3 py-1.5 rounded-lg hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 transition-all">Jenis Iuran</a>
                    <a href="#simulasi" class="px-3 py-1.5 rounded-lg hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 transition-all">Simulasi</a>
                    <a href="#cara-kerja" class="px-3 py-1.5 rounded-lg hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 transition-all">Cara Kerja</a>
                    <a href="#faq" class="px-3 py-1.5 rounded-lg hover:text-teal-700 hover:bg-teal-50/70 dark:hover:text-teal-300 dark:hover:bg-slate-800/60 transition-all">FAQ</a>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ url('/admin/login') }}" class="group relative inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-teal-700 to-teal-800 hover:from-teal-800 hover:to-teal-900 shadow-md shadow-teal-700/25 hover:shadow-lg hover:shadow-teal-700/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                        <span>Login Pengelola</span>
                        <svg class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                {{-- Mobile Hamburger Button --}}
                <div class="flex md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-collapse x-cloak class="md:hidden border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-4 pt-3 pb-6 space-y-2">
            <a @click="mobileMenuOpen = false" href="#beranda" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">Beranda</a>
            <a @click="mobileMenuOpen = false" href="#cek-tagihan" class="block px-3 py-2 rounded-lg text-base font-bold text-teal-800 dark:text-teal-300 bg-teal-100/70 dark:bg-slate-800">Cek Tagihan Warga</a>
            <a @click="mobileMenuOpen = false" href="#denah" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">Denah Perumahan</a>
            <a @click="mobileMenuOpen = false" href="#tentang" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">Fitur</a>
            <a @click="mobileMenuOpen = false" href="#jenis-iuran" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">Jenis Iuran</a>
            <a @click="mobileMenuOpen = false" href="#simulasi" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">Simulasi</a>
            <a @click="mobileMenuOpen = false" href="#cara-kerja" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">Cara Kerja</a>
            <a @click="mobileMenuOpen = false" href="#faq" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-800 hover:text-teal-700 dark:hover:text-teal-400">FAQ</a>
            <div class="pt-3">
                <a href="{{ url('/admin/login') }}" class="block w-full text-center py-3 rounded-xl text-sm font-bold text-white bg-teal-700 hover:bg-teal-800 shadow-md">
                    Login Pengelola
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero Section with Floating Glowing Ambient Orbs & Dots Background --}}
    <section id="beranda" class="relative pt-12 pb-20 lg:pt-20 lg:pb-32 overflow-hidden bg-dots-pattern">
        {{-- Ambient Glowing Blobs --}}
        <div class="pointer-events-none absolute -top-24 left-1/4 w-96 h-96 bg-teal-400/20 dark:bg-teal-500/10 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="pointer-events-none absolute top-40 right-10 w-80 h-80 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="pointer-events-none absolute -bottom-10 left-10 w-80 h-80 bg-sky-400/15 dark:bg-sky-500/10 rounded-full blur-3xl animate-float-delayed"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-teal-950/70 dark:to-emerald-950/70 text-teal-800 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800/80 shadow-xs mb-6 hover:shadow-md hover:scale-105 transition-all duration-300 cursor-default">
                    <span class="w-2 h-2 rounded-full bg-teal-600 animate-pulse"></span>
                    <span>{{ $setting->complex_name }}</span>
                    <span class="text-teal-400 dark:text-teal-600">&bull;</span>
                    <span class="text-teal-700 dark:text-teal-300 font-medium">Kab. Soppeng</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] bg-gradient-to-r from-slate-900 via-teal-900 to-teal-700 dark:from-white dark:via-slate-100 dark:to-teal-300 bg-clip-text text-transparent">
                    Kelola Iuran Warga Lebih Mudah.
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    IuranKita membantu pengelola perumahan mengelola iuran bulanan, iuran pembangunan, pembayaran, tunggakan, dan laporan dalam satu sistem yang sederhana.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3.5 justify-center items-center">
                    <a href="#cek-tagihan" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-base font-bold text-white bg-gradient-to-r from-teal-700 to-teal-800 hover:from-teal-800 hover:to-teal-900 shadow-xl shadow-teal-700/25 hover:shadow-teal-700/40 hover:-translate-y-1 active:translate-y-0 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Cek Tagihan Rumah</span>
                    </a>
                    <a href="{{ url('/admin/login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-base font-semibold text-slate-700 dark:text-slate-200 bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-sm hover:shadow-md hover:-translate-y-1 active:translate-y-0 transition-all duration-200">
                        <span>Login Pengelola</span>
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="#simulasi" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-base font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 transition-colors">
                        <span>Simulasi Iuran &rarr;</span>
                    </a>
                </div>

                {{-- Feature Badges --}}
                <div class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Iuran Rutin Dihuni Rp50rb & Kosong Rp35rb
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Pembangunan Rp100rb Sekali Bayar
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Kwitansi Resmi & Rekap Otomatis
                    </span>
                </div>
            </div>

            {{-- Interactive Hero Live Dashboard Mockup --}}
            <div x-data="{ activeTab: 'summary' }" class="mt-12 max-w-5xl mx-auto">
                <div class="relative bg-white/95 dark:bg-slate-900/95 rounded-2xl p-4 sm:p-7 shadow-2xl shadow-teal-900/10 border border-slate-200/90 dark:border-slate-800 transition-all duration-300">
                    {{-- Window Header Bar with Interactive Tabs --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <span class="ml-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Portal Administrasi &mdash; {{ $setting->complex_name }}
                            </span>
                        </div>

                        {{-- Interactive Tab Switcher --}}
                        <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-bold">
                            <button @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'bg-white dark:bg-slate-900 text-teal-800 dark:text-teal-300 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">
                                Ringkasan Iuran
                            </button>
                            <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'bg-white dark:bg-slate-900 text-teal-800 dark:text-teal-300 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">
                                Transaksi Terkini
                            </button>
                            <button @click="activeTab = 'households'" :class="activeTab === 'households' ? 'bg-white dark:bg-slate-900 text-teal-800 dark:text-teal-300 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">
                                Status Warga
                            </button>
                        </div>
                    </div>

                    {{-- Tab 1: Ringkasan Dua Kategori Iuran (Core PRD Section 77) --}}
                    <div x-show="activeTab === 'summary'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kategori 1: Iuran Bulanan --}}
                        <div class="group relative bg-gradient-to-br from-slate-50 to-teal-50/30 dark:from-slate-800/50 dark:to-teal-950/20 rounded-xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 hover:border-teal-500/50 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-teal-800 dark:text-teal-300 bg-teal-100 dark:bg-teal-950/80 px-3 py-1 rounded-full border border-teal-200/60 dark:border-teal-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                    Iuran Rutin Bulanan
                                </span>
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Periode Berjalan</span>
                            </div>
                            <div class="space-y-3.5">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-600 dark:text-slate-400 font-medium">Tagihan Diterbitkan</span>
                                    <span class="font-bold text-slate-900 dark:text-white">Rp12.450.000</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-600 dark:text-slate-400 font-medium">Sudah Terbayar</span>
                                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400">Rp9.850.000</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-slate-200/80 dark:border-slate-700/80">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tunggakan Bulanan</span>
                                    <span class="text-base font-black text-rose-600 dark:text-rose-400">Rp2.600.000</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden mt-2">
                                    <div class="bg-teal-600 h-2 rounded-full transition-all duration-500" style="width: 79%;"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium pt-0.5">
                                    <span>Realisasi Pembayaran</span>
                                    <span class="text-teal-700 dark:text-teal-300 font-bold">79.1% Lunas</span>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori 2: Iuran Pembangunan (1x Sekali Bayar) --}}
                        <div class="group relative bg-gradient-to-br from-amber-50/70 to-amber-100/30 dark:from-slate-800/50 dark:to-amber-950/20 rounded-xl p-5 sm:p-6 border border-amber-200/90 dark:border-amber-900/60 hover:border-amber-500/60 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-300 bg-amber-200/80 dark:bg-amber-950 px-3 py-1 rounded-full border border-amber-300 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Iuran Pembangunan
                                </span>
                                <span class="text-xs font-black text-amber-800 dark:text-amber-300 bg-amber-200/60 dark:bg-amber-900/50 px-2.5 py-0.5 rounded-full">
                                    1x Sekali Bayar
                                </span>
                            </div>
                            <div class="space-y-3.5">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-600 dark:text-slate-400 font-medium">Proyek Renovasi Aktif</span>
                                    <span class="font-bold text-slate-900 dark:text-white">3 Kegiatan</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-600 dark:text-slate-400 font-medium">Total Iuran Diterbitkan</span>
                                    <span class="font-extrabold text-amber-700 dark:text-amber-400">Rp300.000</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-amber-200/80 dark:border-amber-800/80">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Karakter Iuran</span>
                                    <span class="text-xs font-bold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950 px-2.5 py-1 rounded-md">
                                        Bukan Tagihan Bulanan
                                    </span>
                                </div>
                                <div class="w-full bg-amber-200/60 dark:bg-slate-700 h-2 rounded-full overflow-hidden mt-2">
                                    <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: 100%;"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-amber-800 dark:text-amber-300 font-medium pt-0.5">
                                    <span>Kepastian Biaya</span>
                                    <span class="font-bold">Hanya 1x per kegiatan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 2: Transaksi Terkini Mockup --}}
                    <div x-show="activeTab === 'transactions'" x-cloak x-transition class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="py-3 px-4">No. Kwitansi</th>
                                    <th class="py-3 px-4">Rumah & Warga</th>
                                    <th class="py-3 px-4">Jenis Iuran</th>
                                    <th class="py-3 px-4">Nominal</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                                <tr class="hover:bg-teal-50/40 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-bold text-teal-700 dark:text-teal-400">KW-202609-0012</td>
                                    <td class="py-3 px-4">Blok A5 No. 45 (Bpk. Mulyadi)</td>
                                    <td class="py-3 px-4"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 rounded text-xs">Iuran Rutin Bulanan</span></td>
                                    <td class="py-3 px-4 font-bold">Rp50.000</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full text-xs font-bold">LUNAS</span></td>
                                </tr>
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-bold text-amber-700 dark:text-amber-400">KW-202609-0011</td>
                                    <td class="py-3 px-4">Blok B2 No. 10 (Ibu Fatimah)</td>
                                    <td class="py-3 px-4"><span class="px-2 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 rounded text-xs">Pembangunan Dapur (1x)</span></td>
                                    <td class="py-3 px-4 font-bold">Rp100.000</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 rounded-full text-xs font-bold">1x BAYAR</span></td>
                                </tr>
                                <tr class="hover:bg-teal-50/40 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-bold text-teal-700 dark:text-teal-400">KW-202609-0010</td>
                                    <td class="py-3 px-4">Blok A1 No. 03 (Bpk. Ridwan)</td>
                                    <td class="py-3 px-4"><span class="px-2 py-0.5 bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 rounded text-xs">Rumah Kosong Bulanan</span></td>
                                    <td class="py-3 px-4 font-bold">Rp35.000</td>
                                    <td class="py-3 px-4 text-center"><span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-full text-xs font-bold">LUNAS</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Tab 3: Status Warga Mockup --}}
                    <div x-show="activeTab === 'households'" x-cloak x-transition class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 text-center">
                            <span class="text-xs text-slate-500 font-semibold uppercase">Rumah Dihuni</span>
                            <div class="text-2xl font-black text-teal-700 dark:text-teal-400 mt-1">82 Rumah</div>
                            <span class="text-xs text-slate-500">Tarif: Rp50.000 / bln</span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 text-center">
                            <span class="text-xs text-slate-500 font-semibold uppercase">Rumah Belum Dihuni</span>
                            <div class="text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">18 Rumah</div>
                            <span class="text-xs text-slate-500">Tarif: Rp35.000 / bln</span>
                        </div>
                        <div class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 text-center">
                            <span class="text-xs text-amber-800 dark:text-amber-300 font-semibold uppercase">Pembangunan Berjalan</span>
                            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">3 Kegiatan</div>
                            <span class="text-xs text-amber-700 dark:text-amber-400">Rp100.000 sekali bayar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature Cards with Hover Motion --}}
    <section id="tentang" class="py-20 bg-white dark:bg-slate-900 border-y border-slate-200 dark:border-slate-800 relative bg-grid-slate">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold text-teal-700 dark:text-teal-400 tracking-wider uppercase bg-teal-100/80 dark:bg-teal-950/80 px-3.5 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                    Modul Terintegrasi
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Fitur Utama Pengelolaan
                </h2>
                <p class="mt-3 text-base text-slate-600 dark:text-slate-300">
                    Dirancang khusus untuk kebutuhan administrasi lingkungan perumahan warga secara adil, rapi, dan transparan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                {{-- Card 1 --}}
                <div class="group relative p-7 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700/80 hover:border-teal-500/60 hover:shadow-xl hover:shadow-teal-900/5 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-13 h-13 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-400 flex items-center justify-center mb-6 font-bold border border-teal-100 dark:border-teal-800 group-hover:scale-110 group-hover:bg-teal-700 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2.5">Iuran Bulanan</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Kelola tagihan rutin berdasarkan status hunian: rumah berpenghuni atau belum berpenghuni secara otomatis tiap bulannya.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-bold text-teal-700 dark:text-teal-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                        <span>Otomatisasi Periode</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="group relative p-7 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700/80 hover:border-amber-500/60 hover:shadow-xl hover:shadow-amber-900/5 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-13 h-13 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center mb-6 font-bold border border-amber-100 dark:border-amber-800 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2.5">Iuran Pembangunan</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Catat biaya pembangunan satu kali setiap kali warga melakukan kegiatan pembangunan atau renovasi rumah tanpa berulang tiap bulan.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                        <span>1x Sekali Bayar</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="group relative p-7 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700/80 hover:border-sky-500/60 hover:shadow-xl hover:shadow-sky-900/5 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-13 h-13 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 flex items-center justify-center mb-6 font-bold border border-sky-100 dark:border-sky-800 group-hover:scale-110 group-hover:bg-sky-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2.5">Pembayaran & Kwitansi</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Catat pembayaran dengan validasi saldo ketat, penerbitan nomor kwitansi otomatis, dan cetak bukti pembayaran resmi.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-bold text-sky-700 dark:text-sky-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                        <span>Kwitansi Sah</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                {{-- Card 4 --}}
                <div class="group relative p-7 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700/80 hover:border-purple-500/60 hover:shadow-xl hover:shadow-purple-900/5 hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-13 h-13 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-400 flex items-center justify-center mb-6 font-bold border border-purple-100 dark:border-purple-800 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2.5">Laporan Lengkap</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Pantau pemasukan rutin bulanan, penerimaan pembangunan, serta daftar tunggakan yang dipisahkan secara transparan.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-bold text-purple-700 dark:text-purple-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                        <span>Rekap Kas Real-Time</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Jenis Iuran (PRD Section 79) --}}
    <section id="jenis-iuran" class="py-20 bg-slate-50 dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-teal-700 dark:text-teal-400 tracking-wider uppercase bg-teal-100 dark:bg-teal-950 px-3.5 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                    Transparansi Beban Biaya
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Struktur Tarif Iuran Warga
                </h2>
                <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-300">
                    Sistem membedakan secara tegas antara iuran bulanan rutin dan iuran pembangunan satu kali.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Tarif 1 --}}
                <div class="group relative bg-white dark:bg-slate-900 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 hover:border-teal-500/60 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950 px-3.5 py-1 rounded-full border border-teal-100 dark:border-teal-800">
                            Iuran Rutin
                        </span>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-4">Rumah Dihuni</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Dikenakan kepada rumah yang sudah berpenghuni.</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl sm:text-5xl font-extrabold text-teal-700 dark:text-teal-400">Rp50.000</span>
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 ml-2">/ bulan</span>
                        </div>
                    </div>
                    <ul class="mt-8 space-y-3.5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-6">
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Kebersihan & Pengangkutan Sampah
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Keamanan Kompleks 24 Jam
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tagihan terbit otomatis tiap bulan
                        </li>
                    </ul>
                </div>

                {{-- Tarif 2 --}}
                <div class="group relative bg-white dark:bg-slate-900 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 hover:border-slate-400 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3.5 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                            Iuran Rutin
                        </span>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-4">Rumah Belum Dihuni</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Dikenakan kepada rumah kosong / belum dihuni.</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-slate-200">Rp35.000</span>
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 ml-2">/ bulan</span>
                        </div>
                    </div>
                    <ul class="mt-8 space-y-3.5 text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-6">
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Pemeliharaan Fasilitas Lingkungan
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Keamanan Blok & Patroli Rutin
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tarif adil untuk rumah belum dihuni
                        </li>
                    </ul>
                </div>

                {{-- Tarif 3 (Mencolok PRD Section 79) --}}
                <div class="group relative bg-gradient-to-b from-amber-50/90 to-amber-100/40 dark:from-slate-900 dark:to-amber-950/30 rounded-2xl p-8 border-2 border-amber-400 dark:border-amber-600 shadow-lg shadow-amber-500/10 hover:shadow-2xl hover:shadow-amber-500/20 hover:-translate-y-2.5 transition-all duration-300 flex flex-col justify-between">
                    <div class="absolute -top-3.5 right-6 bg-gradient-to-r from-amber-600 to-amber-700 text-white text-xs font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-md flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <span>SEKALI BAYAR</span>
                    </div>
                    <div>
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-300 bg-amber-200/90 dark:bg-amber-950 px-3.5 py-1 rounded-full border border-amber-300 dark:border-amber-800">
                            Per Kegiatan Pembangunan
                        </span>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-4">Iuran Pembangunan</h3>
                        <p class="text-sm text-amber-900 dark:text-amber-300 mt-2 font-medium">Dikenakan saat warga membangun dapur / renovasi.</p>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl sm:text-5xl font-black text-amber-700 dark:text-amber-400">Rp100.000</span>
                            <span class="text-sm font-bold text-amber-900 dark:text-amber-300 ml-2">/ kegiatan</span>
                        </div>
                    </div>
                    <ul class="mt-8 space-y-3.5 text-sm text-amber-950 dark:text-amber-200 border-t border-amber-200/80 dark:border-amber-900/80 pt-6">
                        <li class="flex items-center gap-2.5 font-bold">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Dibayar SATU KALI SAJA
                        </li>
                        <li class="flex items-center gap-2.5 font-semibold">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tidak dihitung per jumlah bulan pembangunan
                        </li>
                        <li class="flex items-center gap-2.5 font-semibold">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Bukan beban iuran bulanan
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Penjelasan Pembangunan (PRD Section 81) --}}
    <section class="py-20 bg-gradient-to-br from-teal-950 via-teal-900 to-slate-950 text-white relative overflow-hidden">
        {{-- Floating Ambient Light --}}
        <div class="pointer-events-none absolute -top-24 right-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse-slow"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-teal-800/80 text-emerald-300 border border-teal-700 rounded-full text-xs font-extrabold tracking-wider uppercase mb-5 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Prinsip Utama
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                Satu kegiatan pembangunan, satu kali iuran.
            </h2>
            <p class="mt-6 text-lg sm:text-xl text-teal-100/90 max-w-3xl mx-auto leading-relaxed font-normal">
                Ketika warga mulai membangun dapur atau menambah bangunan, sistem akan mencatat iuran pembangunan sebesar tarif yang berlaku. Iuran tersebut hanya dikenakan <strong>satu kali untuk kegiatan tersebut</strong>, meskipun proses pembangunan berlangsung beberapa bulan.
            </p>
        </div>
    </section>

    {{-- Interactive Calculator & Simulasi Iuran (Alpine.js Interactive Motion) --}}
    <section id="simulasi" class="py-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800"
        x-data="{
            occupancy: 'occupied',
            hasConstruction: true,
            durationMonths: 3,
            formatRupiah(num) {
                return 'Rp' + num.toLocaleString('id-ID');
            },
            get monthlyRate() {
                return this.occupancy === 'occupied' ? 50000 : 35000;
            },
            get constructionFee() {
                return this.hasConstruction ? 100000 : 0;
            },
            get month1Total() {
                return this.monthlyRate + this.constructionFee;
            },
            get month2Total() {
                return this.monthlyRate;
            },
            get grandTotal() {
                return (this.monthlyRate * this.durationMonths) + this.constructionFee;
            }
        }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs font-bold text-teal-700 dark:text-teal-400 tracking-wider uppercase bg-teal-100 dark:bg-teal-950 px-3.5 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                    Interaktif & Transparan
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Kalkulator & Simulasi Iuran
                </h2>
                <p class="mt-3 text-base text-slate-600 dark:text-slate-300">
                    Uji langsung bagaimana sistem menghitung kewajiban warga secara adil dan terpisah.
                </p>
            </div>

            {{-- Interactive Widget Card --}}
            <div class="max-w-4xl mx-auto bg-slate-50 dark:bg-slate-800/60 rounded-3xl p-6 sm:p-10 border border-slate-200/90 dark:border-slate-700/80 shadow-xl shadow-slate-200/50 dark:shadow-none">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    {{-- Form Controls --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                                1. Pilih Status Hunian Rumah:
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="occupancy = 'occupied'" :class="occupancy === 'occupied' ? 'bg-teal-700 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="p-3.5 rounded-xl font-bold text-sm text-left transition-all">
                                    <div class="font-extrabold">Rumah Dihuni</div>
                                    <div class="text-xs opacity-85 mt-0.5">Rp50.000 / bulan</div>
                                </button>
                                <button type="button" @click="occupancy = 'unoccupied'" :class="occupancy === 'unoccupied' ? 'bg-teal-700 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="p-3.5 rounded-xl font-bold text-sm text-left transition-all">
                                    <div class="font-extrabold">Belum Dihuni</div>
                                    <div class="text-xs opacity-85 mt-0.5">Rp35.000 / bulan</div>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                                2. Aktivitas Pembangunan / Renovasi:
                            </label>
                            <label class="flex items-center gap-3.5 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-amber-400 transition-colors">
                                <input type="checkbox" x-model="hasConstruction" class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">Ada Kegiatan Bangun Dapur / Renovasi</span>
                                    <p class="text-xs text-amber-700 dark:text-amber-400 font-semibold mt-0.5">+Rp100.000 Sekali Bayar Saja</p>
                                </div>
                            </label>
                        </div>

                        <div x-show="hasConstruction" x-transition>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                                3. Estimasi Lama Pembangunan:
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" min="1" max="6" x-model="durationMonths" class="w-full accent-teal-600">
                                <span class="font-bold text-sm text-slate-800 dark:text-slate-200 whitespace-nowrap px-3 py-1 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700" x-text="durationMonths + ' Bulan'"></span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                                Durasi renovasi membuktikan iuran pembangunan tetap 1x dan tidak bertambah tiap bulan.
                            </p>
                        </div>
                    </div>

                    {{-- Dynamic Calculation Output --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border-2 border-teal-500/40 dark:border-teal-500/30 shadow-md space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="text-xs font-black uppercase text-teal-800 dark:text-teal-400">Rincian Perhitungan</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300">Live Simulation</span>
                        </div>

                        {{-- Month 1 --}}
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 space-y-1.5 text-sm">
                            <div class="flex justify-between font-bold text-slate-900 dark:text-white">
                                <span>Bulan Ke-1 (Saat Mulai)</span>
                                <span class="text-teal-700 dark:text-teal-400" x-text="formatRupiah(month1Total)"></span>
                            </div>
                            <div class="flex justify-between text-xs text-slate-500">
                                <span>Iuran Rutin:</span>
                                <span x-text="formatRupiah(monthlyRate)"></span>
                            </div>
                            <div class="flex justify-between text-xs text-amber-700 dark:text-amber-400 font-semibold" x-show="hasConstruction">
                                <span>Iuran Pembangunan (1x):</span>
                                <span>+Rp100.000</span>
                            </div>
                        </div>

                        {{-- Month 2 and onward --}}
                        <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/60 space-y-1.5 text-sm">
                            <div class="flex justify-between font-bold text-emerald-900 dark:text-emerald-300">
                                <span>Bulan Ke-2 dst (Seterusnya)</span>
                                <span class="text-emerald-700 dark:text-emerald-400" x-text="formatRupiah(month2Total) + ' / bln'"></span>
                            </div>
                            <div class="flex justify-between text-xs text-emerald-800 dark:text-emerald-400">
                                <span>Iuran Rutin:</span>
                                <span x-text="formatRupiah(monthlyRate)"></span>
                            </div>
                            <div class="flex justify-between text-xs text-emerald-700 dark:text-emerald-400 font-bold" x-show="hasConstruction">
                                <span>Iuran Pembangunan:</span>
                                <span>Rp0 (Sudah Lunas 1x)</span>
                            </div>
                        </div>

                        {{-- Grand Total --}}
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex justify-between items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
                                <span>Total <span x-text="durationMonths"></span> Bulan:</span>
                                <span class="text-xl font-black text-slate-900 dark:text-white" x-text="formatRupiah(grandTotal)"></span>
                            </div>
                            <div class="mt-3 p-3 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-xs text-teal-800 dark:text-teal-300 font-medium flex items-start gap-2">
                                <svg class="w-4 h-4 text-teal-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Prinsip PRD Terpenuhi: Biaya pembangunan hanya dibayarkan 1x, bukan iuran berulang tiap bulan.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3 Skenario Pembanding (Static Reference Cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mt-12">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <div class="text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider mb-1">Skenario 1</div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Rumah Dihuni Biasa</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Tanpa kegiatan pembangunan.</p>
                    <div class="text-xl font-extrabold text-teal-700 dark:text-teal-400">Rp50.000 <span class="text-xs font-normal text-slate-500">/ bulan</span></div>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <div class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Skenario 2</div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Rumah Belum Dihuni</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Tanpa kegiatan pembangunan.</p>
                    <div class="text-xl font-extrabold text-slate-700 dark:text-slate-300">Rp35.000 <span class="text-xs font-normal text-slate-500">/ bulan</span></div>
                </div>

                <div class="p-6 rounded-2xl bg-amber-50/80 dark:bg-slate-800/80 border border-amber-300 dark:border-amber-700/80 hover:shadow-md transition">
                    <div class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider mb-1">Skenario 3</div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Rumah Dihuni + Renovasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Membangun dapur / renovasi.</p>
                    <div class="text-sm font-semibold text-amber-900 dark:text-amber-300">
                        Bln 1: <strong class="text-slate-900 dark:text-white">Rp150.000</strong> &bull; Bln 2+: <strong class="text-teal-700 dark:text-teal-400">Rp50.000</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Cek Tagihan Mandiri Warga (Self-Service Citizen Billing Lookup) --}}
    <section id="cek-tagihan" class="py-20 bg-gradient-to-b from-slate-100 via-teal-50/30 to-white dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 border-t border-b border-slate-200 dark:border-slate-800 relative overflow-hidden"
        x-data="{
            searchCode: '',
            isLoading: false,
            errorMessage: '',
            data: null,
            async checkBilling() {
                const code = this.searchCode.trim();
                if (!code) {
                    this.errorMessage = 'Silakan ketik kode rumah Anda (contoh: A-05 atau A5).';
                    return;
                }
                this.isLoading = true;
                this.errorMessage = '';
                this.data = null;
                try {
                    const res = await fetch('{{ route('public.billing.check') }}?code=' + encodeURIComponent(code));
                    const json = await res.json();
                    if (!res.ok || !json.success) {
                        this.errorMessage = json.message || 'Data rumah tidak ditemukan.';
                    } else {
                        this.data = json;
                    }
                } catch (e) {
                    this.errorMessage = 'Gagal menghubungi server. Silakan coba sesaat lagi.';
                } finally {
                    this.isLoading = false;
                }
            },
            resetSearch() {
                this.searchCode = '';
                this.data = null;
                this.errorMessage = '';
            }
        }">
        {{-- Subtle ambient blur --}}
        <div class="pointer-events-none absolute -top-20 left-1/3 w-80 h-80 bg-teal-400/15 dark:bg-teal-500/10 rounded-full blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-10 right-10 w-72 h-72 bg-emerald-400/15 dark:bg-emerald-500/10 rounded-full blur-3xl"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800 uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Layanan Mandiri Warga
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Cek Tagihan dan Kwitansi Iuran
                </h2>
                <p class="mt-3 text-base text-slate-600 dark:text-slate-300">
                    Warga {{ $setting->complex_name }} dapat mengecek status pembayaran, riwayat tunggakan, dan kwitansi resmi secara mandiri tanpa perlu login.
                </p>
            </div>

            {{-- Lookup Form Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl shadow-teal-900/5">
                <form @submit.prevent="checkBilling()" class="max-w-2xl mx-auto">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <input type="text" x-model="searchCode" placeholder="Ketik Kode Rumah (contoh: A-05 atau A5)..." class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent font-medium transition text-base">
                        </div>
                        <button type="submit" :disabled="isLoading" class="inline-flex items-center justify-center px-7 py-3.5 rounded-xl font-bold text-white bg-teal-700 hover:bg-teal-800 disabled:opacity-50 shadow-md shadow-teal-700/20 hover:shadow-lg transition-all">
                            <template x-if="isLoading">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isLoading ? 'Memeriksa...' : 'Periksa Tagihan'"></span>
                        </button>
                    </div>

                    {{-- Quick chips & Denah link --}}
                    <div class="mt-3.5 flex items-center justify-between gap-3 flex-wrap text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span>Contoh kode:</span>
                            <template x-for="chip in ['A-03', 'B-02', 'C-10', 'C-15', 'D-04', 'D-08']" :key="chip">
                                <button type="button" @click="searchCode = chip; checkBilling()" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-teal-100 dark:bg-slate-800 dark:hover:bg-teal-950 font-semibold text-slate-700 hover:text-teal-800 dark:text-slate-300 dark:hover:text-teal-300 transition-colors" x-text="chip"></button>
                            </template>
                        </div>
                        <a href="#denah" class="inline-flex items-center gap-1.5 font-bold text-teal-700 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Lihat Denah Kawasan</span>
                        </a>
                    </div>
                </form>

                {{-- Error Message Box --}}
                <div x-show="errorMessage" x-cloak x-transition class="mt-6 max-w-2xl mx-auto p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-800 dark:text-rose-300 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span x-text="errorMessage"></span>
                </div>

                {{-- Result Presentation --}}
                <div x-show="data" x-cloak x-transition class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-800 space-y-6">
                    {{-- Resident Info Header --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <span class="px-3 py-1 rounded-lg text-sm font-black bg-teal-700 text-white" x-text="data?.household.house_code"></span>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300" x-text="'Blok ' + data?.household.block + ' No. ' + data?.household.house_number"></span>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300" x-text="data?.household.occupancy_status"></span>
                            </div>
                            <div class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                Kepala Keluarga: <strong class="text-slate-900 dark:text-white" x-text="data?.household.masked_name"></strong>
                            </div>
                        </div>

                        {{-- Current Month Badge --}}
                        <div class="text-left sm:text-right">
                            <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium" x-text="'Status ' + data?.current_month.period_name + ':'"></span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 rounded-full text-xs font-extrabold"
                                :class="{
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300': data?.current_month.status === 'Lunas',
                                    'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': data?.current_month.status === 'Sebagian',
                                    'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300': data?.current_month.status === 'Belum Lunas' || data?.current_month.status === 'Menunggak',
                                    'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300': data?.current_month.status === 'Belum Diterbitkan'
                                }">
                                <span class="w-1.5 h-1.5 rounded-full"
                                    :class="{
                                        'bg-emerald-500': data?.current_month.status === 'Lunas',
                                        'bg-amber-500': data?.current_month.status === 'Sebagian',
                                        'bg-rose-500': data?.current_month.status === 'Belum Lunas' || data?.current_month.status === 'Menunggak',
                                        'bg-slate-400': data?.current_month.status === 'Belum Diterbitkan'
                                    }"></span>
                                <span x-text="data?.current_month.status"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Metrics Summary --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-5 rounded-2xl border"
                            :class="data?.total_outstanding > 0 ? 'bg-rose-50/60 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/60' : 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/60'">
                            <span class="text-xs font-bold uppercase tracking-wider block"
                                :class="data?.total_outstanding > 0 ? 'text-rose-700 dark:text-rose-400' : 'text-emerald-700 dark:text-emerald-400'">
                                Total Tagihan Belum Dibayar
                            </span>
                            <div class="mt-1 text-2xl sm:text-3xl font-black"
                                :class="data?.total_outstanding > 0 ? 'text-rose-800 dark:text-rose-300' : 'text-emerald-800 dark:text-emerald-300'"
                                x-text="data?.formatted_total_outstanding"></div>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                                Jumlah Tagihan Terbuka
                            </span>
                            <div class="mt-1 text-2xl sm:text-3xl font-black text-slate-900 dark:text-white" x-text="data?.unpaid_count + ' Lembar Tagihan'"></div>
                        </div>
                    </div>

                    {{-- Unpaid Invoices List --}}
                    <template x-if="data?.unpaid_count > 0">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">
                                Rincian Tagihan Yang Perlu Dibayar:
                            </h4>
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-100 dark:bg-slate-800 text-xs uppercase font-bold text-slate-600 dark:text-slate-300">
                                        <tr>
                                            <th class="p-3">No. Tagihan</th>
                                            <th class="p-3">Keterangan</th>
                                            <th class="p-3">Jatuh Tempo</th>
                                            <th class="p-3">Status</th>
                                            <th class="p-3 text-right">Sisa Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                        <template x-for="inv in data.unpaid_invoices" :key="inv.invoice_number">
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                                <td class="p-3 font-bold text-slate-900 dark:text-white whitespace-nowrap" x-text="inv.invoice_number"></td>
                                                <td class="p-3 text-slate-700 dark:text-slate-300" x-text="inv.title"></td>
                                                <td class="p-3 whitespace-nowrap" :class="inv.is_overdue ? 'text-rose-600 font-semibold' : 'text-slate-600 dark:text-slate-400'" x-text="inv.due_date"></td>
                                                <td class="p-3 whitespace-nowrap">
                                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold"
                                                        :class="{
                                                            'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300': inv.status_label === 'Terlambat' || inv.status_label === 'Belum Lunas',
                                                            'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': inv.status_label === 'Sebagian'
                                                        }"
                                                        x-text="inv.status_label"></span>
                                                </td>
                                                <td class="p-3 text-right font-black text-rose-600 dark:text-rose-400 whitespace-nowrap" x-text="inv.formatted_balance"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    {{-- Fully Paid Alert (if no unpaid invoices) --}}
                    <template x-if="data?.unpaid_count === 0">
                        <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-300 mx-auto flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <h4 class="font-extrabold text-emerald-900 dark:text-emerald-200 text-base">Semua Iuran Telah Lunas!</h4>
                            <p class="text-xs text-emerald-800 dark:text-emerald-300 mt-1 max-w-md mx-auto">
                                Terima kasih atas partisipasi aktif Bapak/Ibu dalam membayar iuran dan menjaga kelancaran operasional {{ $setting->complex_name }}.
                            </p>
                        </div>
                    </template>

                    {{-- Recent Paid History with Printable Receipts --}}
                    <template x-if="data?.recent_paid && data.recent_paid.length > 0">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3">
                                Kwitansi Pembayaran Terakhir:
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <template x-for="paid in data.recent_paid" :key="paid.invoice_number">
                                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex flex-col justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-teal-700 dark:text-teal-400" x-text="paid.period"></div>
                                            <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-1" x-text="paid.formatted_amount"></div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5" x-text="'Dibayar: ' + paid.paid_at"></div>
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                            <a :href="paid.receipt_url" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-700 hover:text-teal-800 dark:text-teal-300">
                                                <span>Buka Kwitansi</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Official Payment Destination Card (Bank & QRIS) --}}
                    <template x-if="data?.total_outstanding > 0 && data?.payment_destination">
                        <div x-data="{ copied: false }" class="p-4 sm:p-5 rounded-2xl bg-teal-50/80 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-teal-900 dark:text-teal-200 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span>Rekening Kas Resmi Pembayaran</span>
                            </h4>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-xl border border-teal-100 dark:border-teal-900/50">
                                <div>
                                    <span class="inline-block text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider" x-text="data.payment_destination.bank_name"></span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-lg sm:text-xl font-mono font-black text-slate-900 dark:text-white" x-text="data.payment_destination.bank_account_number"></span>
                                        <button type="button" @click="navigator.clipboard.writeText(data.payment_destination.bank_account_number); copied = true; setTimeout(() => copied = false, 2000)" class="px-2.5 py-1 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-xs font-semibold inline-flex items-center gap-1 border border-slate-200 dark:border-slate-700">
                                            <span x-show="!copied">Salin No. Rek</span>
                                            <span x-show="copied" class="text-teal-600 font-bold" style="display: none;">Tersalin!</span>
                                        </button>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="'Atas Nama: ' + data.payment_destination.bank_account_holder"></div>
                                </div>
                                <template x-if="data.payment_destination.qris_image_url">
                                    <div class="text-center">
                                        <img :src="data.payment_destination.qris_image_url" alt="QRIS" class="w-24 h-24 object-contain rounded-lg border border-slate-200 dark:border-slate-700 bg-white p-1 mx-auto">
                                        <span class="text-[10px] font-bold text-slate-500 mt-1 block">Scan QRIS</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Call to Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center pt-2">
                        <template x-if="data?.total_outstanding > 0">
                            <a :href="data.wa_confirm_url" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl font-bold text-white bg-[#25D366] hover:bg-[#1EBE5D] shadow-md transition-all">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Konfirmasi Bayar via WhatsApp</span>
                            </a>
                        </template>
                        <button type="button" @click="resetSearch()" class="px-5 py-3 rounded-xl font-semibold text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Cari Rumah Lain
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Denah Perumahan (Site Plan) --}}
    <section id="denah" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800" x-data="{ denahModalOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider text-teal-800 dark:text-teal-300 bg-teal-100 dark:bg-teal-950/80 border border-teal-200 dark:border-teal-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                    Site Plan & Denah Kawasan
                </span>
                <h2 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
                    Denah Perumahan Del Mattappa Residence
                </h2>
                <p class="mt-4 text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                    Peta tata letak blok dan kavling perumahan di Lalabata Rilau, Kabupaten Soppeng. Dikembangkan oleh <strong>PT. Del Mapparenta Properti</strong>.
                </p>
            </div>

            {{-- Denah Card & Viewer --}}
            <div class="relative bg-slate-50 dark:bg-slate-950 rounded-3xl p-4 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl shadow-teal-900/5">
                {{-- Quick Block Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">Blok A</span>
                            <span class="text-xs font-black px-2 py-0.5 rounded-md bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300">10 Unit</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-slate-800 dark:text-slate-200">Kavling A1 &ndash; A10</p>
                        <p class="text-xs text-slate-500 mt-0.5">Sisi barat daya kawasan</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Blok B</span>
                            <span class="text-xs font-black px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300">14 Unit</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-slate-800 dark:text-slate-200">Kavling B1 &ndash; B14</p>
                        <p class="text-xs text-slate-500 mt-0.5">Sisi selatan & tengah</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Blok C</span>
                            <span class="text-xs font-black px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">20 Unit</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-slate-800 dark:text-slate-200">Kavling C1 &ndash; C20</p>
                        <p class="text-xs text-slate-500 mt-0.5">Baris selatan jalan utama (tanpa C13)</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400">Blok D</span>
                            <span class="text-xs font-black px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300">17 Unit</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-slate-800 dark:text-slate-200">Kavling D1 &ndash; D17</p>
                        <p class="text-xs text-slate-500 mt-0.5">Baris utara menghadap sungai (tanpa D13)</p>
                    </div>
                </div>

                {{-- Interactive Image Container --}}
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 cursor-zoom-in shadow-md" @click="denahModalOpen = true">
                    <img
                        src="{{ asset('images/denah-del-mattappa.png') }}"
                        alt="Denah Perumahan Del Mattappa Residence - Lalabata Rilau, Soppeng"
                        class="w-full h-auto max-h-[650px] object-contain mx-auto transition-transform duration-500 group-hover:scale-[1.02]"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="px-5 py-2.5 rounded-xl bg-slate-900/90 text-white font-bold text-sm shadow-xl backdrop-blur-md flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                            </svg>
                            Klik untuk Memperbesar Denah
                        </span>
                    </div>
                    <div class="absolute bottom-4 right-4 bg-white/90 dark:bg-slate-900/90 text-slate-800 dark:text-slate-200 text-xs px-3.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Ukuran Penuh Tersedia</span>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        * Format penomoran di sistem menggunakan kode standar seperti <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-teal-700 dark:text-teal-400 font-mono font-bold">A-01</code>, <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-teal-700 dark:text-teal-400 font-mono font-bold">B-09</code>, <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-teal-700 dark:text-teal-400 font-mono font-bold">C-15</code>, <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-teal-700 dark:text-teal-400 font-mono font-bold">D-04</code>.
                    </p>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="denahModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-teal-700 hover:bg-teal-800 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span>Perbesar Denah</span>
                        </button>
                        <a href="{{ asset('images/denah-del-mattappa.png') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka di Tab Baru</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lightbox Modal --}}
        <div
            x-show="denahModalOpen"
            x-cloak
            @keydown.escape.window="denahModalOpen = false"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-6xl w-full p-4 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[95vh] flex flex-col"
                @click.away="denahModalOpen = false"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Denah Perumahan Del Mattappa Residence</h3>
                        <p class="text-xs text-slate-500">Lalabata Rilau - Soppeng | PT. Del Mapparenta Properti</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('images/denah-del-mattappa.png') }}" target="_blank" class="p-2 rounded-xl text-slate-500 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Buka Tab Baru">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <button type="button" @click="denahModalOpen = false" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Tutup">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="overflow-auto py-4 flex-1 flex items-center justify-center">
                    <img
                        src="{{ asset('images/denah-del-mattappa.png') }}"
                        alt="Denah Del Mattappa Residence"
                        class="w-full h-auto max-h-[75vh] object-contain rounded-xl"
                    >
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                    <span>Tekan tombol <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded font-mono">ESC</kbd> untuk menutup</span>
                    <button type="button" @click="denahModalOpen = false" class="font-bold text-teal-700 dark:text-teal-400 hover:underline">Tutup Tampilan</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Cara Kerja (PRD Section 82) --}}
    <section id="cara-kerja" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold text-teal-700 dark:text-teal-400 tracking-wider uppercase bg-teal-100 dark:bg-teal-950 px-3.5 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                    Alur Otomatis
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Cara Kerja Sistem</h2>
                <p class="mt-3 text-base text-slate-600 dark:text-slate-300">Alur sederhana dan otomatis untuk efisiensi pengurus.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group relative bg-white dark:bg-slate-900 p-7 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500/50 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 font-black flex items-center justify-center text-sm mb-5 group-hover:bg-teal-600 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        1
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Data Rumah & KK</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Menyimpan data kepala keluarga, nomor rumah/blok, dan status hunian (dihuni / kosong).</p>
                </div>

                <div class="group relative bg-white dark:bg-slate-900 p-7 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500/50 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 font-black flex items-center justify-center text-sm mb-5 group-hover:bg-teal-600 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        2
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Tagihan Rutin Bulanan</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Sistem menerbitkan invoice rutin bulanan secara otomatis tanpa mencampurnya dengan biaya proyek.</p>
                </div>

                <div class="group relative bg-white dark:bg-slate-900 p-7 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-black flex items-center justify-center text-sm mb-5 group-hover:bg-amber-600 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        3
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Pencatatan Pembangunan</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Ketika warga mulai renovasi atau membuat dapur, pengelola mencatat kegiatan pembangunan.</p>
                </div>

                <div class="group relative bg-white dark:bg-slate-900 p-7 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-black flex items-center justify-center text-sm mb-5 group-hover:bg-amber-600 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        4
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Invoice 1x Pembangunan</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Sistem otomatis menerbitkan SATU invoice iuran pembangunan sekali bayar.</p>
                </div>

                <div class="group relative bg-white dark:bg-slate-900 p-7 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500/50 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 font-black flex items-center justify-center text-sm mb-5 group-hover:bg-teal-600 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        5
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Pembayaran & Bukti</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Petugas mencatat pembayaran tunai/transfer, menerbitkan nomor kwitansi unik, dan mencetak bukti.</p>
                </div>

                <div class="group relative bg-white dark:bg-slate-900 p-7 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-teal-500/50 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300 font-black flex items-center justify-center text-sm mb-5 group-hover:bg-teal-600 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        6
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Laporan Real-Time</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Pengelola dapat melihat rekapitulasi kas masuk, status proyek, dan daftar tunggakan kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section FAQ with Live Search Filter (PRD Section 83) --}}
    <section id="faq" x-data="{
        openFaq: 1,
        searchQuery: '',
        faqs: [
            { id: 1, q: 'Apakah iuran pembangunan dibayar setiap bulan?', a: 'Tidak. Iuran pembangunan hanya dibayar satu kali untuk setiap kegiatan pembangunan yang dilakukan oleh warga.' },
            { id: 2, q: 'Berapa iuran pembangunan?', a: 'Default adalah Rp100.000 satu kali untuk satu kegiatan pembangunan (dapur, renovasi, atau penambahan bangunan).' },
            { id: 3, q: 'Bagaimana jika pembangunan berlangsung beberapa bulan?', a: 'Tidak ada tambahan Rp100.000 setiap bulan. Walaupun pembangunan berlangsung 2 bulan, 3 bulan, atau 6 bulan, tarif tersebut tetap hanya satu kali untuk kegiatan tersebut.' },
            { id: 4, q: 'Jika beberapa tahun kemudian membangun lagi bagaimana?', a: 'Kegiatan pembangunan baru di masa mendatang akan dicatat sebagai proyek baru, sehingga dapat dikenakan iuran pembangunan satu kali lagi sesuai tarif yang berlaku saat itu.' },
            { id: 5, q: 'Apakah iuran bulanan tetap dibayar saat rumah sedang dibangun?', a: 'Ya. Iuran rutin bulanan tetap dibayar mengikuti status hunian rumah (Rp50.000 jika dihuni, Rp35.000 jika belum dihuni). Iuran pembangunan merupakan pos kewajiban terpisah.' },
            { id: 6, q: 'Apakah tarif dapat berubah?', a: 'Ya. Administrator dapat memperbarui master tarif sewaktu-waktu. Perubahan tarif di masa depan tidak akan mengubah histori invoice yang telah diterbitkan sebelumnya.' }
        ]
    }" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-teal-700 dark:text-teal-400 tracking-wider uppercase bg-teal-100 dark:bg-teal-950 px-3.5 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                    Tanya Jawab
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="mt-3 text-base text-slate-600 dark:text-slate-300">Klarifikasi lengkap seputar aturan iuran bulanan dan iuran pembangunan.</p>

                {{-- Interactive Search Filter --}}
                <div class="mt-6 max-w-md mx-auto relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari pertanyaan (contoh: dapur, bulanan, tarif)..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 dark:text-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="space-y-4">
                <template x-for="faq in faqs.filter(f => !searchQuery || f.q.toLowerCase().includes(searchQuery.toLowerCase()) || f.a.toLowerCase().includes(searchQuery.toLowerCase()))" :key="faq.id">
                    <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs hover:border-teal-500/50 transition-all duration-200">
                        <button @click="openFaq = (openFaq === faq.id ? null : faq.id)" type="button" class="w-full px-6 py-4.5 text-left font-bold text-slate-900 dark:text-white flex justify-between items-center bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800 transition">
                            <span x-text="faq.q" class="text-base"></span>
                            <svg :class="openFaq === faq.id ? 'rotate-180 text-teal-600' : 'text-slate-400'" class="w-5 h-5 transform transition-transform duration-200 shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFaq === faq.id" x-collapse class="px-6 py-4.5 text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/60 leading-relaxed" x-text="faq.a"></div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    {{-- CTA Banner (PRD Section 84) --}}
    <section class="py-20 bg-gradient-to-r from-teal-800 to-teal-900 text-white relative overflow-hidden">
        <div class="pointer-events-none absolute -bottom-10 right-10 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl animate-pulse-slow"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                Administrasi Iuran Lebih Tertib, Sederhana, dan Mudah Dipantau.
            </h2>
            <p class="mt-5 text-base sm:text-lg text-teal-100 max-w-2xl mx-auto leading-relaxed">
                Tingkatkan transparansi dan kenyamanan bertetangga bersama sistem IuranKita di {{ $setting->complex_name }}.
            </p>
            <div class="mt-9">
                <a href="{{ url('/admin/login') }}" class="inline-flex items-center justify-center px-9 py-4 rounded-xl text-base font-extrabold text-teal-900 bg-white hover:bg-teal-50 shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-200">
                    Masuk ke Sistem Pengelola
                </a>
            </div>
        </div>
    </section>

    {{-- Footer (PRD Section 85) --}}
    <footer class="bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 py-14 border-t border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div class="md:col-span-2">
                    <div class="mb-4">
                        <img src="{{ asset('images/logo/49-light.png') }}" alt="IuranKita" class="logo-light h-10 sm:h-12 w-auto">
                        <img src="{{ asset('images/logo/49-dark.png') }}" alt="IuranKita" class="logo-dark h-10 sm:h-12 w-auto">
                    </div>
                    <p class="text-sm mt-3 text-slate-700 dark:text-slate-300 font-semibold">{{ $setting->tagline }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">{{ $setting->footer_text }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider mb-4">Kontak Pengurus</h4>
                    <p class="text-sm text-slate-800 dark:text-slate-200 font-bold">{{ $setting->complex_name }}</p>
                    <p class="text-sm mt-1 text-slate-600 dark:text-slate-400">{{ $setting->address ?? 'Jl. Kemakmuran No.45 Blok A5, Kab. Soppeng' }}</p>
                    @if($setting->phone)
                        <p class="text-sm mt-1 text-slate-600 dark:text-slate-400">Telp: {{ $setting->phone }}</p>
                    @endif
                    @if($setting->email)
                        <p class="text-sm mt-1 text-slate-600 dark:text-slate-400">Email: {{ $setting->email }}</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2.5 text-sm font-medium">
                        <li><a href="#beranda" class="hover:text-teal-700 dark:hover:text-white transition">Beranda</a></li>
                        <li><a href="#jenis-iuran" class="hover:text-teal-700 dark:hover:text-white transition">Tarif Iuran</a></li>
                        <li><a href="#simulasi" class="hover:text-teal-700 dark:hover:text-white transition">Kalkulator Simulasi</a></li>
                        <li><a href="#denah" class="hover:text-teal-700 dark:hover:text-white transition">Denah Kawasan</a></li>
                        <li><a href="#cara-kerja" class="hover:text-teal-700 dark:hover:text-white transition">Cara Kerja</a></li>
                        <li><a href="{{ url('/admin/login') }}" class="hover:text-teal-800 dark:hover:text-white transition font-bold text-teal-700 dark:text-teal-400">Portal Pengelola</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 text-xs text-center text-slate-500">
                &copy; {{ now()->year }} IuranKita &bull; {{ $setting->complex_name }}. Semua hak dilindungi.
            </div>
        </div>
    </footer>

    {{-- Floating Back To Top Button --}}
    <div x-show="showTop" x-transition.opacity.duration.300ms class="fixed bottom-6 right-6 z-50">
        <a href="#beranda" class="flex items-center justify-center w-12 h-12 rounded-full bg-teal-700 hover:bg-teal-800 text-white shadow-xl hover:shadow-2xl hover:scale-110 active:scale-95 transition-all duration-200 focus:outline-none" title="Kembali ke atas">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
        </a>
    </div>
</body>
</html>
