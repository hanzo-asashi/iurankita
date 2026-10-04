@extends('reports.print-layout')

@section('title', 'Rekap Tagihan Bulanan ' . $periodLabel)
@section('report_title', 'Rekap Tagihan Iuran Bulanan — ' . $periodLabel)

@section('content')
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th style="width: 80px;">Rumah</th>
                <th>Kepala Keluarga</th>
                <th style="width: 110px;">Status Hunian</th>
                <th style="width: 100px;" class="text-right">Tagihan</th>
                <th style="width: 100px;" class="text-right">Dibayar</th>
                <th style="width: 100px;" class="text-right">Sisa</th>
                <th style="width: 90px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $inv)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $inv->household->house_code }}</td>
                    <td>{{ $inv->household->head_of_family }}</td>
                    <td>{{ $inv->household->occupancy_status->getLabel() }}</td>
                    <td class="text-right font-bold">Rp{{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #0f766e;">Rp{{ number_format($inv->amount_paid, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: {{ $inv->balance > 0 ? '#e11d48' : '#64748b' }};">
                        Rp{{ number_format($inv->balance, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span style="font-weight: 700; color: {{ $inv->status->value === 'paid' ? '#0f766e' : ($inv->status->value === 'partial' ? '#d97706' : '#e11d48') }};">
                            {{ $inv->status->getLabel() }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data tagihan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>Total Tagihan Diterbitkan</td>
                <td class="text-right font-bold">Rp{{ number_format($totalBilled, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pembayaran Diterima</td>
                <td class="text-right font-bold" style="color: #0f766e;">Rp{{ number_format($totalPaid, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #fff1f2;">
                <td style="color: #9f1239; font-weight: 700;">Total Sisa Tunggakan</td>
                <td class="text-right font-bold" style="color: #9f1239;">Rp{{ number_format($totalBalance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
@endsection
