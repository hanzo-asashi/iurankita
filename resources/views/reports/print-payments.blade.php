@extends('reports.print-layout')

@section('title', 'Laporan Penerimaan Pembayaran ' . $monthLabel)
@section('report_title', 'Laporan Penerimaan Kas Iuran — ' . $monthLabel)

@section('content')
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th style="width: 110px;">No. Kwitansi</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 80px;">Rumah</th>
                <th>Warga</th>
                <th style="width: 80px;">Metode</th>
                <th style="width: 110px;" class="text-right">Nominal</th>
                <th style="width: 100px;">Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $pay)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $pay->receipt_number }}</td>
                    <td>{{ $pay->payment_date->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $pay->invoice?->household?->house_code ?? '-' }}</td>
                    <td>{{ $pay->invoice?->household?->head_of_family ?? '-' }}</td>
                    <td>{{ $pay->payment_method->getLabel() }}</td>
                    <td class="text-right font-bold" style="color: #0f766e;">
                        Rp{{ number_format($pay->amount, 0, ',', '.') }}
                    </td>
                    <td>{{ $pay->receiver?->name ?? 'Pengelola' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada catatan penerimaan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr style="background: #f0fdf4;">
                <td style="color: #166534; font-weight: 700;">Total Kas Diterima</td>
                <td class="text-right font-bold" style="color: #166534; font-size: 14px;">
                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
@endsection
