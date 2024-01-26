@extends('layouts.app')

@section('page_title', 'Stock Report')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/report/print.js') }}"></script>
<script src="{{ asset('custom/library/jquery.rowspanizer.js-master/jquery.rowspanizer.min.js') }}"></script>
<script>
$(function () {
    // $('#report_table').rowspanizer({
    //     vertical_align: 'middle',
    //     columns: [0,1,2]
    // });
});
</script>
@endpush

@section('content')
@if ($open_balance != NULL)
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">

            </span>
            <h3 class="card-label">Stock Report</h3>
        </div>
        <div class="card-toolbar">
            <a class="btn btn-primary font-weight-bolder"style="margin-right:7px;" onclick="javascript:printDiv('printable')">
                <i class="fas fa-print"></i>Print
            </a>
            <a target="_blank" href="{{ route('stock_detail_report_excel', ['plant_code' => $plant_selected,'sloc_code' => $sloc,'material_code' => $mat_code,'start_date' => $start_date,'end_date' => $end_date]) }}" class="btn btn-primary font-weight-bolder" onclick="javascript:false;">
                <i class="fas fa-book"></i>Excel
            </a>              
        </div>
    </div>
    <div class="card-body" id="printable">
        <table border="1" style="width:100%;" id="report_table">
            <tr>
                <td style="text-align:center; padding: 5px;width:10%;"><b>SLoc</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Material Code</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Material Name</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Opening Qty</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Total Receipt Qty</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Total Issued Qty</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Closing Qty</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>SAP Batch</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Expired Date</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Actual Qty</b></td>
            </tr>
            @foreach ($open_balance as $row)
            @php
            if(!empty($row['TR_GR_DETAIL_ID']) && !empty($row_detail[$row['TR_GR_DETAIL_ID']])){
            $in = $row_detail[$row['TR_GR_DETAIL_ID']]['IN'];
            $out = $row_detail[$row['TR_GR_DETAIL_ID']]['OUT'];
            $act = $row["actual_qty"]+$in+$out;
            unset($row_detail[$row['TR_GR_DETAIL_ID']]);
            }else{
            $in = $row["receipt_qty"];
            $out = $row["issued_qty"];
            $act = $row["actual_qty"];
            }
            @endphp
            <tr>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format($row["actual_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format($in,2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format(abs($out), 2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format($act,2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ (empty($row["TR_GR_DETAIL_SAP_BATCH"])?"":$row["TR_GR_DETAIL_SAP_BATCH"]) }}</td>
                <td style="text-align:center; padding: 5px;">{{ convert_to_web_dmy($row["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                @if($row['actual_qty'] > 0)
                <td style="text-align:right; padding: 5px;">{{ number_format($row["actual_qty"], 2)." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                @else
                <td style="text-align:right; padding: 5px;">{{ "-". number_format(abs($row["actual_qty"]), 2)." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                @endif
            </tr>

            @endforeach
            @foreach($row_detail as $dt_detail)
            @php
            $closing = $dt_detail['IN'] + $dt_detail['OUT'];
            @endphp
            <tr>
                <td style="text-align:center; padding: 5px;">{{ $dt_detail["TR_GR_DETAIL_SLOC"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $dt_detail["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $dt_detail["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format(0,2) }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format($dt_detail['IN'],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format(abs($dt_detail['OUT']), 2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format($closing,2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $dt_detail["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ convert_to_web_dmy($dt_detail["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                @if($closing >= 0)
                <td style="text-align:right; padding: 5px;">{{ number_format($closing, 2)." ".$dt_detail["TR_GR_DETAIL_BASE_UOM"] }}</td>
                @else
                <td style="text-align:right; padding: 5px;">{{ "-". number_format(abs($closing), 2)." ".$dt_detail["TR_GR_DETAIL_BASE_UOM"] }}</td>
                @endif                
            </tr>            
            @endforeach
        </table>
    </div>
</div>
@endif
@endsection