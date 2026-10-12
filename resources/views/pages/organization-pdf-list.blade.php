<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Organisasi</title>
    <style>
        @page {
            margin: 40px 35px 70px 35px;
        }
        body {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.4;
        }
        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 8px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }
        table.letterhead-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #1e3a8a;
            margin-bottom: 20px;
        }
        table.letterhead-table td {
            padding: 0 0 10px 0;
            vertical-align: middle;
        }
        .letterhead-logo-cell {
            width: 55px;
        }
        .letterhead-logo-cell img {
            max-width: 55px;
            max-height: 55px;
        }
        .letterhead-meta-cell {
            text-align: right;
            font-size: 9px;
            color: #4b5563;
        }
        .doc-title {
            text-align: center;
            margin: 0 0 15px 0;
        }
        .doc-title h1 {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 10px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        .filter-box {
            border: 1px solid #1e3a8a;
            background-color: #f8fafc;
            padding: 8px 14px;
            margin-bottom: 18px;
            font-size: 10px;
        }
        .filter-box b {
            color: #1e3a8a;
        }
        table.list-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }
        table.list-table th {
            background-color: #1e3a8a;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.list-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9.5px;
            vertical-align: top;
        }
        table.list-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
        }
        .badge-active   { background-color: #15803d; }
        .badge-inactive { background-color: #b91c1c; }
        .col-no     { width: 25px; }
        .col-tier   { width: 35px; text-align: center; }
        .col-status { width: 55px; }
        .summary {
            margin-top: 14px;
            font-size: 9.5px;
            color: #4b5563;
            text-align: right;
        }
        .confidential {
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            font-style: italic;
            margin-top: 25px;
        }
        .empty-state {
            text-align: center;
            padding: 30px 0;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>

    <footer>
        Dokumen ini dihasilkan secara otomatis oleh Sistem CRM Inotal pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.<br>
        Dokumen ini bersifat rahasia dan hanya digunakan untuk kepentingan internal.
    </footer>

    <table class="letterhead-table">
        <tr>
            <td class="letterhead-logo-cell">
                @if($inotalLogoPath)
                    <img src="{{ $inotalLogoPath }}" alt="Inotal">
                @endif
            </td>
            <td class="letterhead-meta-cell">
                No. Dokumen: {{ $documentNumber }}<br>
                Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>

    <div class="doc-title">
        <h1>Daftar Organisasi Mitra</h1>
        <p>Rekapitulasi Data Fasilitas Kesehatan</p>
    </div>

    <div class="filter-box">
        <b>Filter:</b> Industri &mdash; {{ $industryLabel }} &nbsp;|&nbsp; Tier &mdash; {{ $tierLabel }}
    </div>

    @if($organizations->count() > 0)
        <table class="list-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Organisasi</th>
                    <th>Industri</th>
                    <th class="col-tier">Tier</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th class="col-status">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($organizations as $index => $org)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $org->name ?? '-' }}</td>
                        <td>{{ $org->industry->name ?? '-' }}</td>
                        <td class="col-tier">{{ $org->tier ?? '-' }}</td>
                        <td>{{ $org->address ?? '-' }}</td>
                        <td>{{ $org->phone ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $org->is_active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $org->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            Total: <b>{{ $organizations->count() }}</b> organisasi
        </div>
    @else
        <div class="empty-state">
            Tidak ada data organisasi yang sesuai dengan filter yang dipilih.
        </div>
    @endif

    <div class="confidential">
        &mdash; Dokumen ini bersifat rahasia dan tidak untuk disebarluaskan tanpa izin &mdash;
    </div>

</body>
</html>
