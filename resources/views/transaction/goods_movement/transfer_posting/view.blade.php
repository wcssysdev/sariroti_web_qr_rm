@extends('layouts.app')

@section('page_title', 'List Transfer Posting')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/gi_sto/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/gi_sto/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/transaction/gi_sto/view/select2.js') }}"></script>
<script src="{{ asset('custom/js/transaction/delete/submit_ajax.js?=1') }}"></script>
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
                    <label>Transfer Posting Date: </label>
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
            <h3 class="card-label">List Transfer Posting</h3>
        </div>
        <div class="card-toolbar">
            @if (session('user_role') == 2 || session('user_role') == 4)
            <a href="{{ route("goods_movement_transfer_posting_add") }}" class="btn btn-primary font-weight-bolder">
                <i class="fas fa-plus-circle"></i>Add New Transfer Posting
            </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>ID Document</th>
                    <th>SAP Document</th>
                    <th>Posting Date</th>
                    <th>Requested Date</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Error Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["TR_TP_HEADER_ID"] }}</td>
                    <td>{{ $row["TR_TP_HEADER_SAP_DOC"] }}</td>
                    <td>@if ($row["TR_TP_HEADER_PSTG_DATE"] != "")
                        {{ convert_to_web_dmy($row["TR_TP_HEADER_PSTG_DATE"]) }}
                    @endif</td>
                    <td>{{ $row["TR_TP_HEADER_CREATED_TIMESTAMP"] }}</td>
                    <td>{{ $row["TR_TP_HEADER_TXT"] }}</td>
                    <td>{{ $row["TR_TP_HEADER_STATUS"] }}</td>
                    <td>{{ $row["TR_TP_HEADER_ERROR"] }}</td>
                    <td nowrap="nowrap">
                        <a href="{{ route('goods_movement_transfer_posting_detail', ['tp_header_id' => $row["TR_TP_HEADER_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                            class="la la-eye"></i>
                        </a>
                        <a href="{{ route('goods_movement_transfer_posting_detail_print', ['tp_header_id' => $row["TR_TP_HEADER_ID"]]) }}" target="_blank" class="btn btn-sm btn-clean btn-icon"> <i
                            class="la la-print"></i>
                        </a>
                        @if ($row["TR_TP_HEADER_STATUS"] == "ERROR" && (session("user_role") == "2" || session("user_role") == "4"))
                            <a data-action="{{ route('goods_movement_transfer_posting_delete', ['tp_header_id' => $row["TR_TP_HEADER_ID"]]) }}"
                            data-form-success-redirect="{{ route('goods_movement_transfer_posting_view') }}"
                            class="btn btn-sm btn-clean btn-icon" id="delete_transaction_button"> <i
                            class="la la-trash"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection