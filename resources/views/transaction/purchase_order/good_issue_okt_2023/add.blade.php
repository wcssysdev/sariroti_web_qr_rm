@extends('layouts.app')

@section('page_title', 'Create New GI')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/purchase_order/good_issue/add/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/good_issue/add/script.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/good_issue/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<form action="{{ route('purchase_order_good_issue_save') }}" method="POST"
    data-get-materials-url="{{ route('purchase_order_good_issue_add_get_materials') }}"
    data-get-material-status-url="{{ route('purchase_order_good_issue_add_get_material_status') }}" data-get-material-gr-url="{{ route('purchase_order_good_issue_add_get_material_gr') }}"
    data-save-material-url="{{ route('purchase_order_good_issue_save_material') }}"
    data-delete-material-url="{{ route('purchase_order_good_issue_delete_material') }}"
    data-form-success-redirect="{{ route('purchase_order_good_issue_view') }}" id="form">
    @csrf
    <input type="hidden" name="po_number" id="po_number" value="{{$po_number}}">
    <input type="hidden" name="uniqid" id="uniqid">
    <div class="card card-custom overflow-hidden">
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Create New Good Issue:</h2>
                    <hr>
                    <br>
                    <h3>PO Data:</h3>
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

                    <hr>
                    <h3>PO Material:</h3>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable kt_datatable1">
                            <thead>
                                <tr>
                                    <td>Line Number</td>
                                    <td>Material Code</td>
                                    <td>Material Text</td>
                                    <td>QTY Ordered</td>
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
                                    </td>
                                    <td>{{ $row["TR_PO_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_PO_DETAIL_PLANT_RCV"] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <br>
                    <h3>Good Issue Header:</h3>
                    <br>
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label>Material Doc ID (SAP):</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly value="">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>SAP Movement Type:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly value="{{$movement_code}}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Good Issue Material:</h3>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter"><span class="fas fa-plus-circle"></span>&nbsp;
                                Add New Material
                            </button>

                            <!-- Modal-->
                            <div class="modal fade" id="exampleModalCenter" data-backdrop="static" tabindex="-1"
                                role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add New GI Material</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <i aria-hidden="true" class="ki ki-close"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Material Code: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="material_select2">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>GR Detail: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="gr_detail_select2"
                                                        name="gr_detail_id">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Batch SAP:</label>
                                                    <input type="text" class="form-control" id="batch_sap" readonly>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Expired Date: <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control" id="expired_date" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Qty Left:</label>
                                                    <input type="text" class="form-control" readonly
                                                        id="qty_left_input">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>GI Qty: <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control decimal_input" id="qty" name="gi_qty">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Storage Location:</label>
                                                    <input type="text" class="form-control" readonly id="sloc_input">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Notes: <span style="color:red">*</span></label>
                                                    <textarea class="form-control"
                                                    name="gi_note" id="gi_note"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="button" id="save_material_btn"
                                                class="btn btn-primary font-weight-bold">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable kt_datatable1"">
                            <thead>
                                <tr>
                                    <td>Document No</td>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>QTY</td>
                                    <td>UOM</td>
                                    <td>Batch</td>
                                    <td>Expired Date</td>
                                    <td>Note</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($temp_material as $row)
                                <tr>
                                    <td>{{ $row["TR_GR_DETAIL_ID"]}}</td>
                                    <td>{{ $row["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                                    <td>{{ number_format($row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],2) }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                    <td>{{ convert_to_web_dmy($row["TR_GR_DETAIL_EXP_DATE"]) }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_LOCK_NOTE"] }}</td>
                                    <td>
                                        <a data-uniqid="{{ $row['TR_GR_DETAIL_LOCK_ID'] }}"
                                            class="btn btn-sm btn-clean btn-icon delete_material_btn"> <i
                                                class="la la-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <a href="{{ route("purchase_order_good_issue_detail", ["gi_po_number" => $header_data['TR_PO_HEADER_NUMBER']]) }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back / Cancel</a>
                </div>
                <div class="col-lg-6 text-lg-right">
                    <button type="button" class="btn btn-primary mr-2" id="submit_btn">
                        <span class="fas fa-save"></span>&nbsp;Submit GI
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection