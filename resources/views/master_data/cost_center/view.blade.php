@extends('layouts.app')

@section('page_title', 'View Cost Center')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/costCenter/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/master_data/costCenter/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/costCenter/view/submit_ajax.js') }}"></script>
@endpush

@section('content')
<form class="form" id="form" action="{{ route('master_data_cost_center_sync_sap') }}" data-form-success-redirect="{{ route('master_data_cost_center_view') }}" action2 = "{{ route("master_data_cost_center_request_sap") }}">
    @csrf
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">                 
                <h3 class="card-label">Master Data Cost Center</h3>
            </span>
        </div>
        <div class="card-toolbar">
            @if (session('user_role') == 1)
            <button type="button" class="btn btn-primary font-weight-bolder" id="submit_sync_sap">
                <i class="fas fa-cog"></i>Sync Master Data
            </button> 
            &nbsp;
            
            <button type="button" class="btn btn-primary font-weight-bolder" id="submit_request_sap">
                <i class="fas fa-cogs"></i>Request Master Data
            </button>
            @endif
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Cost Center</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["MA_COSTCNTR_CODE"] }}</td>
                        <td>{{ $row["MA_COSTCNTR_DESC"] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</form>
@endsection