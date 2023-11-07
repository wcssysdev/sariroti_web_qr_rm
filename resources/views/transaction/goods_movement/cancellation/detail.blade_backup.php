@extends('layouts.app')

@section('page_title', 'Create Cancellation')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/cancellation/add/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/cancellation/add/datatable.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom overflow-hidden">
    <form action="{{ route('transaction_goods_movement_cancellation_save') }}" method="POST" data-form-success-redirect="{{ route('transaction_goods_movement_cancellation_view') }}" id="form">
        @csrf
        <input type="hidden" name="type" value="{{ $cancellation_type }}">
        <input type="hidden" name="doc_number" value="{{ $doc_number }}">
        @if ($cancellation_type == "GR")
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Good Receipt Header:</h2>
                    <div class="form-group row d-flex justify-content-between pt-6">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">PO Number: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_PO_NUMBER']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Plant Code: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_PLANT_CODE']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP Number: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_SAP_DOC']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Posting Date: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_PSTG_DATE']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Document Date: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_DOC_DATE']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Movement Code: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_MVT_CODE']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP Year: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_SAP_YEAR']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Integration Status: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_STATUS']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Photo: </span>
                            <span class="opacity-70">
                                @if ($header_data["TR_GR_HEADER_PHOTO"] != null && $header_data["TR_GR_HEADER_PHOTO"] != "")
                                    <a href="{{ asset('storage/GR_images/')."/".$header_data["TR_GR_HEADER_PHOTO"] }}" target="_blank"> Vew Photo</a>
                                @else
                                    No Photo Available
                                @endif
                            </span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Recipient: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_RECIPIENT']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Note: </span>
                            <span class="opacity-70">{{$header_data['TR_GR_HEADER_TXT']}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-lg-11">
                    <h3 class="card-label">Cancellation Header:</h3>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-lg-11">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Posting Date: <span style="color:red">*</span></label>
                            <input type="text" class="form-control date" name="TR_CANCELLATION_MVT_POSTING_DATE" placeholder="Input Posting Date">
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-md-11">
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h2 class="card-label">Cancelled Goods Receipt Materials:</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable datatable">
                            <thead>
                                <tr>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>Batch</td>
                                    <td>Qty</td>
                                    <td>Actual Qty</td>
                                    <td>Plant</td>
                                    <td>Storage Location</td>
                                    <td>Expired Date</td>
                                    <td>Notes</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_data as $row)
                                @if ($row["TR_GR_DETAIL_IS_CANCELLED"] == true)
                                <tr>
                                    <td>{{ $row["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                    <td>{{ number_format($row["TR_GR_DETAIL_QTY"],2)." ".$row["TR_GR_DETAIL_UOM"] }}</td>
                                    <td>{{ number_format($row["TR_GR_DETAIL_LEFT_QTY"],2)." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_UNLOADING_PLANT"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_EXP_DATE"]}} </td>
                                    <td>{{ $row["TR_GR_DETAIL_NOTES"]}} </td>
                                    <td nowrap="nowrap">
                                        @if ($row["TR_GR_DETAIL_PHOTO"] != null && $row["TR_GR_DETAIL_PHOTO"] != "")
                                        <a href="{{ asset('storage/GR_images/')."/".$row["TR_GR_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                            class="la la-image"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
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
                            <h2 class="card-label">Not Cancelled Goods Receipt Materials:</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable datatable">
                            <thead>
                                <tr>
                                    <td>Select</td>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>Batch</td>
                                    <td>Qty</td>
                                    <td>Actual Qty</td>
                                    <td>Plant</td>
                                    <td>Storage Location</td>
                                    <td>Expired Date</td>
                                    <td>Notes</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_data as $row)
                                @if ($row["TR_GR_DETAIL_IS_CANCELLED"] == false)
                                <tr>
                                    <td>
                                        <div class="checkbox-list">
                                            <label class="checkbox">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $row["TR_GR_DETAIL_ID"] }}"/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $row["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                    <td>{{ number_format($row["TR_GR_DETAIL_QTY"])." ".$row["TR_GR_DETAIL_UOM"] }}</td>
                                    <td>{{ number_format($row["TR_GR_DETAIL_LEFT_QTY"])." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_UNLOADING_PLANT"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_EXP_DATE"]}} </td>
                                    <td>{{ $row["TR_GR_DETAIL_NOTES"]}} </td>
                                    <td nowrap="nowrap">
                                        @if ($row["TR_GR_DETAIL_PHOTO"] != null && $row["TR_GR_DETAIL_PHOTO"] != "")
                                        <a href="{{ asset('storage/GR_images/')."/".$row["TR_GR_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                            class="la la-image"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-1">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route("transaction_goods_movement_cancellation_view") }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back</a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="text-lg-right">
                        <button type="button" class="btn btn-primary mr-2" id="submit_btn"><span class="fas fa-save"></span>&nbsp;Submit Cancellation</button>
                    </div>
                </div>
            </div>
        </div>
        @elseif ($cancellation_type == "GI")
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Goods Issue Header:</h2>
                    <div class="form-group row d-flex justify-content-between pt-6">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">PO Number: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_PO_NUMBER']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Plant Code: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_PLANT_CODE']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP Number: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_SAP_DOC']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Posting Date: </span>
                            <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_GI_SAPHEADER_PSTG_DATE']) }}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Document Date: </span>
                            <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_GI_SAPHEADER_DOC_DATE']) }}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Movement Code: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_MVT_CODE']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP Year: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_SAP_YEAR']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Integration Status: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_STATUS']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Photo: </span>
                            @if ($header_data["TR_GI_SAPHEADER_PHOTO"] != null && $header_data["TR_GI_SAPHEADER_PHOTO"] != "")
                                <a href="{{ asset('storage/GI_images/')."/".$header_data["TR_GI_SAPHEADER_PHOTO"] }}" target="_blank"> Vew Photo</a>
                            @else
                                No Photo Available
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Note: </span>
                            <span class="opacity-70">{{$header_data['TR_GI_SAPHEADER_TXT']}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-lg-11">
                    <h3 class="card-label">Cancellation Header:</h3>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-lg-11">
                    <div class="row">
                        <div class="col-lg-3">
                            <label>Posting Date: <span style="color:red">*</span></label>
                            <input type="text" class="form-control date" name="TR_CANCELLATION_MVT_POSTING_DATE" placeholder="Input Posting Date">
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-md-11">
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h2 class="card-label">Cancelled Goods Issue Material:</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable datatable">
                            <thead>
                                <tr>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>Batch</td>
                                    <td>Qty</td>
                                    <td>Mobile Qty</td>
                                    <td>Storage Location</td>
                                    <td>Notes</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_data as $row)
                                @if ($row["TR_GI_SAPDETAIL_IS_CANCELLED"] == true)
                                <tr>
                                    <td>{{ $row["TR_GI_SAPDETAIL_MATERIAL_CODE"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_MATERIAL_NAME"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_SAP_BATCH"] }}</td>
                                    <td>{{ number_format($row["TR_GI_SAPDETAIL_GI_QTY"])." ".$row["TR_GI_SAPDETAIL_GI_UOM"] }}</td>
                                    <td>{{ number_format($row["TR_GI_SAPDETAIL_MOBILE_QTY"])." ".$row["TR_GI_SAPDETAIL_MOBILE_UOM"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_NOTES"]}} </td>
                                    <td nowrap="nowrap">
                                        @if ($row["TR_GI_SAPDETAIL_PHOTO"] != null && $row["TR_GI_SAPDETAIL_PHOTO"] != "")
                                        <a href="{{ asset('storage/GI_images/')."/".$row["TR_GI_SAPDETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                            class="la la-image"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
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
                            <h2 class="card-label">Not Cancelled Goods Issue Material:</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable datatable">
                            <thead>
                                <tr>
                                    <td>Select</td>
                                    <td>Material Code</td>
                                    <td>Material Name</td>
                                    <td>Batch</td>
                                    <td>Qty</td>
                                    <td>Mobile Qty</td>
                                    <td>Storage Location</td>
                                    <td>Notes</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_data as $row)
                                @if ($row["TR_GI_SAPDETAIL_IS_CANCELLED"] == false)
                                <tr>
                                    <td>
                                        <div class="checkbox-list">
                                            <label class="checkbox">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $row["TR_GI_SAPDETAIL_ID"] }}"/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_MATERIAL_CODE"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_MATERIAL_NAME"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_SAP_BATCH"] }}</td>
                                    <td>{{ number_format($row["TR_GI_SAPDETAIL_GI_QTY"])." ".$row["TR_GI_SAPDETAIL_GI_UOM"] }}</td>
                                    <td>{{ number_format($row["TR_GI_SAPDETAIL_MOBILE_QTY"])." ".$row["TR_GI_SAPDETAIL_MOBILE_UOM"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_NOTES"]}} </td>
                                    <td nowrap="nowrap">
                                        @if ($row["TR_GI_SAPDETAIL_PHOTO"] != null && $row["TR_GI_SAPDETAIL_PHOTO"] != "")
                                        <a href="{{ asset('storage/GI_images/')."/".$row["TR_GI_SAPDETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                            class="la la-image"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-1">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route("transaction_goods_movement_cancellation_view") }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back</a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="text-lg-right">
                        <button type="button" class="btn btn-primary mr-2" id="submit_btn"><span class="fas fa-save"></span>&nbsp;Submit Cancellation</button>
                    </div>
                </div>
            </div>
        </div>
        @elseif ($cancellation_type == "TP")
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Transfer Posting Header:</h2>
                    <hr>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Document ID:</span>
                            <span class="opacity-70">{{ $header_data['TR_TP_HEADER_ID'] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Material DOC:</span>
                            <span class="opacity-70">{{ $header_data['TR_TP_HEADER_SAP_DOC'] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Plant:</span>
                            <span class="opacity-70">{{ $header_data['TR_TP_HEADER_PLANT_CODE'] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Posting Date:</span>
                            <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_TP_HEADER_PSTG_DATE']) }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Bill of Landing:</span>
                            <span class="opacity-70">{{ $header_data['TR_TP_HEADER_BOL'] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP Movement Type:</span>
                            <span class="opacity-70">{{ $header_data['TR_TP_HEADER_MVT_CODE'] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Note:</span>
                            <span class="opacity-70">{{ $header_data['TR_TP_HEADER_TXT'] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Photo:</span>
                            <span class="opacity-70">
                                @if ($header_data["TR_TP_HEADER_PHOTO"] != null && $header_data["TR_TP_HEADER_PHOTO"] != "")
                                <a href="{{ asset('storage/TP_images/')."/".$header_data["TR_TP_HEADER_PHOTO"] }}" target="_blank"> Vew Photo</a>
                            @else
                                No Photo Available
                            @endif
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Cancellation Header:</h3>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Posting Date: <span style="color:red">*</span></label>
                            <input type="text" class="form-control date" name="TR_CANCELLATION_MVT_POSTING_DATE"
                                placeholder="Input Posting Date">
                        </div>
                    </div>
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Cancelled Transfer Posting Materials:</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable datatable">
                            <thead>
                                <tr>
                                    <td>Material</td>
                                    <td>QTY</td>
                                    <td>Mobile Input Qty</td>
                                    <td>SLOC From</td>
                                    <td>SLOC Destination</td>
                                    <td>Batch SAP</td>
                                    <td>Expired Date</td>
                                    <td>Note</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_data as $row)
                                @if ($row["TR_TP_DETAIL_IS_CANCELLED"] == true)
                                <tr>
                                    <td>{{ $row["TR_TP_DETAIL_MATERIAL_CODE"]." - ".$row["TR_TP_DETAIL_MATERIAL_NAME"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_QTY"])." ".$row["TR_TP_DETAIL_UOM"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_MOBILE_QTY"])." ".$row["TR_TP_DETAIL_MOBILE_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_TP_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_TP_DETAIL_SAP_BATCH"] }}</td>
                                    <td>-</td>
                                    <td>{{ $row["TR_TP_DETAIL_NOTES"] }}</td>
                                    <td>
                                        @if ($row["TR_TP_DETAIL_PHOTO"] != null && $row["TR_TP_DETAIL_PHOTO"] != "")
                                        <a href="{{ asset('storage/TP_images/')."/".$row["TR_TP_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                            class="la la-image"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Not Cancelled Transfer Posting Materials:</h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-checkable datatable">
                            <thead>
                                <tr>
                                    <td>Select</td>
                                    <td>Material</td>
                                    <td>QTY</td>
                                    <td>Mobile Input Qty</td>
                                    <td>SLOC From</td>
                                    <td>SLOC Destination</td>
                                    <td>Batch SAP</td>
                                    <td>Expired Date</td>
                                    <td>Note</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail_data as $row)
                                @if ($row["TR_TP_DETAIL_IS_CANCELLED"] == false)
                                <tr>
                                    <td>
                                        <div class="checkbox-list">
                                            <label class="checkbox">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $row["TR_TP_DETAIL_ID"] }}"/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $row["TR_TP_DETAIL_MATERIAL_CODE"]." - ".$row["TR_TP_DETAIL_MATERIAL_NAME"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_QTY"])." ".$row["TR_TP_DETAIL_UOM"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_MOBILE_QTY"])." ".$row["TR_TP_DETAIL_MOBILE_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_TP_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_TP_DETAIL_SAP_BATCH"] }}</td>
                                    <td>-</td>
                                    <td>{{ $row["TR_TP_DETAIL_NOTES"] }}</td>
                                    <td>
                                        @if ($row["TR_TP_DETAIL_PHOTO"] != null && $row["TR_TP_DETAIL_PHOTO"] != "")
                                        <a href="{{ asset('storage/TP_images/')."/".$row["TR_TP_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                            class="la la-image"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-1">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route("transaction_goods_movement_cancellation_view") }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back</a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="text-lg-right">
                        <button type="button" class="btn btn-primary mr-2" id="submit_btn"><span class="fas fa-save"></span>&nbsp;Submit Cancellation</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </form>
</div>
@endsection