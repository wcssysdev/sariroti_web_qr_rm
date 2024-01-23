@extends('layouts.app')

@section('page_title', 'Stock Report')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/report/print.js') }}"></script>
<script src="{{ asset('custom/library/jquery.rowspanizer.js-master/jquery.rowspanizer.min.js') }}"></script>
<script>
$(function () {
    $('#report_table').rowspanizer({
        vertical_align: 'middle',
        columns: [0, 1, 2]
    });
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
                <div class="col-lg-4">
                    <label>Date: </label>
                    <div class="input-daterange input-group" id="kt_datepicker_5">
                        <input type="text" class="form-control date" name="start_date" autocomplete="off"
                               placeholder="Start" value="{{ $start_date }}">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                -
                            </span>
                        </div>
                        <input type="text" class="form-control date" name="end_date" autocomplete="off"
                               placeholder="End" value="{{ $end_date }}">
                    </div>
                </div>
                <div class="col-lg-4">
                    <label>Plant:</label>
                    <select class="form-control" id="plant_select2" name="plant_code">
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
            <a class="btn btn-primary font-weight-bolder" style="margin-right:7px;" onclick="javascript:printDiv('printable')">
                <i class="fas fa-print"></i>Print
            </a>
            <a target="_blank" href="{{ route('stock_report_excel', ['plant_code' => $plant_selected,'start_date' => htmlentities($start_date),'end_date' => htmlentities($end_date)]) }}" class="btn btn-primary font-weight-bolder" onclick="javascript:false;">
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
                <td style="text-align:center; padding: 5px;width:10%;"><b>Action</b></td>
            </tr>
            @foreach ($open_balance as $row)
            @php
            if(empty($row["mat_name"])){
            $matname = "";
            }else{
            $matname = $row["mat_name"];
            }
            $sloc = "";
            if(!empty($row["TR_GR_DETAIL_SLOC"])){
                $sloc = $row["TR_GR_DETAIL_SLOC"];
            }elseif(!empty($row["gr_detail"][0]["TR_GR_DETAIL_SLOC"])){
                $sloc = $row["gr_detail"][0]["TR_GR_DETAIL_SLOC"];
            }
            $flag = 0;
            @endphp
            <tr>
                <td style="text-align:center; padding: 5px;">{{ $sloc }}</td>
                <td style="text-align:center; padding: 5px;" rowspan="1">{{ $row["LG_MATERIAL_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;" rowspan="1">{{ $matname }}</td>
                <td style="text-align:right; padding: 5px;" rowspan="1">{{ number_format($row["actual_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;" rowspan="1">{{ number_format($row["receipt_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;" rowspan="1">{{ number_format(abs($row["issued_qty"]), 2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td style="text-align:right; padding: 5px;" rowspan="1">{{ number_format($row["closing_qty"],2)." ".$row["LG_MATERIAL_UOM"] }}</td>
                <td nowrap="nowrap" style="text-align:center; padding: 5px;">
                    <a href="{{ route('stock_detail_report_view', ['plant_code' => $plant_selected,'sloc_code' => $sloc,'material_code' => $row['LG_MATERIAL_CODE'],'start_date' => ($start_date),'end_date' => ($end_date)]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                            class="la la-eye"></i>
                    </a>
                </td>                    
            </tr>

            @endforeach
        </table>
    </div>
</div>
@endif
@endsection