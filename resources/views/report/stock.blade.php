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
<div class="card card-custom gutter-b">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <form class="form" id="form" method="GET" action="">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-3">
                    <label>Date: </label>
                    <input type="text" class="form-control date" name="date" value="{{ $date }}">
                </div>
                <div class="col-lg-3">
                    <label>Plant:</label>
                    <select class="form-control" id="plant_select2" name="plant_code">
                        <option></option>
                        @foreach ($plant as $row)
                            <option value="{{ $row["MA_PLANT_CODE"] }}" 
                            @if ($row["MA_PLANT_CODE"] == $plant_selected)
                                selected
                            @endif>{{ $row["MA_PLANT_CODE"] }} - {{ $row["MA_PLANT_NAME"] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i>Filter</button>
                </div>
            </div>
        </div>
    </form>
</div>
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
        </div>
    </div>
    <div class="card-body" id="printable">
        <table border="1" style="width:100%;" id="report_table">
            <tr>
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
                @endphp
                @foreach ($row["gr_detail"] as $gr_detail)
                <tr>
                    @if ($flag == 0)
                    <td style="text-align:center; padding: 5px;" rowspan="{{count($row['gr_detail'])}}">{{ $row["LG_MATERIAL_CODE"] }}</td>
                    <td style="text-align:center; padding: 5px;" rowspan="{{count($row['gr_detail'])}}">{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{count($row['gr_detail'])}}">{{ number_format($row["actual_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{count($row['gr_detail'])}}">{{ number_format($row["receipt_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{count($row['gr_detail'])}}">{{ number_format(abs($row["issued_qty"]), 2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    <td style="text-align:right; padding: 5px;" rowspan="{{count($row['gr_detail'])}}">{{ number_format($row["closing_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                    @php
                        $flag++;
                    @endphp
                    @endif
                    
                    <td style="text-align:center; padding: 5px;">{{ $gr_detail["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                    <td style="text-align:center; padding: 5px;">{{ convert_to_web_dmy($gr_detail["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                    <td style="text-align:right; padding: 5px;">{{ number_format($gr_detail["TR_GR_DETAIL_LEFT_QTY"], 2)." ".$gr_detail["TR_GR_DETAIL_BASE_UOM"] }}</td>
                </tr>
                @endforeach
            
            @endforeach
        </table>
    </div>
</div>
@endif
@endsection