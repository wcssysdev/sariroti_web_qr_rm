@extends('layouts.app')

@section('page_title', 'Home Page')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/home/dashboard/datatable.js') }}"></script>
@endpush

@section('content')
@if (session("message"))

<div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
    <div class="alert-icon"><i class="flaticon-warning"></i></div>
    <div class="alert-text">{{ session("message") }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Pending / Error Integration Notification (Good Receipt)</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable1">
                            <thead>
                                <tr>
                                    <th>GR Number</th>
                                    <th>PO Number</th>
                                    <th>Plant Code</th>
                                    <th>Document Date</th>
                                    <th>Status</th>
                                    <th>SAP Error Message</th>
                                    <th>Created Timestamp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gr_data as $row)
                                <tr>
                                    <td>{{ $row["TR_GR_HEADER_ID"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_PO_NUMBER"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_PLANT_CODE"] }}</td>
                                    <td>{{ convert_to_web_dmy($row["TR_GR_HEADER_DOC_DATE"]) }}</td>
                                    <td>{{ $row["TR_GR_HEADER_STATUS"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_ERROR"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_CREATED_TIMESTAMP"] }}</td>
                                    <td>
                                        @if ($row["TR_GR_HEADER_STATUS"] == "ERROR")
                                        <a href="{{ route('repost_gr', ['id' => $row["TR_GR_HEADER_ID"]])}}" class="btn btn-sm btn-clean btn-icon"> 
                                            <i class="la la-refresh"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Pending / Error Integration Notification (Good Issue)</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable2">
                            <thead>
                                <tr>
                                    <th>GI Number</th>
                                    <th>PO Number</th>
                                    <th>Plant Code</th>
                                    <th>Document Date</th>
                                    <th>Status</th>
                                    <th>SAP Error Message</th>
                                    <th>Created Timestamp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gi_data as $row)
                                <tr>
                                    <td>{{ $row["TR_GI_SAPHEADER_ID"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_PO_NUMBER"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_PLANT_CODE"] }}</td>
                                    <td>{{ convert_to_web_dmy($row["TR_GI_SAPHEADER_DOC_DATE"]) }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_STATUS"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_ERROR"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_CREATED_TIMESTAMP"] }}</td>
                                    <td>
                                        @if ($row["TR_GI_SAPHEADER_STATUS"] == "ERROR")
                                        <a href="{{ route('repost_gi', ['id' => $row["TR_GI_SAPHEADER_ID"]])}}" class="btn btn-sm btn-clean btn-icon"> 
                                            <i class="la la-refresh"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Pending / Error Integration Notification (Transfer Posting)</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable3">
                            <thead>
                                <tr>
                                    <th>TP Number</th>
                                    <th>Plant Code</th>
                                    <th>Document Date</th>
                                    <th>Status</th>
                                    <th>SAP Error Message</th>
                                    <th>Created Timestamp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tp_data as $row)
                                <tr>
                                    <td>{{ $row["TR_TP_HEADER_ID"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_PLANT_CODE"] }}</td>
                                    <td>{{ convert_to_web_dmy($row["TR_TP_HEADER_DOC_DATE"]) }}</td>
                                    <td>{{ $row["TR_TP_HEADER_STATUS"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_ERROR"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_CREATED_TIMESTAMP"] }}</td>
                                    <td>
                                        @if ($row["TR_TP_HEADER_STATUS"] == "ERROR")
                                        <a href="{{ route('repost_tp', ['id' => $row["TR_TP_HEADER_ID"]])}}" class="btn btn-sm btn-clean btn-icon"> 
                                            <i class="la la-refresh"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Pending / Error Integration Notification (Cancellation)</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable4">
                            <thead>
                                <tr>
                                    <th>Cancellation Number</th>
                                    <th>Plant Code</th>
                                    <th>Movement Code</th>
                                    <th>Status</th>
                                    <th>SAP Error Message</th>
                                    <th>Created Timestamp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cancellation_data as $row)
                                <tr>
                                    <td>{{ $row["TR_CANCELLATION_MVT_ID"] }}</td>
                                    <td>{{ $row["TR_CANCELLATION_PLANT_CODE"] }}</td>
                                    <td>{{ $row["TR_CANCELLATION_MVT_SAP_CODE"] }}</td>
                                    <td>{{ $row["TR_CANCELLATION_MVT_STATUS"] }}</td>
                                    <td>{{ $row["TR_CANCELLATION_MVT_ERROR"] }}</td>
                                    <td>{{ $row["TR_CANCELLATION_MVT_CREATED_TIMESTAMP"] }}</td>
                                    <td>
                                        @if ($row["TR_CANCELLATION_MVT_STATUS"] == "ERROR")
                                        <a href="{{ route('repost_cancellation', ['id' => $row["TR_CANCELLATION_MVT_ID"]])}}" class="btn btn-sm btn-clean btn-icon"> 
                                            <i class="la la-refresh"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Pending / Error Integration Notification (PID)</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable5">
                            <thead>
                                <tr>
                                    <th>PID Number</th>
                                    <th>Plant Code</th>
                                    <th>Document Date</th>
                                    <th>Status</th>
                                    <th>SAP Error Message</th>
                                    <th>Created Timestamp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pid_data as $row)
                                <tr>
                                    <td>{{ $row["TR_PID_HEADER_ID"] }}</td>
                                    <td>{{ $row["TR_PID_HEADER_PLANT"] }}</td>
                                    <td>{{ convert_to_web_dmy($row["TR_PID_HEADER_SAP_CREATED_DATE"]) }}</td>
                                    <td>{{ $row["TR_PID_HEADER_STATUS"] }}</td>
                                    <td>{{ $row["TR_PID_HEADER_SAP_RETURN_ERROR"] }}</td>
                                    <td>{{ $row["TR_PID_HEADER_CREATED_TIMESTAMP"] }}</td>
                                    <td>
                                        @if ($row["TR_PID_HEADER_STATUS"] == "ERROR")
                                        <a href="{{ route('repost_pid', ['id' => $row["TR_PID_HEADER_ID"]])}}" class="btn btn-sm btn-clean btn-icon"> 
                                            <i class="la la-refresh"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
