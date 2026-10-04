<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Termal - {{ $payment->receipt_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/49-favicon.png') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace, system-ui;
        }

        body {
            background-color: #f1f5f9;
            color: #000000;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .paper {
            width: 58mm;
            background: #ffffff;
            padding: 8px 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-size: 11px;
            line-height: 1.25;
            color: #000;
        }

        .paper.paper-80mm {
            width: 80mm;
            padding: 12px 10px;
            font-size: 12px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .title {
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 9px;
            color: #333;
            margin-bottom: 4px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .row .label {
            color: #222;
        }

        .row .value {
            font-weight: bold;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

        .total-box {
            font-size: 13px;
            font-weight: 800;
            padding: 4px 0;
            margin: 4px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            display: flex;
            justify-content: space-between;
        }

        .qr-section {
            margin: 8px 0;
            text-align: center;
        }

        .qr-section img {
            width: 64px;
            height: 64px;
        }

        .footer-note {
            font-size: 9px;
            text-align: center;
            margin-top: 6px;
            line-height: 1.3;
        }

        /* Screen Controls */
        .controls {
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            max-width: 480px;
        }

        .btn {
            font-family: system-ui, -apple-system, sans-serif;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn:hover {
            background: #f8fafc;
        }

        .btn-primary {
            background: #0f766e;
            color: #ffffff;
            border-color: #0f766e;
        }

        .btn-primary:hover {
            background: #115e59;
        }

        .btn-wa {
            background: #16a34a;
            color: #ffffff;
            border-color: #16a34a;
        }

        .btn-wa:hover {
            background: #15803d;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
                display: block;
            }

            .controls {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                padding: 0;
                margin: 0 auto;
                width: 100%;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="controls">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Struk</button>
        <button onclick="toggleWidth()" id="widthToggleBtn" class="btn">Ganti ke 80mm</button>
        <a href="{{ route('receipt.print', $payment) }}" class="btn">Format Standar (A4)</a>
        @if($household->phone)
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $household->phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62'.substr($cleanPhone, 1);
                }
                $waMsg = "Halo Bpk/Ibu *{$household->head_of_family}* ({$household->house_code}),\n\nStruk pembayaran Anda sebesar *Rp" . number_format($payment->amount, 0, ',', '.') . "* (No. Kwitansi: *{$payment->receipt_number}*) telah dicatat.\n\nStruk Digital:\n" . route('receipt.print', ['payment' => $payment, 'format' => 'thermal']) . "\n\nSalam,\nPengurus " . $setting->complex_name;
            @endphp
            <a href="https://wa.me/{{ $cleanPhone }}?text={{ rawurlencode($waMsg) }}" target="_blank" class="btn btn-wa">📱 WA Struk</a>
        @endif
    </div>

    <div class="paper" id="receiptPaper">
        <div class="text-center">
            <div class="title">{{ $setting->complex_name }}</div>
            <div class="subtitle">{{ $setting->address ?? 'Soppeng, Sulawesi Selatan' }}</div>
            @if($setting->phone)
                <div class="subtitle">Telp: {{ $setting->phone }}</div>
            @endif
        </div>

        <div class="double-divider"></div>

        <div class="text-center font-bold" style="font-size: 11px; margin-bottom: 4px;">
            STRUK BUKTI PEMBAYARAN
        </div>

        <div class="row">
            <span class="label">No. Kwitansi:</span>
            <span class="value">{{ $payment->receipt_number }}</span>
        </div>
        <div class="row">
            <span class="label">Tgl Bayar:</span>
            <span class="value">{{ $payment->payment_date->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span class="label">Petugas:</span>
            <span class="value">{{ $payment->receiver?->name ?? 'Pengurus' }}</span>
        </div>

        <div class="divider"></div>

        <div class="row">
            <span class="label">Warga:</span>
            <span class="value">{{ $household->head_of_family }}</span>
        </div>
        <div class="row">
            <span class="label">Rumah:</span>
            <span class="value">{{ $household->house_code }} (Blok {{ $household->block }}/{{ $household->house_number }})</span>
        </div>

        <div class="divider"></div>

        <div class="row">
            <span class="label">Jenis Iuran:</span>
            <span class="value">{{ $invoice->invoice_type->getLabel() }}</span>
        </div>

        @if($invoice->isMonthly())
            <div class="row">
                <span class="label">Periode:</span>
                <span class="value">{{ \Carbon\Carbon::createFromFormat('Y-m', $invoice->billing_period)->translatedFormat('F Y') }}</span>
            </div>
        @else
            <div class="row">
                <span class="label">Kegiatan:</span>
                <span class="value">{{ $invoice->constructionProject?->project_type?->getLabel() ?? 'Insidental' }}</span>
            </div>
        @endif

        <div class="row">
            <span class="label">Metode:</span>
            <span class="value">{{ $payment->payment_method->getLabel() }}</span>
        </div>

        @if($payment->reference_number)
            <div class="row">
                <span class="label">No. Ref:</span>
                <span class="value">{{ $payment->reference_number }}</span>
            </div>
        @endif

        <div class="total-box">
            <span>TOTAL DIBAYAR</span>
            <span>Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
        </div>

        <div class="row">
            <span class="label">Status Tagihan:</span>
            <span class="value">
                @if($invoice->balance <= 0)
                    LUNAS
                @else
                    SISA Rp{{ number_format($invoice->balance, 0, ',', '.') }}
                @endif
            </span>
        </div>

        <div class="divider"></div>

        <div class="qr-section">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=64x64&amp;data={{ urlencode(route('receipt.print', $payment)) }}" alt="QR Verifikasi" loading="lazy">
            <div style="font-size: 8px; color: #555; margin-top: 2px;">Scan untuk Cek Keaslian</div>
        </div>

        <div class="footer-note">
            *** TERIMA KASIH ***<br>
            Mari bersama merawat lingkungan perumahan yang tertib, bersih & aman.
        </div>
        <div class="double-divider"></div>
        <div class="text-center" style="font-size: 8px; color: #666;">
            IURANKITA - Del Mattappa Residence
        </div>
    </div>

    <script>
        function toggleWidth() {
            const paper = document.getElementById('receiptPaper');
            const btn = document.getElementById('widthToggleBtn');
            if (paper.classList.contains('paper-80mm')) {
                paper.classList.remove('paper-80mm');
                btn.textContent = 'Ganti ke 80mm';
            } else {
                paper.classList.add('paper-80mm');
                btn.textContent = 'Ganti ke 58mm';
            }
        }

        // Auto print if ?autoprint=1
        if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 300);
            });
        }
    </script>
</body>
</html>
