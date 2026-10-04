@extends('reports.print-layout')

@section('title', 'Buku Kas Umum RT — ' . $monthLabel)
@section('report_title', 'Buku Kas Umum & Mutasi Keuangan RT — ' . $monthLabel)

@section('content')
    <table>
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th style="width: 85px;">Tanggal</th>
                <th style="width: 105px;">No. Referensi</th>
                <th>Uraian Transaksi</th>
                <th style="width: 100px;" class="text-right">Debet (Masuk)</th>
                <th style="width: 100px;" class="text-right">Kredit (Keluar)</th>
                <th style="width: 110px;" class="text-right">Saldo Kas</th>
            </tr>
        </thead>
        <tbody>
            {{-- Baris Saldo Awal --}}
            <tr style="background: #f8fafc; font-weight: 700;">
                <td class="text-center">-</td>
                <td>-</td>
                <td style="color: #64748b;">SALDO-AWAL</td>
                <td style="font-weight: 700; color: #0f766e;">SALDO AWAL KAS PERIODE INI</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold" style="color: #0f766e;">
                    Rp{{ number_format($openingBalance, 0, ',', '.') }}
                </td>
            </tr>

            @forelse($ledger as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['date_formatted'] }}</td>
                    <td class="font-bold">{{ $row['ref'] }}</td>
                    <td>
                        <span>{{ $row['description'] }}</span>
                        @if($row['type'] === 'in')
                            <span class="badge badge-success" style="font-size: 10px; margin-left: 4px;">{{ $row['method'] }}</span>
                        @endif
                    </td>
                    <td class="text-right font-bold" style="color: {{ $row['debit'] > 0 ? '#0d9488' : '#94a3b8' }};">
                        {{ $row['debit'] > 0 ? 'Rp' . number_format($row['debit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold" style="color: {{ $row['credit'] > 0 ? '#e11d48' : '#94a3b8' }};">
                        {{ $row['credit'] > 0 ? 'Rp' . number_format($row['credit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold" style="color: #1e293b;">
                        Rp{{ number_format($row['balance'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                        Belum ada mutasi transaksi kas pada periode bulan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>Saldo Awal Kas</td>
                <td class="text-right font-bold">Rp{{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Kas Masuk (Penerimaan Iuran)</td>
                <td class="text-right font-bold" style="color: #0d9488;">+ Rp{{ number_format($totalDebit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Kas Keluar (Pengeluaran Operasional)</td>
                <td class="text-right font-bold" style="color: #e11d48;">- Rp{{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #f0fdf4; border-top: 2px solid #0f766e;">
                <td style="color: #0f766e; font-weight: 800; font-size: 13px;">SALDO AKHIR KAS BERJALAN</td>
                <td class="text-right font-bold" style="color: #0f766e; font-size: 15px;">
                    Rp{{ number_format($closingBalance, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
@endsection
