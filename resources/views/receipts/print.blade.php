<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $payment->receipt_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo/49-favicon.png') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 40px 20px;
        }
        .container {
            max-width: 720px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .logo-title h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f766e;
            letter-spacing: -0.5px;
        }
        .logo-title p {
            font-size: 14px;
            color: #475569;
            margin-top: 4px;
        }
        .receipt-badge {
            text-align: right;
        }
        .receipt-badge .title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-badge .number {
            font-size: 14px;
            font-weight: 700;
            color: #0f766e;
            margin-top: 4px;
        }
        .details-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .details-grid td {
            padding: 10px 0;
            vertical-align: top;
            font-size: 14px;
        }
        .details-grid td.label {
            width: 32%;
            color: #64748b;
            font-weight: 500;
        }
        .details-grid td.colon {
            width: 3%;
            color: #64748b;
        }
        .details-grid td.value {
            width: 65%;
            color: #0f172a;
            font-weight: 600;
        }
        .amount-box {
            background-color: #f8fafc;
            border: 2px dashed #0f766e;
            border-radius: 8px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .amount-box .label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-box .amount {
            font-size: 26px;
            font-weight: 800;
            color: #0f766e;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
            padding-top: 20px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-box .title {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 60px;
        }
        .signature-box .name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-monthly {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-construction {
            background-color: #fef3c7;
            color: #b45309;
        }
        .actions {
            margin-top: 24px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #0f766e;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #115e59;
        }
        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
        }
        .btn-secondary:hover {
            background-color: #cbd5e1;
        }
        /* Compact / Thin Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(100, 116, 139, 0.7);
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border: none;
                padding: 20px;
                max-width: 100%;
            }
            .actions {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-title">
                <h1>{{ $setting->complex_name }}</h1>
                <p>{{ $setting->address ?? 'Sekretariat Pengelola' }}</p>
                @if($setting->phone)
                    <p>Telp: {{ $setting->phone }}</p>
                @endif
            </div>
            <div class="receipt-badge">
                <div class="title">Bukti Pembayaran</div>
                <div class="number">{{ $payment->receipt_number }}</div>
            </div>
        </div>

        <table class="details-grid">
            <tr>
                <td class="label">Telah Diterima Dari</td>
                <td class="colon">:</td>
                <td class="value">{{ $household->head_of_family }}</td>
            </tr>
            <tr>
                <td class="label">Rumah / Alamat</td>
                <td class="colon">:</td>
                <td class="value">Blok {{ $household->block }} No. {{ $household->house_number }} (Kode: {{ $household->house_code }})</td>
            </tr>
            <tr>
                <td class="label">Jenis Pembayaran</td>
                <td class="colon">:</td>
                <td class="value">
                    @if($invoice->isMonthly())
                        <span class="badge badge-monthly">Iuran Rutin Bulanan</span>
                    @else
                        <span class="badge badge-construction">Iuran Pembangunan (Sekali Bayar)</span>
                    @endif
                </td>
            </tr>

            @if($invoice->isMonthly())
                <tr>
                    <td class="label">Periode Tagihan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ \Carbon\Carbon::createFromFormat('Y-m', $invoice->billing_period)->translatedFormat('F Y') }}</td>
                </tr>
            @else
                <tr>
                    <td class="label">Kegiatan Pembangunan</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $invoice->constructionProject?->project_type?->getLabel() ?? 'Pembangunan' }}
                        @if($invoice->constructionProject?->description)
                            ({{ $invoice->constructionProject->description }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Sifat Pembayaran</td>
                    <td class="colon">:</td>
                    <td class="value"><strong>Sekali Bayar</strong> (Bukan Biaya Rutin Bulanan)</td>
                </tr>
            @endif

            <tr>
                <td class="label">Nomor Tagihan (Invoice)</td>
                <td class="colon">:</td>
                <td class="value">{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td class="colon">:</td>
                <td class="value">{{ $payment->payment_date->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td class="colon">:</td>
                <td class="value">
                    {{ $payment->payment_method->getLabel() }}
                    @if($payment->reference_number)
                        (Ref: {{ $payment->reference_number }})
                    @endif
                </td>
            </tr>
            @if($payment->notes)
                <tr>
                    <td class="label">Keterangan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $payment->notes }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Status Tagihan Saat Ini</td>
                <td class="colon">:</td>
                <td class="value">
                    @if($invoice->balance <= 0)
                        <strong style="color: #0f766e;">LUNAS</strong>
                    @else
                        <span style="color: #d97706;">Sebagian (Sisa: Rp{{ number_format($invoice->balance, 0, ',', '.') }})</span>
                    @endif
                </td>
            </tr>
        </table>

        <div class="amount-box">
            <div>
                <div class="label">Jumlah Pembayaran</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Diterima dengan sah oleh pengelola</div>
            </div>
            <div class="amount">Rp{{ number_format($payment->amount, 0, ',', '.') }}</div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="title">Warga / Pembayar</div>
                <div class="name">{{ $household->head_of_family }}</div>
            </div>
            <div class="qr-verify-box" style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=84x84&amp;data={{ urlencode(route('receipt.print', $payment)) }}" alt="QR Verifikasi" style="width: 80px; height: 80px; border: 1px solid #cbd5e1; padding: 4px; border-radius: 6px; background: #fff;" loading="lazy">
                <span style="font-size: 10px; color: #64748b; margin-top: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Scan untuk Verifikasi</span>
            </div>
            <div class="signature-box">
                <div class="title">{{ $setting->complex_name }}, {{ $payment->payment_date->translatedFormat('d F Y') }}<br>Petugas Penerima</div>
                <div class="name">{{ $payment->receiver?->name ?? 'Pengelola' }}</div>
            </div>
        </div>

        <div class="actions">
            @if($household->phone)
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $household->phone);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62'.substr($cleanPhone, 1);
                    }
                    $waMsg = "Halo Bpk/Ibu *{$household->head_of_family}* ({$household->house_code}),\n\nTerima kasih, pembayaran sebesar *Rp" . number_format($payment->amount, 0, ',', '.') . "* pada {$payment->payment_date->translatedFormat('d F Y')} telah berhasil kami terima (No. Kwitansi: *{$payment->receipt_number}*).\n\nKwitansi digital resmi:\n" . route('receipt.print', $payment) . "\n\nSalam,\nPengurus " . $setting->complex_name;
                @endphp
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waMsg) }}" target="_blank" class="btn" style="background-color: #16a34a; color: #ffffff;">Kirim via WhatsApp</a>
            @endif
            <button onclick="window.print()" class="btn btn-primary">Cetak Kwitansi</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</body>
</html>
