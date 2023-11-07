@extends('layouts.app')

@section('page_title', 'View Master PO')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/po/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/master_data/po/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/po/view/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom gutter-b">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <form class="form" method="GET" action="">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>PO Date: </label>
                    <div class="input-daterange input-group" id="kt_datepicker_5">
                        <input type="text" class="form-control date" name="start_date" autocomplete="off" placeholder="Start" value="{{ $start }}">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                -
                            </span>
                        </div>
                        <input type="text" class="form-control date" name="end_date" autocomplete="off" placeholder="End" value="{{ $end }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i>Filter</button>
                </div>
            </div>
        </div>
    </form>
</div>
<form class="form" id="form" action="{{ route('purchase_order_master_sync_sap') }}" data-form-success-redirect="{{ route('purchase_order_master_view') }}" action2="{{ route("purchase_order_master_request_sap") }}">
    @csrf
    <div class="card card-custom">
        <div class="card-header py-3">
            <div class="card-title">
                <span class="card-icon">                 
                    <h3 class="card-label">Master Data Purchase Order</h3>
                </span>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-primary font-weight-bolder" id="submit_sync_sap">
                    <i class="fas fa-cog"></i>Sync PO Master
                </button> 
                &nbsp;
                
                <button type="button" class="btn btn-primary font-weight-bolder" id="submit_request_sap">
                    <i class="fas fa-cogs"></i>Request PO Master
                </button> 
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-checkable" id="kt_datatable1">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Vendor</th>
                        <th>Supplying Plant</th>
                        <th>SAP Created Date</th>
                        <th>SAP Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($po_data as $row)
                        <tr>
                            <td>{{ $row["TR_PO_HEADER_NUMBER"] }}</td>
                            <td>{{ $row["TR_PO_HEADER_TYPE"] }}</td>
                            <td>{{ $row["TR_PO_HEADER_STATUS"] }}</td>
                            <td>{{ $row["TR_PO_HEADER_VENDOR"] }}</td>
                            <td>{{ $row["TR_PO_HEADER_SUP_PLANT"] }}</td>
                            <td>{{ $row["TR_PO_HEADER_SAP_CREATED_DATE"] }}</td>
                            <td>{{ $row["TR_PO_HEADER_SAP_CREATED_BY"] }}</td>
                            <td nowrap="nowrap">
                                <a href="{{ route('purchase_order_master_view_detail', ['po_number' => $row["TR_PO_HEADER_NUMBER"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                    class="la la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>
@endsection