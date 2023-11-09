@extends('layouts.app')

@section('page_title', 'List PO - GI')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/transaction/gi_sto/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/transaction/gi_sto/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/transaction/gi_sto/view/select2.js') }}"></script>
@endpush

@section('content')
@if (session("fifo_res"))

<div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
    <div class="alert-icon"><i class="flaticon-warning"></i></div>
    <div class="alert-text">Gagal Dalam Membuat GI, Material {{ session("fifo_res")["material_code"] }} {{ session("fifo_res")["material_name"] }} tidak mencukupi!</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
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
                <div class="col-lg-3">
                    <label>Vendor:</label>
                    <select class="form-control" id="vendor_select2" name="vendor_code">
                        <option></option>
                        @foreach ($vendor as $row)
                            <option value="{{ $row["MA_VENDOR_CODE"] }}" 
                            @if ($row["MA_VENDOR_CODE"] == $vendor_selected)
                            selected
                            @endif>{{ $row["MA_VENDOR_CODE"] }} - {{ $row["MA_VENDOR_NAME"] }}</option>
                        @endforeach
                    </select>
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
            <h3 class="card-label">List PO - Good Issue</h3>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>PO SAP Number</th>
                    <th>PO Document Type</th>
                    <th>Vendor Name</th>
                    <th>Plant</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["TR_PO_HEADER_NUMBER"] }}</td>
                        <td>{{ $row["TR_PO_HEADER_TYPE"] }}</td>
                        <td>{{ $row["TR_PO_HEADER_VENDOR"].' '.$row["MA_VENDOR_NAME"] }}</td>
                        <td>{{ $row["TR_PO_HEADER_SUP_PLANT"].' '.$row["MA_PLANT_NAME"] }}</td>
                        <td>
                            @if ($row["TR_PO_HEADER_STATUS"] == "I")
                                Insert
                            @elseif ($row["TR_PO_HEADER_STATUS"] == "U")
                                Update
                            @elseif ($row["TR_PO_HEADER_STATUS"] == "D")
                                Delete
                            @endif
                        </td>
                        <td nowrap="nowrap">
                            <a href="{{ route('purchase_order_good_issue_detail', ['gi_po_number' => $row["TR_PO_HEADER_NUMBER"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
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