@extends('reports.print-layout')

@section('title', 'Laporan Tunggakan Iuran Warga')
@section('report_title', 'Laporan Daftar Tunggakan Iuran Warga')

@section('content')
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th style="width: 100px;">No. Tagihan</th>
                <th style="width: 80px;">Rumah</th>
                <th>Kepala Keluarga</th>
                <th style="width: 110px;">Telp / WA</th>
                <th style="width: 140px;">Keterangan</th>
                <th style="width: 100px;" class="text-right">Total</th>
                <th style="width: 100px;" class="text-right">Sisa Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $inv)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->household->house_code }}</td>
                    <td>{{ $inv->household->head_of_family }}</td>
                    <td>{{ $inv->household->phone ?? '-' }}</td>
                    <td>
                        {{ $inv->isMonthly() ? 'Iuran Rutin ' . ($inv->billing_period ? \Carbon\Carbon::createFromFormat('Y-m', $inv->billing_period)->translatedFormat('F Y') : '-') : 'Iuran Pembangunan' }}
                    </td>
                    <td class="text-right">Rp{{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #e11d48;">
                        Rp{{ number_format($inv->balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #0f766e; font-weight: 700;">
                        Alhamdulillah, tidak ada tunggakan iuran warga saat ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr style="background: #fff1f2;">
                <td style="color: #9f1239; font-weight: 700;">Total Seluruh Tunggakan</td>
                <td class="text-right font-bold" style="color: #9f1239; font-size: 14px;">
                    Rp{{ number_format($totalOutstanding, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
@endsection
