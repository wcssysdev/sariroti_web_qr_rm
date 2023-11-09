<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Transfer Posting Detail</title>
    <style>
        @page {
            size: 7in 9.25in;
            margin: 27mm 0mm 27mm 0mm;
        }
        th,td{
            font-size:12px;
        }
    </style>
</head>
<body>
    <div>
        <h2 style="text-align: center; font-size:14px;">TRANSFER SLIP</h2>
        <table style="width:30%;">
            <tr>
                <td>No IS</td>
                <td>:</td>
                <td>{{ $header_data["TR_TP_HEADER_ID"] }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>:</td>
                <td>{{ $header_data["TR_TP_HEADER_CREATED_TIMESTAMP"] }}</td>
            </tr>
        </table>
        <br>
        <table border=1 style="width:100%; border-collapse: collapse;"> 
            <tr>
                <th>No.</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>From</th>
                <th>To</th>
                <th>Remarks</th>
            </tr>
            @php
                $count = 1;
            @endphp
            @foreach ($detail_data as $row)
            <tr>
                <td>{{ $count }}</td>
                <td>{{ $row["TR_TP_DETAIL_MATERIAL_CODE"] }}</td>
                <td>{{ $row["TR_TP_DETAIL_MATERIAL_NAME"] }}</td>
                <td>{{ $row["TR_TP_DETAIL_BASE_UOM"] }}</td>
                <td>{{ number_format($row["TR_TP_DETAIL_BASE_QTY"],2) }}</td>
                <td>{{ $row["sloc_from"] }}</td>
                <td>{{ $row["sloc_to"] }}</td>
                <td>{{ $row["TR_TP_DETAIL_NOTES"] }}</td>
            </tr>
            @php
                $count++;
            @endphp
            @endforeach
        </table>
        <br><br>
        <table border=1 style="width:50%; border-collapse: collapse; float: right;"> 
            <tr>
                <td style="text-align:center;">Disetujui,</td>
                <td style="text-align:center;">Diterima,</td>
                <td style="text-align:center;">Disetujui,</td>
                <td style="text-align:center;">Diserahkan,</td>
            </tr>
            <tr>
                <td height="50" style="vertical-align:bottom; text-align:center;">SL Prod</td>
                <td height="50" style="vertical-align:bottom; text-align:center;">Admin Prod</td>
                <td height="50" style="vertical-align:bottom; text-align:center;">SL RM</td>
                <td height="50" style="vertical-align:bottom; text-align:center;">Admin RM</td>
            </tr>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(function () {
            window.print();
        });
    </script>
</body>
</html>