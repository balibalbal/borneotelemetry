<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kecepatan Kendaraan</title>
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
        <h3>Laporan Kecepatan Kendaraan</h3>
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
                <th>No</th>
                <th>No Polisi</th>
                <th>Tanggal</th>
                <th>Kecepatan (KM/h)</th>
                <th>Jarak Tempuh (KM)</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody> 
            @foreach ($data as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td> {{-- Menambahkan nomor urut --}}
                    <td>{{ $item['no_pol'] }}</td>
                    <td>{{ $item['time'] }}</td>
                    <td>{{ $item['speed'] }}</td>
                    <td>{{ $item['distance'] }}</td>
                    <td>{{ $item['address'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
        
</body>
</html>
