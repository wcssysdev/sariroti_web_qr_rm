@extends('layouts.app')

@section('page_title', 'Detail Material Page')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/home/dashboard/datatable.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Stock Batch</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable1">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>SAP Document</th>
                                    <th>Posting Date</th>
                                    <th>Expired Date</th>
                                    <th>Qty</th>
                                    <th>Actual Qty</th>
                                    <th>Storage Loc</th>
                                    <th>SAP Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gr_data as $row)
                                <tr>
                                    <td>{{ $row["TR_GR_HEADER_PO_NUMBER"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_SAP_DOC"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_PSTG_DATE"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_EXP_DATE"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_GR_DETAIL_BASE_QTY"])." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_GR_DETAIL_LEFT_QTY"])." ".$row["TR_GR_DETAIL_BASE_UOM"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_SLOC"] }}</td>
                                    <td>{{ $row["TR_GR_HEADER_STATUS"] }}</td>
                                    <td>
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
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Good Issue History</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable2">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>SAP Document</th>
                                    <th>Posting Date</th>
                                    <th>Expired Date</th>
                                    <th>Batch Code</th>
                                    <th>Qty Issued</th>
                                    <th>SAP Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gi_data as $row)
                                <tr>
                                    <td>{{ $row["TR_GI_SAPHEADER_PO_NUMBER"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_SAP_DOC"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_PSTG_DATE"] }}</td>
                                    <td>{{ $row["TR_GR_DETAIL_EXP_DATE"] }}</td>
                                    <td>{{ $row["TR_GI_SAPDETAIL_SAP_BATCH"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_GI_SAPDETAIL_BASE_QTY"])." ".$row["TR_GI_SAPDETAIL_BASE_UOM"] }}</td>
                                    <td>{{ $row["TR_GI_SAPHEADER_STATUS"] }}</td>
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
                    <span class="card-label font-weight-bolder text-dark">Transfer Posting History</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable3">
                            <thead>
                                <tr>
                                    <th>SAP Document</th>
                                    <th>Posting Date</th>
                                    <th>Qty Issued</th>
                                    <th>SAP Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tp_data as $row)
                                <tr>
                                    <td>{{ $row["TR_TP_HEADER_SAP_DOC"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_PSTG_DATE"] }}</td>
                                    <td style="text-align:right;">{{ number_format($row["TR_TP_DETAIL_BASE_QTY"])." ".$row["TR_TP_DETAIL_BASE_UOM"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_STATUS"] }}</td>
                                     <td nowrap="nowrap">
                                        <a href="{{ route('goods_movement_transfer_posting_detail', ['tp_header_id' => $row["TR_TP_HEADER_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
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
        </div>
    </div>
</div>

{{-- <div class="row">
    <div class="col-xl-12">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Cancellation MVT History</span>
                </h3>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-checkable" id="kt_datatable4">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Plant Code</th>
                                    <th>SAP Document</th>
                                    <th>Posting Date</th>
                                    <th>Document Date</th>
                                    <th>Bill of Landing</th>
                                    <th>Movement Code</th>
                                    <th>Qty</th>
                                    <th>UOM</th>
                                    <th>SAP Year</th>
                                    <th>SAP Status</th>
                                    <th>Created Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tp_data as $row)
                                <tr>
                                    <td>{{ $row["TR_TP_HEADER_PLANT_CODE"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_SAP_DOC"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_PSTG_DATE"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_DOC_DATE"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_BOL"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_MVT_CODE"] }}</td>
                                    <td>{{ $row["TR_TP_DETAIL_QTY"] }}</td>
                                    <td>{{ $row["TR_TP_DETAIL_UOM"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_SAP_YEAR"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_STATUS"] }}</td>
                                    <td>{{ $row["TR_TP_HEADER_CREATED_TIMESTAMP"] }}</td>
                                   
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection