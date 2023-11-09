@extends('layouts.app')

@section('page_title', 'Detail Transfer Posting')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/purchase_order/po_gr/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/transfer_posting/add/script.js') }}"></script>
<script src="{{ asset('custom/js/transaction/purchase_order/transfer_posting/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom overflow-hidden">
    <div class="card-body p-0">
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-11">
                <h2>Transfer Posting:</h2>
                <hr>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Document ID:</span>
                        <span class="opacity-70">{{ $data['TR_TP_HEADER_ID'] }}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Material DOC:</span>
                        <span class="opacity-70">{{ $data['TR_TP_HEADER_SAP_DOC'] }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Plant:</span>
                        <span class="opacity-70">{{ $data['TR_TP_HEADER_PLANT_CODE'] }}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Posting Date:</span>
                        <span class="opacity-70">{{ convert_to_web_dmy($data['TR_TP_HEADER_PSTG_DATE']) }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Bill of Landing:</span>
                        <span class="opacity-70">{{ $data['TR_TP_HEADER_BOL'] }}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SAP Movement Type:</span>
                        <span class="opacity-70">{{ $data['TR_TP_HEADER_MVT_CODE'] }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Note:</span>
                        <span class="opacity-70">{{ $data['TR_TP_HEADER_TXT'] }}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Photo:</span>
                        <span class="opacity-70">
                            @if ($data["TR_TP_HEADER_PHOTO"] != null && $data["TR_TP_HEADER_PHOTO"] != "")
                            <a href="{{ asset('storage/TP_images/')."/".$data["TR_TP_HEADER_PHOTO"] }}" target="_blank"> Vew Photo</a>
                        @else
                            No Photo Available
                        @endif
                        </span>
                    </div>
                </div>
                <hr>
                <div class="form-group row pt-6">
                    <div class="col-lg-10">
                        <h3 class="card-label">Material:</h3>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable2">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Material</td>
                                <td>QTY</td>
                                <td>Mobile Input Qty</td>
                                <td>SLoc From</td>
                                <td>SLoc Destination</td>
                                <td>Batch SAP</td>
                                <td>Expired Date</td>
                                <td>Note</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_data as $row)
                            <tr>
                                <td>{{ $row["TR_TP_DETAIL_ID"]}}</td>
                                <td>{{ $row["TR_TP_DETAIL_MATERIAL_CODE"]." - ".$row["TR_TP_DETAIL_MATERIAL_NAME"] }}</td>
                                <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_QTY"],2)." ".$row["TR_TP_DETAIL_UOM"] }}</td>
                                <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_MOBILE_QTY"],2)." ".$row["TR_TP_DETAIL_MOBILE_UOM"] }}</td>
                                @if($data['TR_TP_HEADER_MVT_CODE'] != "Y21")
                                <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                @else
                                <td>{{ $row["TR_TP_DETAIL_SLOC_Y21_FROM"] }}</td>
                                @endif
                                <td>{{ $row["TR_TP_DETAIL_SLOC"] }}</td>
                                <td>{{ $row["TR_TP_DETAIL_SAP_BATCH"] }}</td>
                                @if($data['TR_TP_HEADER_MVT_CODE'] != "Y21")
                                <td>{{ $row["TR_GR_DETAIL_EXP_DATE"] }}</td>
                                @else
                                <td>{{ convert_to_web_dmy($row["TR_TP_DETAIL_Y21_EXP_DATE"]) }}</td>
                                @endif
                                <td>{{ $row["TR_TP_DETAIL_NOTES"] }}</td>
                                <td>
                                    @if ($row["TR_TP_DETAIL_PHOTO"] != null && $row["TR_TP_DETAIL_PHOTO"] != "")
                                    <a href="{{ asset('storage/TP_images/')."/".$row["TR_TP_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-image"></i>
                                    </a>
                                    @endif
                                    
                                    <a target="_blank" href="{{ route('goods_movement_transfer_posting_detail_detail_qr_code', ['tp_detail_id' => $row["TR_TP_DETAIL_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-barcode"></i>
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
        </div>
    </div>
</div>
@endsection