<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jarak Tempuh Kendaraan</title>
    <style>
        body {
            padding: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
            page-break-inside: avoid;
        }
        .nopol-group {
            display: table-row-group;
            page-break-inside: avoid;
        }
        tr {
            page-break-inside: avoid;
        }


    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <h3>Laporan Jarak Tempuh Kendaraan</h3>
        @php
            use Carbon\Carbon;
            Carbon::setLocale('id'); 
        @endphp

        <p>
            Periode Laporan : 
            {{ Carbon::parse($startDate)->translatedFormat('d-m-Y') }} s/d 
            {{ Carbon::parse($endDate)->translatedFormat('d-m-Y') }}
            <br><small>Tanggal Cetak Laporan : {{ Carbon::now()->translatedFormat('d F Y') }}</small>
        </p>
       
    <table>
        <thead>
            <tr>
                <th>Nopol</th>
                <th>Tanggal</th>
                <th>Total Jarak Harian (KM)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentNoPol = null;
                $totalDistance = 0;
            @endphp
    
            @foreach($data as $item)
                @if($item->no_pol != $currentNoPol)
                    @if($currentNoPol !== null)
                        <!-- Tampilkan Total Jarak sebelum berpindah ke Nopol berikutnya -->
                        <tr class="total-row">
                            <td><strong>Total Jarak</strong></td>
                            <td><strong>{{ number_format($totalDistance, 2) }}</strong></td>
                        </tr>
                    @endif
                    @php
                        $totalDistance = 0;
                        $currentNoPol = $item->no_pol;
                    @endphp
                    <!-- Baris pertama untuk Nopol baru -->
                    <tbody class="nopol-group">
                        <tr>
                            <td rowspan="{{ $data->where('no_pol', $item->no_pol)->count() + 1 }}">{{ $item->no_pol }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td>{{ number_format($item->total_distance, 2) }}</td>
                        </tr>
                @else
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                        <td>{{ number_format($item->total_distance, 2) }}</td>
                    </tr>
                @endif
                @php $totalDistance += $item->total_distance; @endphp
            @endforeach
    
            @if($currentNoPol !== null)
                <tr class="total-row">
                    <td><strong>Total Jarak</strong></td>
                    <td><strong>{{ number_format($totalDistance, 2) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>
    </div>
        
</body>
</html>
