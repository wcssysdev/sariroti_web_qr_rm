@extends('layouts.app')

@section('page_title', 'View Plant')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/plant/view/datatable.js') }}"></script>
<script src="{{ asset('custom/js/master_data/plant/view/delete_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/plant/view/submit_ajax.js') }}"></script>
@endpush

@section('content')
<form class="form" id="form" action="{{ route('master_data_plant_sync_sap') }}" data-form-success-redirect="{{ route('master_data_plant_view') }}" action2 = "{{ route("master_data_plant_request_sap") }}">
    @csrf
    <div class="card card-custom">
        <div class="card-header py-3">
            <div class="card-title">
                <span class="card-icon">                 
                    <h3 class="card-label">Master Data Plant</h3>
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
                        <th>Plant Code</th>
                        <th>Plant Name</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Postal Code</th>
                        <th>Phone Number</th>
                        <th>Fax</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                    <tr>
                        <td>{{ $row["MA_PLANT_CODE"] }}</td>
                        <td>{{ $row["MA_PLANT_NAME"] }}</td>
                        <td>{{ $row["MA_PLANT_STREET"] }}</td>
                        <td>{{ $row["MA_PLANT_CITY"] }}</td>
                        <td>{{ $row["MA_PLANT_POSTAL_CODE"] }}</td>
                        <td>{{ $row["MA_PLANT_TELP"] }}</td>
                        <td>{{ $row["MA_PLANT_FAX"] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>
@endsection