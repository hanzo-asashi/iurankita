@extends('reports.print-layout')

@section('title', 'Laporan Iuran Pembangunan & Renovasi')
@section('report_title', 'Laporan Rekapitulasi Iuran Pembangunan & Renovasi')

@section('content')
    <table>
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th style="width: 75px;">Rumah</th>
                <th>Kepala Keluarga</th>
                <th style="width: 140px;">Jenis Kegiatan</th>
                <th style="width: 150px;">Jadwal Proyek</th>
                <th style="width: 110px;">Status Proyek</th>
                <th style="width: 100px;">Status Bayar</th>
                <th style="width: 110px;" class="text-right">Biaya (1x)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $index => $project)
                @php
                    $inv = $project->feeInvoice;
                    $isPaid = $inv && $inv->isPaid();
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $project->household->house_code }}</td>
                    <td>{{ $project->household->head_of_family }}</td>
                    <td>{{ $project->project_type?->getLabel() ?? '-' }}</td>
                    <td>
                        {{ $project->start_date ? $project->start_date->format('d/m/Y') : '-' }} s/d 
                        {{ $project->completion_date ? $project->completion_date->format('d/m/Y') : 'Sekarang' }}
                    </td>
                    <td>
                        <span class="badge {{ $project->status->value === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ $project->status?->getLabel() ?? '-' }}
                        </span>
                    </td>
                    <td>
                        @if($inv)
                            <span class="badge {{ $isPaid ? 'badge-success' : 'badge-danger' }}">
                                {{ $inv->status?->getLabel() }}
                            </span>
                        @else
                            <span class="badge badge-warning">Belum Ditagih</span>
                        @endif
                    </td>
                    <td class="text-right font-bold">
                        Rp{{ number_format($project->one_time_fee, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                        Belum ada data proyek pembangunan atau renovasi rumah warga.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td>Total Nilai Retribusi Pembangunan</td>
                <td class="text-right font-bold">Rp{{ number_format($totalFee, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
@endsection
