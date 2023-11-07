@extends('layouts.app')

@section('page_title', 'List Stock Opname')

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
                    <label>PO Date: </label>
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
                {{-- <div class="col-lg-3">
                    <label>Plant:</label>
                    <select class="form-control" id="plant_select2" name="plant_code">
                        <option></option>
                        @foreach ($plant as $row)
                            <option value="{{ $row["MA_PLANT_CODE"] }}" 
                            @if ($row["MA_PLANT_CODE"] == $plant_selected)
                                selected
                            @endif>{{ $row["MA_PLANT_CODE"] }} - {{ $row["MA_PLANT_NAME"] }}</option>
                        @endforeach
                    </select>
                </div> --}}
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
            <h3 class="card-label">List Stock Opname</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>PID SAP Number</th>
                    <th>PID SAP Year</th>
                    <th>PID Status</th>
                    <th>PID SAP Created Date</th>
                    <th>Plant</th>
                    <th>Approval Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["TR_PID_HEADER_SAP_NO"] }}</td>
                        <td>{{ $row["TR_PID_HEADER_YEAR"] }}</td>
                        <td>{{ $row["TR_PID_HEADER_STATUS"] }}</td>
                        <td>{{ $row["TR_PID_HEADER_SAP_CREATED_DATE"] }}</td>
                        <td>{{ $row["TR_PID_HEADER_PLANT"] }}</td>
                        <td>{{ $row["TR_PID_HEADER_APPROVAL_STATUS"] }}</td>
                        <td nowrap="nowrap">
                            @if ($row["TR_PID_MOBILE_ALLOW_TO_INPUT"] == true)
                            <a href="{{ route('transaction_stock_opname_view_detail_edit_material', ['pid_header_id' => $row["TR_PID_HEADER_SAP_NO"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                                class="la la-pencil"></i>
                            </a>
                            @endif
                            <a href="{{ route('transaction_stock_opname_view_detail', ['pid_id' => $row["TR_PID_HEADER_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
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