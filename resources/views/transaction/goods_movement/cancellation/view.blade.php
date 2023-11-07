@extends('layouts.app')

@section('page_title', 'List Cancellation MVT')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/gi_sto/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/gi_sto/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/transaction/gi_sto/view/select2.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom gutter-b">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <form class="form" id="form" method="GET" action="">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>Document Date: </label>
                    <div class="input-daterange input-group" id="kt_datepicker_5">
                        <input type="text" class="form-control date" name="start_date" autocomplete="off" placeholder="Start" value="{{ convert_to_web_dmy($start) }}">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                -
                            </span>
                        </div>
                        <input type="text" class="form-control date" name="end_date" autocomplete="off" placeholder="End" value="{{ convert_to_web_dmy($end) }}">
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
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">

            </span>
            <h3 class="card-label">List Cancellation MVT</h3>
        </div>
        <div class="card-toolbar">
            @if (session('user_role') == 2 || session('user_role') == 4 || session('user_role') == 5)
            <a href="{{ route("transaction_goods_movement_cancellation_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add New Cancellation MVT
            </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>ID Document</th>
                    <th>Movement Code</th>
                    <th>SAP Document No</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Error Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["TR_CANCELLATION_MVT_ID"] }}</td>
                        <td>{{ $row["TR_CANCELLATION_MVT_SAP_CODE"] }}</td>
                        <td>{{ $row["TR_CANCELLATION_MVT_MATDOC"] }}</td>
                        <td>{{ $row["TR_CANCELLATION_MVT_NOTES"] }}</td>
                        <td>{{ $row["TR_CANCELLATION_MVT_STATUS"] }}</td>
                        <td>{{ $row["TR_CANCELLATION_MVT_ERROR"] }}</td>
                        <td nowrap="nowrap">
                            <a href="{{ route('transaction_goods_movement_cancellation_view_detail', ['cancellation_id' => $row["TR_CANCELLATION_MVT_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                class="la la-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection