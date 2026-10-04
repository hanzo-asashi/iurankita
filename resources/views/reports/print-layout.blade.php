<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laporan Keuangan Lingkungan') - {{ $setting->complex_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 30px;
        }
        .page {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2.5px solid #0f766e;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-title h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title p {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }
        .report-meta {
            text-align: right;
            font-size: 12px;
            color: #64748b;
        }
        .report-meta .title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }
        .summary-table {
            width: 320px;
            border: 1px solid #cbd5e1;
        }
        .summary-table td {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
        }
        .sig-block {
            text-align: center;
            width: 220px;
            font-size: 12px;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: 700;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
        }
        .no-print {
            max-width: 900px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary { background: #0f766e; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        @media print {
            body { background: #fff; padding: 0; }
            .page { border: none; box-shadow: none; padding: 10px; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="javascript:history.back()" class="btn btn-secondary">← Kembali ke Sistem</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="page">
        <div class="header">
            <div class="header-title">
                <h1>{{ $setting->complex_name }}</h1>
                <p>{{ $setting->address }} | Telp/WA: {{ $setting->phone }}</p>
            </div>
            <div class="report-meta">
                <div class="title">@yield('report_title')</div>
                <div>Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        @yield('content')

        <div class="signatures">
            <div class="sig-block">
                <div>Mengetahui,</div>
                <div>Ketua Pengurus Lingkungan</div>
                <div class="sig-space"></div>
                <div class="sig-name">( ........................................ )</div>
            </div>
            <div class="sig-block">
                <div>{{ $setting->complex_name }}, {{ now()->translatedFormat('d F Y') }}</div>
                <div>Bendahara / Petugas Kas</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ auth()->user()?->name ?? '( ........................................ )' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
