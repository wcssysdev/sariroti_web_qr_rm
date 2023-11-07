@extends('layouts.app')

@section('page_title', 'Detail PO - GR')

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
                <h2>Goods Receipt Header:</h2>
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
                        <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_GR_HEADER_PSTG_DATE']) }}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Document Date: </span>
                        <span class="opacity-70">{{ convert_to_web_dmy($header_data['TR_GR_HEADER_DOC_DATE']) }}</span>
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
            <div class="col-md-11">
                <div class="form-group row pt-6">
                    <div class="col-lg-10">
                        <h2 class="card-label">Goods Receipt:</h2>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable2">
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
                            <tr>
                                <td>{{ $row["TR_GR_DETAIL_MATERIAL_CODE"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_MATERIAL_NAME"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_SAP_BATCH"] }}</td>
                                <td>{{ number_format($row["TR_GR_DETAIL_QTY"],2)." ".$row["TR_GR_DETAIL_UOM"] }}</td>
                                <td>{{ number_format($row["TR_GR_DETAIL_LEFT_QTY"],2)." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_UNLOADING_PLANT"] }}</td>
                                <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                <td>{{ convert_to_web_dmy($row["TR_GR_DETAIL_EXP_DATE"]) }} </td>
                                <td>{{ $row["TR_GR_DETAIL_NOTES"]}} </td>
                                <td nowrap="nowrap">
                                    @if ($row["TR_GR_DETAIL_PHOTO"] != null && $row["TR_GR_DETAIL_PHOTO"] != "")
                                    <a href="{{ asset('storage/GR_images/')."/".$row["TR_GR_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-image"></i>
                                    </a>
                                    @endif
                                    <a target="_blank" href="{{ route('purchase_order_good_receipt_detail_detail_qr_code', ['gr_detail_id' => $row["TR_GR_DETAIL_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
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
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-1">
                <div class="d-flex justify-content-between">
                    <a href="{{ route("purchase_order_good_receipt_view") }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back</a>
                </div>
            </div>
            <div class="col-md-10">
            </div>
        </div>
    </div>
</div>
@include('modals.transaction.gi_sto.detail')
@endsection