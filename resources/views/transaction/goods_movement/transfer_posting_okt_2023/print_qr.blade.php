<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Transfer Posting Detail QR</title>
    <style>
        .rotate90 {
            transform: rotate(270deg);
            transform-origin: 50% 35%;
            -ms-transform: rotate(270deg);
            /* IE 9 */
            -ms-transform-origin: 50% 35%;
            /* IE 9 */
            -webkit-transform: rotate(270deg);
            /* Safari and Chrome */
            -webkit-transform-origin: 50% 35%;
            /* Safari and Chrome */
            -moz-transform: rotate(270deg);
            /* Firefox */
            -moz-transform-origin: 50% 35%;
            /* Firefox */
            -o-transform: rotate(270deg);
            /* Opera */
            -o-transform-origin: 50% 35%;
            /* Opera */
        }
    </style>
</head>
<body>
    <div id="qrcode" class="rotate90" style="max-width: 7cm; max-height: 10cm; border-style: solid; text-align: center; padding:10px;">
        {!! QrCode::size(200)->generate($data["TR_TP_DETAIL_QR_CODE_NUMBER"]); !!}
        <h4>{{$data["TR_TP_DETAIL_QR_CODE_NUMBER"]}}</h4>
        <table width="100%" style="font-size: 12px;">
            <tr>
                <td><b>Material</b></td>
                <td>{{$data["TR_TP_DETAIL_MATERIAL_CODE"]." - ".$data["TR_TP_DETAIL_MATERIAL_NAME"]}}</td>
            </tr>
            <tr>
                <td><b>Quantity</b></td>
                <td>{{number_format($data["TR_TP_DETAIL_QTY"])." ".$data["TR_TP_DETAIL_UOM"]}}</td>
            </tr>
            <tr>
                <td><b>S.Loc</b></td>
                <td>{{$data["TR_TP_DETAIL_SLOC"]}}</td>
            </tr>
            <tr>
                <td><b>Exp Date</b></td>
                <td>{{$data["TR_GR_DETAIL_EXP_DATE"]}}</td>
            </tr>
            <tr>
                <td><b>Batch Code</b></td>
                <td>{{$data["TR_TP_DETAIL_SAP_BATCH"]}}</td>
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