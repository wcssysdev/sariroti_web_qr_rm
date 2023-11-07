@extends('layouts.app')

@section('page_title', 'Detail Stock Opname')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/purchase_order/good_receipt/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/pid/approval.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom overflow-hidden">
    <div class="card-body p-0">
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-11">
                <h2>PID Header:</h2>
                <div class="form-group row d-flex justify-content-between pt-6">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SAP Number: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_SAP_NO']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SAP Year: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_YEAR']}}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Header Status: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_STATUS']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SAP Created Date: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_SAP_CREATED_DATE']}}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SAP Created By: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_SAP_CREATED_BY']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Plant: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_PLANT']}}</span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">SLOC: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_SLOC']}}</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Approval Status: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_APPROVAL_STATUS']}}
                            @if ($header_data['TR_PID_HEADER_APPROVAL_TIMESTAMP'] != NULL && $header_data['TR_PID_HEADER_APPROVAL_TIMESTAMP'] != "")
                            - {{$header_data['TR_PID_HEADER_APPROVAL_TIMESTAMP']}}
                            @endif
                            @if ($header_data['TR_PID_HEADER_APPROVAL_BY'] != NULL && $header_data['TR_PID_HEADER_APPROVAL_BY'] != "")
                            - {{$header_data['TR_PID_HEADER_APPROVAL_BY']}}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: -10px">
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Photo: </span>
                        @if ($header_data["TR_PID_HEADER_PHOTO"] != null && $header_data["TR_PID_HEADER_PHOTO"] != "")
                            <a href="{{ asset('storage/PID_images/')."/".$header_data["TR_PID_HEADER_PHOTO"] }}" target="_blank"> Vew Photo</a>
                        @else
                            No Photo Available
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <span class="font-weight-bolder mb-2">Approval Notes: </span>
                        <span class="opacity-70">{{$header_data['TR_PID_HEADER_APPROVAL_NOTES']}}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
            <div class="col-md-11">
                <h2>PID Material:</h2>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-checkable" id="kt_datatable1">
                        <thead>
                            <tr>
                                <td>Line Number</td>
                                <td>Material Code</td>
                                <td>Material Text</td>
                                <td>QTY</td>
                                <td>UOM</td>
                                <td>SAP Batch Code</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail_data as $row)
                            <tr>
                                <td>{{ $row["TR_PID_DETAIL_LINE_MATERIAL"] }}</td>
                                <td>{{ $row["TR_PID_DETAIL_MATERIAL_CODE"] }}</td>
                                <td>{{ $row["TR_PID_DETAIL_MATERIAL_NAME"] }}</td>
                                <td align="right">{{ number_format($row["TR_PID_DETAIL_MATERIAL_MOBILE_QTY"]) }}</td>
                                <td>{{ $row["TR_PID_DETAIL_MATERIAL_UOM"] }}</td>
                                <td>{{ $row["TR_PID_DETAIL_MATERIAL_SAP_BATCH"] }}</td>
                                <td nowrap="nowrap">
                                    {{-- @if ($row["TR_PID_DETAIL_PHOTO"] != null && $row["TR_PID_DETAIL_PHOTO"] != "")
                                    <a href="{{ asset('storage/PID_images/')."/".$row["TR_PID_DETAIL_PHOTO"] }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-image"></i>
                                    </a>
                                    @endif --}}
                                    <a href="{{ route('transaction_stock_opname_view_detail_material', ['pid_detail_id' => $row["TR_PID_DETAIL_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                        class="la la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-3">
                <div class="d-flex justify-content-between">
                    <a href="{{ route("transaction_stock_opname_view") }}" class="btn btn-secondary"><i class="fas fa-angle-double-left"></i> Back</a>
                </div>
            </div>
            <div class="col-md-5">
                
            </div>
            <div class="col-md-3">
                @if (session('user_role') == 4 && ($header_data["TR_PID_HEADER_APPROVAL_STATUS"] != "APPROVED") && $header_data["TR_PID_MOBILE_ALLOW_TO_INPUT"] == false)
                <div class="pull-right">
                    <button type="button" class="btn btn-danger approval_btn" data-toggle="modal" data-approval-type="Rejected" data-target="#exampleModalCenter" class="btn btn-danger"><i class="fas fa-times"></i> Reject</button>
                    &nbsp;
                    <button type="button" class="btn btn-primary approval_btn" data-toggle="modal" data-approval-type="Approved" data-target="#exampleModalCenter"><span class="fas fa-check"></span>&nbsp; Approve
                    </button>
                </div>
                @endif

                <div class="modal fade" id="exampleModalCenter" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form action="{{ route('transaction_stock_opname_submit_approval')}}" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Stock Opname Approval</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i aria-hidden="true" class="ki ki-close"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @csrf
                                    <input type="hidden" name="TR_PID_HEADER_ID" value="{{$header_data['TR_PID_HEADER_ID']}}">
                                    <input type="hidden" name="approval_status" id="hidden_approval_status">
                                    <p><b>Are You Sure to Approove This Stock Opname?</b></p>
                                    <p>Please enter your approval notes:</p>
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <label>Note:</label>
                                            <textarea class="form-control"
                                            name="notes" placeholder="Input Approval Notes (Required)"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancel</button>
                                    <button type="submit" id="save_material_btn" class="btn btn-primary font-weight-bold">Submit Approval</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection