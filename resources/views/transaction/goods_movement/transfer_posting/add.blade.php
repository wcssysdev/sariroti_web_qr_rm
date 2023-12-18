@extends('layouts.app')

@section('page_title', 'Create New Transfer Posting')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/purchase_order/po_gr/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/transfer_posting/add/select2.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/transfer_posting/add/script.js?=2') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/transfer_posting/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<form action="{{ route('goods_movement_transfer_posting_save') }}" method="POST"
    data-get-materials-url="{{ route('goods_movement_transfer_posting_add_get_materials') }}" data-get-materials-y21-url="{{ route('goods_movement_transfer_posting_add_get_materials_y21') }}"
    data-get-material-batch-y21-url="{{ route('goods_movement_transfer_posting_add_get_material_batch_y21') }}" 
    data-get-material-status-url="{{ route('goods_movement_transfer_posting_add_get_material_status') }}" data-get-material-gr-url="{{ route('goods_movement_transfer_posting_add_get_material_gr') }}"
    data-save-material-url="{{ route('goods_movement_transfer_posting_save_material') }}"
    data-delete-material-url="{{ route('goods_movement_transfer_posting_delete_material') }}"
    data-save-material-y21-url="{{ route('goods_movement_transfer_posting_save_material_y21') }}"
    data-delete-material-y21-url="{{ route('goods_movement_transfer_posting_delete_material_y21') }}"
    data-form-success-redirect="{{ route('goods_movement_transfer_posting_view') }}" id="form">
    @csrf
    <input type="hidden" name="uniqid" id="uniqid">
    <div class="card card-custom overflow-hidden">
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Create New Transfer Posting:</h2>
                    <hr>
                    <br>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Movement Code:</label>
                            <select class="form-control" id="movement_select2" name="TR_TP_HEADER_MVT_CODE" data-mvt-selected="{{$header_movement_code}}">
                                <option value="311">311 - TP</option>
                                <option value="411">411 - Gudang Eksternal to RM</option>
                                <option value="Y21">Y21 - PROD->RM</option>
                                <option value="551">551 - BIWA</option>
                            </select>
                        </div>
                        
                        <div class="col-lg-3" id="posting_date_input">
                            <label>Posting Date:<span style="color:red">*</span></label>
                            <input type="text" class="form-control date" value="{{ $header_posting_date }}" placeholder="Input Posting Date" name="TR_TP_HEADER_PSTG_DATE">
                        </div>

                        <div class="col-lg-3" id="cost_center_div">
                            <label>Cost Center:</label>
                            <select class="form-control" id="cost_center_select2" name="TR_TP_COST_CENTER_CODE" data-cc-selected="{{$header_cost_center}}">
                                @foreach ($cost_center as $row)
                                <option value="{{$row['MA_COSTCNTR_CODE']}}">{{$row['MA_COSTCNTR_CODE']." - ".$row['MA_COSTCNTR_DESC']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3" id="gl_account_div">
                            <label>GL Account:</label>
                            <select class="form-control" id="gl_acc_select2" name="TR_TP_GL_ACCOUNT_CODE" data-gl-selected="{{$header_gl_account}}">
                                @foreach ($gl_account as $row)
                                <option value="{{$row['MA_GLACC_CODE']}}">{{$row['MA_GLACC_CODE']." - ".$row['MA_GLACC_DESC']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Bill of Landing:</label>
                            <input type="text" class="form-control" name="TR_TP_HEADER_BOL"
                                   placeholder="Input Bill of Landing (If Any)" value="{{ $header_bill_of_landing }}">
                        </div>                        
                        <div class="col-lg-4">
                            <label>Delivery Notes: <span style="color:red">*</span></label>
                            <div class="input-group">
                                <textarea type="text" class="form-control" name="TR_TP_HEADER_TXT"
                                          placeholder="Input Note">{{ $header_note }}</textarea>
                            </div>
                        </div>
                    </div>                    
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Material:</h3>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" id="add_new_material_btn" class="btn btn-primary">
                                Add New Material
                            </button>
                            <!-- Modal-->
                            <div class="modal fade" id="general_modal" data-backdrop="static" tabindex="-1"
                            role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add New Transfer Posting Material</h5>
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
                                                    <label>Posting Qty: <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control decimal_input" id="qty" name="posting_qty">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>From Storage Location:</label>
                                                    <input type="text" class="form-control" readonly id="sloc_input">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Destination Storage Location:<span style="color:red">*</span></label>
                                                    <select class="form-control" id="sloc_select2"
                                                        name="TR_TP_DETAIL_SLOC">
                                                        <option></option>
                                                    </select>
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

                            <div class="modal fade" id="y21_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add New TP (Y21) Material</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <i aria-hidden="true" class="ki ki-close"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>Material Code: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="material_y21_select2" name="material_code_y21">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Batch SAP:</label>
                                                    <select class="form-control" id="batch_y21_select2" name="batch_sap_y21">
                                                        <option selected disabled>Choose Batch</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                
                                                <div class="col-lg-6">
                                                    <label>Expired Date: <span style="color:red">*</span></label>
                                                    <input type="text" class="form-control date" id="expired_date_y21" name="expired_date_y21">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>Posting Qty: <span style="color:red">*</span> <span id="uom_text"></span></label>
                                                    <input type="text" class="form-control decimal_input" id="qty_y21" name="posting_qty_y21">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <label>From Storage Location: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="sloc_y21_from_select2" name="from_sloc_y21">
                                                        <option selected disabled>Choose Sloc</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>To Storage Location: <span style="color:red">*</span></label>
                                                    <select class="form-control" id="sloc_y21_to_select2" name="to_sloc_y21">
                                                        <option selected disabled>Choose Sloc</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12">
                                                    <label>Note:</label>
                                                    <textarea class="form-control"
                                                    name="note_y21" id="note_y21"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="button" id="save_material_y21_btn"
                                                class="btn btn-primary font-weight-bold">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable" id="kt_datatable2">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>QTY</td>
                                    <td>SLOC From</td>
                                    <td>SLOC Destination</td>
                                    <td>Batch SAP</td>
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
                                    <td>{{ number_format($row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],2)." ".$row["TR_GR_DETAIL_LOCK_BOOKED_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_LOCK_BOOKED_SLOC"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_EXP_DATE"] }}</td>
                                    <td></td>
                                    <td>
                                        <a data-uniqid="{{ $row['TR_GR_DETAIL_LOCK_ID'] }}"
                                            class="btn btn-sm btn-clean btn-icon delete_material_btn"> <i
                                                class="la la-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @foreach ($y21_data as $row)
                                <tr>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_ID"]}}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_CODE"] }}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_NAME"] }}</td>
                                    <td>{{ number_format($row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],2)." ".$row["TR_TP_Y21_DETAIL_TEMP_BASE_UOM"] }}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_SLOC_FROM"] }}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_SLOC_TO"] }}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_SAP_BATCH"] }}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_EXP_DATE"] }}</td>
                                    <td>{{ $row["TR_TP_Y21_DETAIL_TEMP_NOTES"] }}</td>
                                    <td>
                                        <a data-uniqid="{{ $row['TR_TP_Y21_DETAIL_TEMP_ID'] }}"
                                            class="btn btn-sm btn-clean btn-icon delete_material_y21_btn"> <i class="la la-trash"></i>
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
                    <a href="{{ route("goods_movement_transfer_posting_view") }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back / Cancel</a>
                </div>
                <div class="col-lg-6 text-lg-right">
                    <button type="button" class="btn btn-primary mr-2" id="submit_btn">
                        <span class="fas fa-save"></span>&nbsp;Submit Transfer Posting
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>


@endsection