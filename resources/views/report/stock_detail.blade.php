@extends('layouts.app')

@section('page_title', 'Stock Report')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/report/print.js') }}"></script>
<script src="{{ asset('custom/library/jquery.rowspanizer.js-master/jquery.rowspanizer.min.js') }}"></script>
<!--<script>
$(function () {
     $('#report_table').rowspanizer({
         vertical_align: 'middle',
         columns: [0,1,2,3]
     });
});
</script>-->
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
            <a class="btn btn-primary font-weight-bolder" onclick="javascript:printDiv('printable')">
                <i class="fas fa-print"></i>Print
            </a>
            <a target="_blank" href="{{ route('stock_detail_report_excel', ['plant_code' => $plant_selected,'sloc_code' => $sloc,'material_code' => $mat_code,'date' => $date]) }}" class="btn btn-primary font-weight-bolder" onclick="javascript:false;">
                <i class="fas fa-book"></i>Excel
            </a>              
        </div>
    </div>
    <div class="card-body" id="printable">
        <table border="1" style="width:100%;" id="report_table">
            <tr>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Storage Location</b></td>
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
            $flag = 0;
            $mat_name = '';
            $rowcount = count($row['gr_detail']);
            if($rowcount < 1){
            $rowcount = 1;
            }
            @endphp            
                <tr>
                    <td style="text-align:center; padding: 5px;" rowspan="{{$rowcount}}">{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                    <td style="text-align:center; padding: 5px;" rowspan="{{$rowcount}}">{{ $row["LG_MATERIAL_CODE"] }}</td>
                    <td style="text-align:center; padding: 5px;" rowspan="{{$rowcount}}">{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{$rowcount}}">{{ number_format($row["actual_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{$rowcount}}">{{ number_format($row["receipt_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{$rowcount}}">{{ number_format(abs($row["issued_qty"]), 2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{$rowcount}}">{{ number_format($row["closing_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    @if($flag == 0)
                    @if(empty($row["gr_detail"]))
                    <td style="text-align:center; padding: 5px;"></td>
                    <td style="text-align:center; padding: 5px;"></td>
                    <td style="text-align:center; padding: 5px;"></td>
                    @else
                    @php
                    $gr_detail0 = $row["gr_detail"][0];
                    unset($row["gr_detail"][0]);
                    $flag++;
                    @endphp
                    <td style="text-align:center; padding: 5px;">{{ $gr_detail0["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                    <td style="text-align:center; padding: 5px;">{{ convert_to_web_dmy($gr_detail0["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                    <td style="text-align:right; padding: 5px;">{{ number_format($gr_detail0["TR_GR_DETAIL_LEFT_QTY"], 2)." ".$gr_detail0["TR_GR_DETAIL_BASE_UOM"] }}</td>
                    @endif
                    @else
                    <td style="text-align:center; padding: 5px;"></td>
                    <td style="text-align:center; padding: 5px;"></td>
                    <td style="text-align:center; padding: 5px;"></td>
                    @endif
                </tr>
<!--                @if($row["gr_detail"])
                @foreach ($row["gr_detail"] as $gr_detail)
                <tr>
                    <td style="text-align:center; padding: 5px;">{{ $gr_detail["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                    <td style="text-align:center; padding: 5px;">{{ convert_to_web_dmy($gr_detail["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                    <td style="text-align:right; padding: 5px;">{{ number_format($gr_detail["TR_GR_DETAIL_LEFT_QTY"], 2)." ".$gr_detail["TR_GR_DETAIL_BASE_UOM"] }}</td>
                </tr>
                @endforeach
                @endif-->
            
            @endforeach
        </table>
    </div>
</div>
@endif
@endsection