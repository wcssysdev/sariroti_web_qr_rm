<html>
    <p>Kepada PPIC Plant {{$plant}},</p>
    <p>Terlampir list material yang sudah mau mencapai masa kadaluarsa:</p>
    <hr>
    <table border="1">
        <tr>
            <th>No.</th>
            <th>Material Code</th>
            <th>Material Name</th>
            <th>Batch Code</th>
            <th>Expired Date</th>
            <th>Qty</th>
        </tr>
        @php
            $counter = 1;
        @endphp
        @foreach ($gr as $row)
        <tr>
            <td>{{$counter}}</td>
            <td>{{$row["TR_GR_DETAIL_MATERIAL_CODE"]}}</td>
            <td>{{$row["TR_GR_DETAIL_MATERIAL_NAME"]}}</td>
            <td>{{$row["TR_GR_DETAIL_SAP_BATCH"]}}</td>
            <td>{{$row["TR_GR_DETAIL_EXP_DATE"]}}</td>
            <td style="text-align:right;">{{number_format($row["TR_GR_DETAIL_LEFT_QTY"])}} {{$row["TR_GR_DETAIL_BASE_UOM"]}}</td>
        </tr>
        @php
            $counter++;
        @endphp
        @endforeach
    </table>
    <hr>

    <small>Email ini digenerate secara otomatis oleh system GI GR Scanner pada {{ date("Y-m-d H:i:s")}}, Mohon untuk tidak membalas email ini.</small>
</html>