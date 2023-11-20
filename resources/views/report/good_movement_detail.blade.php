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
        columns: [0, 1, 2, 3]
    });
});
</script>
@endpush

@section('content')
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
            <a target="_blank" href="{{ route('goods_mvt_detail_excel', ['plant_code' => $plant_selected,'sloc_code' => $sloc_selected,'material_code' => $mat_selected,'start_date' => $sdate,'end_date' => $edate]) }}" class="btn btn-primary font-weight-bolder" onclick="javascript:false;">
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
                <td style="text-align:center; padding: 5px;width:10%;"><b>Expired Date</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Batch ID</b></td>

                <td style="text-align:center; padding: 5px;width:10%;"><b>Qty</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Status</b></td>
                <!--{{-- <td style="text-align:center; padding: 5px;width:10%;"><b>Material Document SAP</b></td> --}}-->
                <td style="text-align:center; padding: 5px;width:10%;"><b>Movement Type</b></td>
                <td style="text-align:center; padding: 5px;width:10%;"><b>Transaction Date</b></td>
            </tr>
            @foreach ($data as $row)
            <tr>
                <td style="text-align:center; padding: 5px;">{{ $row["MA_SLOC_CODE"] }}</td>

                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_CODE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ convert_to_web_dmy($row["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>

                <td style="text-align:right; padding: 5px;">{{ number_format(abs($row["LG_MATERIAL_QTY"]), 2)." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                <td style="text-align:center; padding: 5px;">
                    @if ($row["LG_MATERIAL_QTY"] >= 0)
                    IN
                    @else
                    OUT
                    @endif
                </td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_MVT_TYPE"] }}</td>
                <td style="text-align:center; padding: 5px;">{{ $row["LG_MATERIAL_CREATED_TIMESTAMP"] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif
@endsection