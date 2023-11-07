<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print GR Detail</title>
    <style>
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
		        size: 8.5in 5.5in; /* . Random dot? */;
		    }
		}
    </style>
</head>
<body>
    <div>
        <table width="100%">
            <tr>
                <td><h5 style="text-align:left;">PT. Nippon Indosari Corpindo, Tbk</h5></td>
                <td><h5 style="text-align:right;">Copy: {{ number_format($header_data["TR_GR_HEADER_PRINT_COUNT"]) }}</h5></td>
            </tr>
        </table>
        <h2 style="text-align: center; font-size:14px;">RECEIVING SLIP</h2>
        <table style="width:100%;">
            <tr>
                <td>No. RC</td>
                <td>:</td>
                <td>{{ $header_data["TR_GR_HEADER_ID"] }}</td>
                <td style="width:30%"></td>
                <td>No. PO</td>
                <td>:</td>
                <td>{{ $header_data["TR_GR_HEADER_PO_NUMBER"] }}</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>:</td>
                <td>{{$user_data["MA_USRACC_FULL_NAME"]}}</td>
                <td style="width:30%"></td>
                <td>Date</td>
                <td>:</td>
                <td>{{ $header_data["TR_PO_HEADER_SAP_CREATED_DATE"] }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>:</td>
                <td>{{ $header_data["TR_GR_HEADER_CREATED_TIMESTAMP"] }}</td>
            </tr>
            <tr>
                <td>Vendor</td>
                <td>:</td>
                <td>{{ $header_data["TR_PO_HEADER_VENDOR"]." - ".$header_data["MA_VENDOR_NAME"] }}</td>
            </tr>
            <tr>
                <td>Surat jalan</td>
                <td>:</td>
                <td>{{ $header_data["TR_GR_HEADER_BOL"] }}</td>
            </tr>
        </table>
        <br>
        <table border=1 style="width:100%; border-collapse: collapse;"> 
            <tr>
                <th>No.</th>
                <th>Item Code</th>
                <th>Specification</th>
                <th>Unit</th>
                <th>Qty Received</th>
                <th>Outst. PO</th>
                <th>Location</th>
            </tr>
            @php
                $count = 1;
            @endphp
            @foreach ($detail_data as $row)
            <tr>
                <td>{{ $count }}</td>
                <td>{{ $row["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                <td>{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                <td>{{ $row["TR_GR_DETAIL_UOM"] }}</td>
                <td>{{ number_format($row["TR_GR_DETAIL_QTY"],2) }}</td>
                <td>{{ number_format(($row["TR_PO_DETAIL_QTY_ORDER"] - $row["TR_PO_DETAIL_QTY_DELIV"]),2) }}</td>
                <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
            </tr>
            @php
                $count++;
            @endphp
            @endforeach
        </table>
        <br><br>
        <table border=1 style="width:100%; border-collapse: collapse;"> 
            <tr>
                <td style="width:45%;">Distribusi : Asli (Supplier - Lampiran Tagihan), Copy Asli (File Gudang/User)</td>
                <td>Receipt by,</td>
                <td>Acknowledge by,</td>
                <td>Quality Control</td>
            </tr>
            <tr>
                <td rowspan="3">
                    <table>
                        <tr>
                            <td>Note</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td>Receipt</td>
                            <td>:</td>
                            <td>Penerima</td>
                        </tr>
                        <tr>
                            <td>Acknowledge</td>
                            <td>:</td>
                            <td>Superior</td>
                        </tr>
                        <tr>
                            <td>Quality Control</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>RM</td>
                            <td>:</td>
                            <td>QC RM</td>
                        </tr>
                        <tr>
                            <td>A & P</td>
                            <td>:</td>
                            <td>Marketing</td>
                        </tr>
                        <tr>
                            <td>Others</td>
                            <td>:</td>
                            <td>Dept. Head (Cukup Tanda Tangan di kolom QC)</td>
                        </tr>
                    </table>
                </td>
                <td height="100;">&nbsp;</td>
                <td height="100;"></td>
                <td height="100;"></td>
            </tr>
            <tr>
                <td >Name:<br>Date:</td>
                <td >Name:<br>Date:</td>
                <td >Name:<br>Date:</td>
            </tr>
            <tr>
                <td colspan="3">*) Wajib distempel Departemen/GA</td>
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