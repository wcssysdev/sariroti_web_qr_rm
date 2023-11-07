@extends('layouts.app')

@section('page_title', 'Detail PO')

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
                <h2>PO Header:</h2>
                <div class="form-group row d-flex justify-content-between pt-6">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">PO Number: </span>
                        <span class="opacity-70">{{$header_data['TR_PO_HEADER_NUMBER']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Vendor: </span>
                        <span class="opacity-70">{{$header_data['TR_PO_HEADER_VENDOR']}}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">PO Document Type: </span>
                        <span class="opacity-70">{{$header_data['TR_PO_HEADER_TYPE']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Supplying Plant: </span>
                        <span class="opacity-70">{{$header_data['TR_PO_HEADER_SUP_PLANT']}}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">PO Created By: </span>
                        <span class="opacity-70">{{$header_data['TR_PO_HEADER_SAP_CREATED_BY']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">PO Created Time: </span>
                        <span class="opacity-70">{{$header_data['TR_PO_HEADER_SAP_CREATED_DATE']}}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
            <div class="col-md-11">
                <h2>PO Material:</h2>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable1">
                        <thead>
                            <tr>
                                <td>Line Number</td>
                                <td>Material Code</td>
                                <td>Material Text</td>
                                <td>QTY</td>
                                <td>QTY Delivered</td>
                                <td>QTY Left</td>
                                <td>UOM</td>
                                <td>Storage Location</td>
                                <td>Plant</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_data as $row)
                            <tr>
                                <td>{{ $row["TR_PO_DETAIL_MATERIAL_LINE_NUM"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_MATERIAL_CODE"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_MATERIAL_NAME"] }}</td>
                                <td align="right">{{ number_format($row["TR_PO_DETAIL_QTY_ORDER"]) }}</td>
                                <td align="right">{{ number_format($row["TR_PO_DETAIL_QTY_DELIV"]) }}</td>
                                <td align="right">{{ number_format($row["TR_PO_DETAIL_QTY_ORDER"] - $row["TR_PO_DETAIL_QTY_DELIV"]) }}
                                </td>
                                <td>{{ $row["TR_PO_DETAIL_UOM"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_SLOC"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_PLANT_RCV"] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-1">
                <div class="d-flex justify-content-between">
                    <a href="{{ route("purchase_order_master_view") }}" class="btn btn-secondary"><i
                            class="fas fa-angle-double-left"></i> Back</a>
                </div>
            </div>
            <div class="col-md-10">
            </div>
        </div>
    </div>
</div>
@endsection