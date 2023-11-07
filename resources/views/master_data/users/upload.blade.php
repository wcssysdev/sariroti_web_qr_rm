@extends('layouts.app')

@section('page_title', 'Upload Pengguna')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/users/upload/datatable.js') }}"></script>
<script src="{{ asset('custom/js/master_data/users/upload/submit_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/users/upload/clear_ajax.js') }}"></script>
<script src="{{ asset('custom/js/master_data/users/upload/save_uploaded.js') }}"></script>
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">

            </span>
            <h3 class="card-label">Upload Data Pengguna</h3>
        </div>
        <div class="card-toolbar">
            <form action="{{ route('master_data_users_upload') }}" class="form" id="form" method="POST" data-form-success-redirect="{{ route('master_data_users_upload_view') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Upload File</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" accept=".xlsx" id="customFile"/>
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary mr-2">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-checkable" id="kt_datatable1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Plant Code</th>
                    <th>Role</th>
                    <th>Is Active?</th>
                    <th>Login Via SSO?</th>
                </tr>
            </thead>
            <tbody>
                @if($user_data)
                @foreach ($user_data as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="ml-3">
                            <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">{{ $user["MA_USRACC_FULL_NAME"] }}</span>

                            </div>
                        </div>
                    </td>
                    <td><a target="_blank" href="mailto: {{ $user["MA_USRACC_EMAIL"] }}">{{ $user["MA_USRACC_EMAIL"] }}</a></td>
                    <td><a href="tel:{{ $user["MA_USRACC_PLANT_CODE"] }}">{{ $user["MA_USRACC_PLANT_CODE"] }}</a></td>
                    <td>
                        @if ($user["MA_USRACC_ROLE"] == 1)
                        Admin
                        @elseif ($user["MA_USRACC_ROLE"] == 2)
                        PPIC
                        @elseif ($user["MA_USRACC_ROLE"] == 3)
                        Warehouse Mobile
                        @elseif ($user["MA_USRACC_ROLE"] == 4)
                        Costing
                        @elseif ($user["MA_USRACC_ROLE"] == 5)
                        Warehouse
                        @elseif ($user["MA_USRACC_ROLE"] == 6)
                        Head PPIC
                        @endif
                    </td>
                    <td>
                        @if ($user["MA_USRACC_IS_ACTIVE"] == 1)
                            <span class="label label-lg font-weight-bold label-light-success label-inline">Active</span>
                        @else
                            <span class="label label-lg font-weight-bold label-light-danger label-inline">In Active</span>
                        @endif
                    </td>
                    <td>
                        @if ($user["MA_USRACC_LOGIN_VIA_SSO"] == 1)
                            <span class="label label-lg font-weight-bold label-light-success label-inline">Yes</span>
                        @else
                            <span class="label label-lg font-weight-bold label-light-danger label-inline">No</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-lg-6">
                <a href="{{ route('master_data_users_add') }}" class="btn btn-secondary">Back</a>
                <a href="javascript:;" data-redirect="{{ route("master_data_users_upload_clear") }}" class="btn btn-primary" id="clear_btn">Clear</a>
            </div>
            <div class="col-lg-6 text-right">
                <a href="javascript:;" data-action="{{ route("master_data_users_upload_save") }}" data-success-redirect="{{ route("master_data_users_view") }}" class="btn btn-primary mr-2" id="save_btn">Save</a>
            </div>
        </div>
    </div>
</div>
@endsection
