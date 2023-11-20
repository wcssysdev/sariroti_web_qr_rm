@extends('layouts.app')

@section('page_title', 'Good Movement Report')

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
                               placeholder="Start" value="{{ $start }}">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                -
                            </span>
                        </div>
                        <input type="text" class="form-control date" name="end_date" autocomplete="off"
                               placeholder="End" value="{{ $end }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label>Plant:</label>
                    <select class="form-control" id="plant_select2" name="plant_code">
                        <option></option>
                        @foreach ($plant as $row)
                        <option value="{{ $row["MA_PLANT_CODE"] }}"
                                @if ($row["MA_PLANT_CODE"] == $plant_selected)
                                selected
                                @endif
                                >{{ $row["MA_PLANT_CODE"] }} - {{ $row["MA_PLANT_NAME"] }}</option>
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
@if ($data != NULL)
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">

            </span>
            <h3 class="card-label">Good Movement Report</h3>
        </div>
        <div class="card-toolbar">
            <a class="btn btn-primary font-weight-bolder" onclick="javascript:printDiv('printable')">
                <i class="fas fa-print"></i>Print
            </a>
            <a target="_blank" href="{{ route('goods_mvt_excel', ['plant_code' => $plant_selected,'start_date' => $start,'end_date' => $end]) }}" class="btn btn-primary font-weight-bolder" onclick="javascript:false;">
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
                <td style="text-align:center; padding: 5px;width:10%;"><b>Status</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Qty</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Action</b></td>
            </tr>
            @foreach ($data as $row)
            <tr>
                <td style="text-align:center; padding: 5px;">{{ $row["MA_SLOC_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                <td style="text-align:center; padding: 5px;">
                    @if ($row["SUM_QTY"] >= 0)
                    IN
                    @else
                    OUT
                    @endif</td>                
                <td style="text-align:right; padding: 5px;">{{ number_format(abs($row["SUM_QTY"]), 2) }}</td>
                <td nowrap="nowrap" style="text-align:center; padding: 5px;">
                    <a href="{{ route('goods_mvt_detail', ['plant_code' => $plant_selected,'sloc_code' => $row['MA_SLOC_CODE'],'material_code' => $row['LG_MATERIAL_CODE'],'start_date' => $start,'end_date' => $end]) }}" class="btn btn-sm btn-clean btn-icon"> <i
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