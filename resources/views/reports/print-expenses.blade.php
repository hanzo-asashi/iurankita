@extends('reports.print-layout')

@section('title', 'Laporan Pengeluaran Kas RT — ' . $monthLabel)
@section('report_title', 'Laporan Buku Pengeluaran Kas RT — ' . $monthLabel)

@section('content')
    <table>
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th style="width: 100px;">No. Bukti</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 130px;">Kategori</th>
                <th>Keperluan / Uraian Pengeluaran</th>
                <th style="width: 120px;">Penerima / Toko</th>
                <th style="width: 110px;" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $expense->expense_number }}</td>
                    <td>{{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        <span class="badge badge-warning">
                            {{ $expense->category?->getLabel() ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div class="font-bold">{{ $expense->title }}</div>
                        @if($expense->notes)
                            <div style="font-size: 11px; color: #64748b;">{{ $expense->notes }}</div>
                        @endif
                    </td>
                    <td>{{ $expense->recipient ?? '-' }}</td>
                    <td class="text-right font-bold" style="color: #e11d48;">
                        Rp{{ number_format($expense->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                        Belum ada catatan pengeluaran kas operasional pada bulan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr style="background: #fff1f2;">
                <td style="color: #9f1239; font-weight: 700;">Total Seluruh Pengeluaran Kas</td>
                <td class="text-right font-bold" style="color: #9f1239; font-size: 14px;">
                    Rp{{ number_format($totalExpense, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
@endsection
