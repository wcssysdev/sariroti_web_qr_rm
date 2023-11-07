@extends('layouts.app')

@section('page_title', 'Create New GR')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/add/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/add/script.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
@if (isset($err_message))
<div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
    <div class="alert-icon"><i class="flaticon-warning"></i></div>
    <div class="alert-text">{{$err_message}}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
<form action="{{ route('purchase_order_good_receipt_save') }}" method="POST" data-get-materials-url="{{ route('purchase_order_good_receipt_add_get_materials') }}"
    data-get-material-status-url="{{ route('purchase_order_good_receipt_add_get_material_status') }}"
    data-save-material-url="{{ route('purchase_order_good_receipt_save_material') }}" data-delete-material-url="{{ route('purchase_order_good_receipt_delete_material') }}" data-form-success-redirect="{{ route('purchase_order_good_receipt_view') }}" id="form">
    @csrf
    <input type="hidden" name="po_number" id="po_number" value="{{$po_number}}">
    <input type="hidden" name="uniqid" id="uniqid">
    <div class="card card-custom overflow-hidden">
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Create New Good Receipt:</h2>
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
                            <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_PO_HEADER_SAP_CREATED_DATE'])}}</span>
                        </div>
                    </div>
                    <hr>
                    <br>
                    <h3>Good Receipt Header:</h3>
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
                                <input type="text" class="form-control" readonly value="101">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>Posting Date: <span style="color:red">*</span></label>
                            <input type="text" class="form-control date" name="TR_GR_HEADER_PSTG_DATE" placeholder="Input Posting Date" value="{{ $header_posting_date }}">
                        </div>
                        <div class="col-lg-3">
                            <label>Bill of Landing:</label>
                            <input type="text" class="form-control" name="TR_GR_HEADER_BOL"
                                placeholder="Input Bill of Landing (If Any)" value="{{ $header_bill_of_landing }}">
                        </div>
                        <div class="col-lg-3">
                            <label>Recepient Name: <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="TR_GR_HEADER_RECIPIENT" placeholder="Input Recipient" value="{{ $header_recipient }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Delivery Notes: <span style="color:red">*</span></label>
                            <div class="input-group">
                                <textarea type="text" class="form-control" name="TR_GR_HEADER_TXT"
                                    placeholder="Input Note">{{ $header_note }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>Is Adjustment GR?: <span style="color:red">*</span></label>
                            <div class="checkbox-list">
                                <label class="checkbox">
                                <input type="checkbox" name="TR_GR_HEADER_IS_ADJUSTMENT">
                                <span></span>Yes</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Good Receipt Material:</h3>
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
                                            <h5 class="modal-title" id="exampleModalLabel">Add New GR Material</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <i aria-hidden="true" class="ki ki-close"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Material Code: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="material_select2" name="po_detail_id">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Storage Location: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="sloc_select2" name="TR_GR_DETAIL_SLOC">
                                                        <option selected disabled>Choose Sloc</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Batch SAP:</label>
                                                    <select class="form-control" id="batch_select2" name="batch_sap">
                                                        <option selected disabled>Choose Batch</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Expired Date: <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control date" id="expired_date" name="expired_date">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Remaining Unreceived Qty:</label>
                                                    <input type="text" class="form-control" readonly
                                                        id="qty_left_input">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Qty: <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control decimal_input" id="qty" name="qty">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12">
                                                    <label>Note:</label>
                                                    <textarea class="form-control"
                                                    name="TR_GR_DETAIL_NOTES" id="gr_note"></textarea>
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
                        <table class="table table-bordered table-checkable" id="kt_datatable1">
                            <thead>
                                <tr>
                                    <td>Document No</td>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>QTY</td>
                                    <td>UOM</td>
                                    <td>Batch</td>
                                    <td>SLoc</td>
                                    <td>Expired Date</td>
                                    <td>Note</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($temp_material as $row)
                                <tr>
                                    <td>{{ $counter }}</td>
                                    <td>{{ $row["material_code"] }}</td>
                                    <td>{{ $row["material_name"] }}</td>
                                    <td>{{ number_format($row["qty"],2) }}</td>
                                    <td>{{ $row["uom"] }}</td>
                                    <td>{{ $row["batch"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ convert_to_web_dmy($row["expired_date"]) }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_NOTES"] }}</td>
                                    <td>
                                        <a data-uniqid="{{ $row["uniqid"] }}"
                                            class="btn btn-sm btn-clean btn-icon delete_material_btn"> <i class="la la-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @php
                                    $counter++;
                                @endphp
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
                    <a href="{{ url()->previous() }}" class="btn btn-secondary"><i
                        class="fas fa-angle-double-left"></i> Back / Cancel</a>
                </div>
                <div class="col-lg-6 text-lg-right">
                    <button type="button" class="btn btn-primary mr-2" id="submit_btn"><span class="fas fa-save"></span>&nbsp;Submit GR</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection