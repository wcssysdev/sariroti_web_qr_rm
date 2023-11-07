@extends('layouts.app')
@section('page_title', 'Detail Material Stock Opname')

@push('styles')
@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/detail/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/good_issue/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<form action="{{ route('transaction_stock_opname_submit') }}" data-form-success-redirect="{{ route('transaction_stock_opname_view') }}" method="POST" id="form">
    @csrf
    <input type="hidden" name="TR_PID_HEADER_ID" value="{{ $header_data["TR_PID_HEADER_ID"] }}">
    <div class="card card-custom overflow-hidden">
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>PID Header Data:</h2>
                    <div class="form-group row d-flex justify-content-between pt-6">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP No: </span>
                            <span class="opacity-70">{{ $header_data['TR_PID_HEADER_SAP_NO'] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Plant: </span>
                            <span class="opacity-70">{{$header_data['TR_PID_HEADER_PLANT']}}</span>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -10px">
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">Storage Location: </span>
                            <span class="opacity-70">{{$header_data['TR_PID_HEADER_SLOC']}}</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="font-weight-bolder mb-2">SAP Created By: </span>
                            <span class="opacity-70">{{$header_data['TR_PID_HEADER_SAP_CREATED_BY']}} ({{$header_data['TR_PID_HEADER_SAP_CREATED_DATE']}})</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <br>
            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
                <div class="col-md-11">
                    <h2>PID Header Information:</h2>
                    <br>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Posting Date:</label>
                            <div class="input-group">
                                <input type="text" name="TR_PID_POSTING_DATE" class="form-control date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
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
                                    <td>UOM</td>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < count($detail_data); $i++)
                                <tr>
                                    <input type="hidden" name="detail[{{$i}}][TR_PID_DETAIL_ID]" value="{{ $detail_data[$i]["TR_PID_DETAIL_ID"] }}">
                                    <input type="hidden" name="detail[{{$i}}][TR_PID_HEADER_ID]" value="{{ $detail_data[$i]["TR_PID_HEADER_ID"] }}">
                                    <input type="hidden" name="detail[{{$i}}][TR_GR_DETAIL_ID]" value="{{ $detail_data[$i]["TR_GR_DETAIL_ID"] }}">
                                    <td>{{ $detail_data[$i]["TR_GR_DETAIL_ID"] }}</td>
                                    <td>{{ $detail_data[$i]["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                                    <td>{{ $detail_data[$i]["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                                    <td>{{ $detail_data[$i]["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                    <td align="right">{{ number_format($detail_data[$i]["TR_GR_DETAIL_LEFT_QTY"]) }}</td>
                                    <td align="right"><input type="text" class="form-control decimal_input" value="{{ $detail_data[$i]["TR_GR_DETAIL_LEFT_QTY"] }}" name="detail[{{$i}}][qty]"></td>
                                    <td>{{ $detail_data[$i]["TR_GR_DETAIL_BASE_UOM"] }}</td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    
                </div>
                <div class="col-lg-6 text-lg-right">
                    <button type="button" class="btn btn-primary mr-2" id="submit_btn">
                        <span class="fas fa-save"></span>&nbsp;Submit Stock Opname
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection