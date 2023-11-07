@extends('layouts.app')

@section('page_title', 'View Pengguna')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/users/view/datatable.js') }}"></script>
<!-- <script src="{{ asset('custom/js/master_data/users/view/delete_ajax.js') }}"></script> -->
@endpush

@section('content')
<div class="card card-custom">
    <div class="card-header py-3">
        <div class="card-title">
            <span class="card-icon">

            </span>
            <h3 class="card-label">Master Data Pengguna</h3>
        </div>
        <div class="card-toolbar">
                <a href="{{ route('master_data_users_add') }}" class="btn btn-primary font-weight-bolder">
                    <i class="fas fa-plus-circle"></i>Add New User
                </a>
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
                    <th>Last Login</th>
                    <th>Login Via SSO</th>
                    <th>Is Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_data as $user)
                <tr>
                    <td>{{ $user["MA_USRACC_FULL_NAME"] }}</td>
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
                        @if ($user["MA_USRACC_LAST_LOGIN_TIMESTAMP"] == "")
                            -
                        @else
                            {{ $user["MA_USRACC_LAST_LOGIN_TIMESTAMP"] }}
                        @endif
                    </td>
                    <td>
                        @if ($user["MA_USRACC_LOGIN_VIA_SSO"] == "t")
                            Yes
                        @else
                            No
                        @endif
                    </td>
                    <td>
                        @if ($user["MA_USRACC_IS_ACTIVE"] == 1)
                            <span class="label label-lg font-weight-bold label-light-success label-inline">Active</span>
                        @else
                            <span class="label label-lg font-weight-bold label-light-danger label-inline">Inactive</span>
                        @endif
                    </td>
                    <td nowrap="nowrap">
                        <a href="{{ route("master_data_users_edit", ["MA_USRACC_ID" => $user["MA_USRACC_ID"]]) }}" class="btn btn-sm btn-clean btn-icon"> <i
                            class="la la-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
