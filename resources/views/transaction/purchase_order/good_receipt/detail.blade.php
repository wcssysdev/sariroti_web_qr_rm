@extends('layouts.app')

@section('page_title', 'Detail PO - GR')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/detail/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/delete/submit_ajax.js?=1') }}"></script>
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
                        <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_PO_HEADER_SAP_CREATED_DATE']) }}</span>
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
                                <td>QTY Ordered</td>
                                <td>QTY Delivered</td>
                                <td>Remaining Unreceived Qty</td>
                                <td>Storage Location</td>
                                <td>Plant</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allow_to_create_gr = false;
                            @endphp
                            @foreach ($detail_data as $row)
                            <tr>
                                <td>{{ $row["TR_PO_DETAIL_MATERIAL_LINE_NUM"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_MATERIAL_CODE"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_MATERIAL_NAME"] }}</td>
                                <td align="right">{{ number_format($row["TR_PO_DETAIL_QTY_ORDER"],2)." ".$row["TR_PO_DETAIL_UOM"] }}</td>
                                <td align="right">{{ number_format($row["TR_PO_DETAIL_QTY_DELIV"],2)." ".$row["TR_PO_DETAIL_UOM"] }}</td>
                                <td align="right">{{ number_format(($row["TR_PO_DETAIL_QTY_ORDER"] - $row["TR_PO_DETAIL_QTY_DELIV"]),2)." ".$row["TR_PO_DETAIL_UOM"] }}
                                @if ($row["TR_PO_DETAIL_QTY_ORDER"] - $row["TR_PO_DETAIL_QTY_DELIV"] > 0)
                                    @php
                                        $allow_to_create_gr = true;
                                    @endphp
                                @endif
                                </td>
                                <td>{{ $row["TR_PO_DETAIL_SLOC"] }}</td>
                                <td>{{ $row["TR_PO_DETAIL_PLANT_RCV"] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
            <div class="col-md-11">
                <div class="form-group row pt-6">
                    <div class="col-lg-10">
                        <h2 class="card-label">Goods Receipt:</h2>
                    </div>
                    <div class="col-lg-2">
                        @if (session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 5)
                            @if ($allow_to_create_gr === true)
                            <a href="{{ route('purchase_order_good_receipt_add', ['gr_po_number' => $header_data["TR_PO_HEADER_NUMBER"]]) }}"
                                class="btn btn-primary font-weight-bolder"><span class="fas fa-plus-circle"></span>&nbsp;Create GR</a>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable2">
                        <thead>
                            <tr>
                                <td>Document No</td>
                                <td>Material Doc SAP</td>
                                <td>Posting Date</td>
                                <td>Receipt</td>
                                <td>Plant</td>
                                <td>Created Date</td>
                                <td>Integration Status</td>
                                <td>Error Message</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gr_data as $row)
                            <tr>
                                <td>{{ $row["TR_GR_HEADER_ID"] }}</td>
                                <td>{{ $row["TR_GR_HEADER_SAP_DOC"] }}</td>
                                <td>{{ convert_to_web_dmy($row["TR_GR_HEADER_PSTG_DATE"]) }}</td>
                                <td>{{ $row["TR_GR_HEADER_RECIPIENT"] }}</td>
                                <td>{{ $row["TR_GR_HEADER_PLANT_CODE"] }}</td>
                                <td>{{ $row["TR_GR_HEADER_DOC_DATE"] }}</td>
                                <td>{{ $row["TR_GR_HEADER_STATUS"] }} </td>
                                <td>{{ $row["TR_GR_HEADER_ERROR"]}} </td>
                                <td nowrap="nowrap">
                                    <a href="{{ route('purchase_order_good_receipt_detail_detail', ['gr_header_id' => $row["TR_GR_HEADER_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-eye"></i>
                                    </a>
                                    <a href="{{ route('purchase_order_good_receipt_detail_print', ['gr_header_id' => $row["TR_GR_HEADER_ID"]]) }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-print"></i>
                                    </a>
                                    @if ($row["TR_GR_HEADER_STATUS"] == "ERROR")
                                    <a data-action="{{ route('purchase_order_good_receipt_detail_delete', ['gr_header_id' => $row["TR_GR_HEADER_ID"]]) }}"
                                    data-form-success-redirect="{{ route('purchase_order_good_receipt_view') }}"
                                    class="btn btn-sm btn-clean btn-icon" id="delete_transaction_button"> <i
                                    class="la la-trash"></i>
                                    </a>
                                    @endif
                                </td>
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
                    <a href="{{ route("purchase_order_good_receipt_view") }}" class="btn btn-secondary"><i
                            class="fas fa-angle-double-left"></i> Back</a>
                </div>
            </div>
            <div class="col-md-10">
            </div>
        </div>
    </div>
</div>
@include('modals.transaction.gi_sto.detail')
@endsection