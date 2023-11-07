@extends('layouts.app')

@section('page_title', 'Create New Cancellation MVT')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/cancellation/add/select2.js?=1') }}"></script>
<script src="{{ asset('custom/js/cancellation/add/ajax.js') }}"></script>
<script src="{{ asset('custom/js/cancellation/add/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom overflow-hidden">
    <form method="GET" action="{{ route('transaction_goods_movement_cancellation_detail') }}" data-get-doc-number="{{ route('cancellation_get_doc_number') }}" data-form-success-redirect="{{ route('transaction_goods_movement_cancellation_view') }}" data-detail-gr-url="{{ route('purchase_order_good_receipt_detail_detail')}}" data-detail-tp-url="{{ route('goods_movement_transfer_posting_detail')}}" data-detail-gi-url="{{ route('purchase_order_good_issue_detail_detail')}}" id="form">
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-11">
                    <h2>Create New Cancellation MVT:</h2>
                    <hr>
                    <div class="form-group row pt-6">
                        <div class="col-lg-10">
                            <h3 class="card-label">Choose Document:</h3>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>Document Type:</label>
                            <select class="form-control" id="doc_type_select2" name="doc_type" required>
                                <option></option>
                                <option value="GR">GR</option>
                                <option value="GI">GI</option>
                                <option value="TP">TP</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label>Document Number:</label>
                            <select class="form-control" id="doc_number_select2" name="doc_number" required>
                                <option></option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary mr-2"><span class="fas fa-eye"></span>&nbsp;View Document Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form action="{{ route('transaction_goods_movement_cancellation_save') }}" method="POST" data-get-doc-number="{{ route('cancellation_get_doc_number') }}" data-get-doc-number-detail="{{ route('cancellation_get_doc_number_detail') }}" data-form-success-redirect="{{ route('transaction_goods_movement_cancellation_view') }}" data-detail-gr-url="{{ route('purchase_order_good_receipt_detail_detail')}}" data-detail-tp-url="{{ route('goods_movement_transfer_posting_detail')}}" data-detail-gi-url="{{ route('purchase_order_good_issue_detail_detail')}}" id="form2">
        @csrf
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <a href="{{ route("transaction_goods_movement_cancellation_view") }}" class="btn btn-secondary"><i
                        class="fas fa-angle-double-left"></i> Back</a>
                    
                </div>
                {{-- <div class="col-lg-6 text-lg-right">
                    <button type="button" class="btn btn-primary mr-2" id="submit_btn"><span class="fas fa-save"></span>&nbsp;Save</button>
                </div> --}}
            </div>
        </div>
    </form>
</div>
@endsection