@extends('layouts.app')

@section('page_title', 'Good Movement Report')

@push('styles')
@endpush

@push('scripts')
<script src="{{ asset('custom/js/report/print.js') }}"></script>
<script src="{{ asset('custom/library/jquery.rowspanizer.js-master/jquery.rowspanizer.min.js') }}"></script>
<!--<script>
$(function () {
    $('#report_table').rowspanizer({
        vertical_align: 'middle',
        columns: [0, 1, 2, 3]
    });
});
</script>-->
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
            <a class="btn btn-primary font-weight-bolder" style="margin-right:7px;" onclick="javascript:printDiv('printable')">
                <i class="fas fa-print"></i>Print
            </a>
            <a target="_blank" href="{{ route('goods_mvt_excel', ['plant_code' => $plant_selected,'sloc_code' => '','material_code' => '','start_date' => htmlentities($start),'end_date' => htmlentities($end)]) }}" class="btn btn-primary font-weight-bolder" onclick="javascript:false;">
                <i class="fas fa-book"></i>Excel
            </a>
        </div>
    </div>
    <div class="card-body" id="printable">
        <table border="1" style="width:100%;" id="report_table">
            <tr>

                <td style="text-align:center; padding: 5px;width:10%;"><b>Material</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Material Desc</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Quantity in Unit Entry</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Entri Unit</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Posting Date</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Doc. Date</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>PO</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Plant</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Batch</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Sloc</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Mvt Type</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Mat. Doc.</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Entry Date</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Time</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>User</b></td>


            </tr>
            @foreach ($data as $row)
            <tr>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                <td style="text-align:right; padding: 5px;">{{ number_format($row["LG_MATERIAL_QTY"], 2)}}</td>
                <td style="text-align:right; padding: 5px;">{{ $row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_POSTING_DATE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_HEADER_DOC_DATE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_HEADER_PO_NUMBER"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_PLANT_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["MA_SLOC_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_MVT_TYPE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_HEADER_SAP_DOC"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["ENTRY_DATE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["ENTRY_TIME"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["MA_USRACC_FULL_NAME"] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif
@endsection