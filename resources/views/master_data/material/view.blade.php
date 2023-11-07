@extends('layouts.app')

@section('page_title', 'View Material')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/material/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/master_data/material/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/material/view/submit_ajax.js') }}"></script>
@endpush

@section('content')
<form class="form" id="form" action="{{ route('master_data_material_sync_sap') }}" data-form-success-redirect="{{ route('master_data_material_view') }}" action2 = "{{ route("master_data_material_request_sap") }}">
    @csrf
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">                 
                <h3 class="card-label">Master Data Material</h3>
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
                    <th>Material ID</th>
                    <th>Description</th>
                    <th>Tipe</th>
                    <th>Group</th>
                    <th>Plant</th>
                    <th>Base UOM</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row["MA_MATL_CODE"] }}</td>
                    <td>{{ $row["MA_MATL_DESC"] }}</td>
                    <td>{{ $row["MA_MATL_TYPE"] }}</td>
                    <td>{{ $row["MA_MATL_GROUP"] }}</td>
                    <td>{{ $row["MA_MATL_PLANT"] }}</td>
                    <td>{{ $row["MA_MATL_UOM"] }}</td>
                    <td nowrap="nowrap">
                            <a href="{{ route('master_data_material_detail', ['code' => $row['MA_MATL_CODE']]) }}" class="btn btn-sm btn-clean btn-icon"> <i
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