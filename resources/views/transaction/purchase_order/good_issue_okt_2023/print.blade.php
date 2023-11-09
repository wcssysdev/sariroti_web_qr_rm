<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Good Issue Detail</title>
    <style>
        @page {
            size: 7in 9.25in;
            margin: 27mm 0mm 27mm 0mm;
        }
        th,td{
            font-size:12px;
        }
        @media print {
            html, body {
                width: 8.5in; /* was 8.5in */
                height: 5.5in; /* was 5.5in */
                display: block;
                font-family: "Calibri";
                /*font-size: auto; NOT A VALID PROPERTY */
            }

            @page {
                size: 8.5in 5.5in; /* . Random dot? */
            }
        }
    </style>
</head>
<body>
    <div>
        <h2 style="text-align: center; font-size:14px;">RECEIVING SLIP TRANSFER ANTAR PLANT</h2>
        <table style="width:30%;">
            <tr>
                <td>No RC</td>
                <td>:</td>
                <td>{{ $header_data["TR_GI_SAPHEADER_ID"] }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>:</td>
                <td>{{ $header_data["TR_GI_SAPHEADER_CREATED_TIMESTAMP"] }}</td>
            </tr>
            <tr>
                <td>Plant Pengirim</td>
                <td>:</td>
                <td>{{ $header_data["MA_PLANT_NAME"] }}</td>
            </tr>
            <tr>
                <td>Surat jalan</td>
                <td>:</td>
                <td>-</td>
            </tr>
        </table>
        <br>
        <table border=1 style="width:100%; border-collapse: collapse;"> 
            <tr>
                <th>No.</th>
                <th>Material</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Qty. Received</th>
                <th>Location</th>
            </tr>
            @php
                $count = 1;
            @endphp
            @foreach ($detail_data as $row)
            <tr>
                <td>{{ $count }}</td>
                <td>{{ $row["TR_GI_SAPDETAIL_MATERIAL_CODE"] }}</td>
                <td>{{ $row["TR_GI_SAPDETAIL_MATERIAL_NAME"] }}</td>
                <td>{{ $row["TR_GI_SAPDETAIL_BASE_UOM"] }}</td>
                <td>{{ number_format($row["TR_GI_SAPDETAIL_BASE_QTY"],2) }}</td>
                <td>{{ $row["MA_SLOC_DESC"] }}</td>
            </tr>
            @php
                $count++;
            @endphp
            @endforeach
        </table>
        <br><br>
        <table style="width:20%; border-collapse: collapse; float: left;"> 
            <tr>
                <td>Distribusi</td>
                <td>:</td>
            </tr>
            <tr>
                <td>Putih</td>
                <td>:</td>
                <td>Plant Pengirim</td>
            </tr>
            <tr>
                <td>Merah</td>
                <td>:</td>
                <td>Plant Penerima</td>
            </tr>
            <tr>
                <td>Kuning</td>
                <td>:</td>
                <td>Stock Keeper</td>
            </tr>
            <tr>
                <td>Hijau</td>
                <td>:</td>
                <td>Costing Pengirim</td>
            </tr>
            <tr>
                <td>Biru</td>
                <td>:</td>
                <td>Costing Penerima</td>
            </tr>
        </table>
        <table border=1 style="width:50%; border-collapse: collapse; float: right;"> 
            <tr>
                <td style="text-align:center;">Receipt By,</td>
                <td style="text-align:center;" colspan="2">Approve</td>
            </tr>
            <tr>
                <td height="50" style="vertical-align:bottom; text-align:center;"></td>
                <td height="50" style="vertical-align:bottom; text-align:center;"></td>
                <td height="50" style="vertical-align:bottom; text-align:center;"></td>
            </tr>
            <tr>
                <td style="vertical-align:bottom; text-align:center;">Stock Keeper</td>
                <td style="vertical-align:bottom; text-align:center;">Dept Head</td>
                <td style="vertical-align:bottom; text-align:center;">Dept Head FA</td>
            </tr>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(function () {
            // window.print();
        });
    </script>
</body>
</html>