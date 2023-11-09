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
                        <span class="opacity-70">{{convert_to_web_dmy($header_data['TR_PO_HEADER_SAP_CREATED_DATE'])}}</span>
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
                                {{-- <td>QTY Issued</td> --}}
                                {{-- <td>QTY Left</td> --}}
                                {{-- <td>UOM</td> --}}
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
                                <td align="right">{{ number_format($row["TR_PO_DETAIL_QTY_ORDER"],2)." ".$row["TR_PO_DETAIL_UOM"] }}</td>
                                {{-- <td align="right">{{ $row["TR_PO_DETAIL_QTY_DELIV"]." ".$row["TR_PO_DETAIL_UOM"] }}</td> --}}
                                {{-- <td align="right">{{ $row["TR_PO_DETAIL_QTY_ORDER"] - $row["TR_PO_DETAIL_QTY_DELIV"] }} --}}
                                </td>
                                {{-- <td>{{ $row["TR_PO_DETAIL_UOM"] }}</td> --}}
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
                        <h2 class="card-label">Goods Issue:</h2>
                    </div>
                    @if (session('user_role') == 2)
                    @if (count($gi_data) == 0)
                        <div class="col-lg-2">
                            <a href="{{ route('purchase_order_good_issue_add', ['gi_po_number' => $header_data["TR_PO_HEADER_NUMBER"]]) }}" class="btn btn-primary font-weight-bolder"><span class="fas fa-plus-circle"></span>&nbsp;Create Good Issue</a>
                        </div>
                    @endif
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable2">
                        <thead>
                            <tr>
                                <td>Document No</td>
                                <td>Material Doc SAP</td>
                                <td>Posting Date</td>
                                {{-- <td>Receipt</td> --}}
                                <td>Plant</td>
                                <td>Created Date</td>
                                <td>Integration Status</td>
                                <td>Error</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gi_data as $row)
                            <tr>
                                <td>{{ $row["TR_GI_SAPHEADER_ID"] }}</td>
                                <td>{{ $row["TR_GI_SAPHEADER_SAP_DOC"] }}</td>
                                <td>{{ convert_to_web_dmy($row["TR_GI_SAPHEADER_PSTG_DATE"]) }}</td>
                                {{-- <td>{{ $row["TR_GR_HEADER_RECIPIENT"] }}</td> --}}
                                <td>{{ $row["TR_GI_SAPHEADER_PLANT_CODE"] }}</td>
                                <td>{{ convert_to_web_dmy($row["TR_GI_SAPHEADER_DOC_DATE"]) }}</td>
                                <td>{{ $row["TR_GI_SAPHEADER_STATUS"]}} </td>
                                <td>{{ $row["TR_GI_SAPHEADER_ERROR"]}} </td>
                                <td nowrap="nowrap">
                                    <a href="{{ route('purchase_order_good_issue_detail_detail', ['gi_header_id' => $row["TR_GI_SAPHEADER_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-eye"></i>
                                    </a>
                                    <a href="{{ route('purchase_order_good_issue_detail_print', ['gi_header_id' => $row["TR_GI_SAPHEADER_ID"]]) }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-print"></i>
                                    </a>
                                    @if ($row["TR_GI_SAPHEADER_STATUS"] == "ERROR")
                                    <a data-action="{{ route('purchase_order_good_issue_detail_delete', ['gi_header_id' => $row["TR_GI_SAPHEADER_ID"]]) }}"
                                    data-form-success-redirect="{{ route('purchase_order_good_issue_view') }}"
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
                    <a href="{{ route("purchase_order_good_issue_view") }}" class="btn btn-secondary"><i
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