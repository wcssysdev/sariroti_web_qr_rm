@extends('layouts.app')

@section('page_title', 'Detail Material Stock Opname')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/detail/datatable.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom overflow-hidden">
    <div class="card-body p-0">
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-11">
                <h2>PID Detail:</h2>
                <div class="form-group row d-flex justify-content-between pt-6">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Line Material: </span>
                        <span class="opacity-70">{{ $header_data['TR_PID_DETAIL_LINE_MATERIAL'] }}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SAP Batch: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_DETAIL_MATERIAL_SAP_BATCH']}}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Material Code: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_DETAIL_MATERIAL_CODE']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Material Name: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_DETAIL_MATERIAL_NAME']}}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <br>
        <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
            <div class="col-md-11">
                <h2>PID Detail Material:</h2>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable1">
                        <thead>
                            <tr>
                                <td>GR Detail ID</td>
                                <td>Material Code</td>
                                <td>Material Text</td>
                                <td>SAP Batch No</td>
                                <td>QTY Before</td>
                                <td>QTY After</td>
                                <td>Difference</td>
                                <td>UOM</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_data as $row)
                            <tr>
                                <td>{{ $row["TR_PID_DETAIL_GR_DETAIL_ID"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                <td align="right">{{ number_format($row["TR_GR_DETAIL_LEFT_QTY"]) }}</td>
                                <td align="right">{{ number_format($row["TR_PID_DETAIL_UPDATED_QTY"]) }}</td>
                                <td align="right">{{ number_format($row["TR_GR_DETAIL_LEFT_QTY"]-$row["TR_PID_DETAIL_UPDATED_QTY"]) }}</td>
                                <td>{{ $row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection