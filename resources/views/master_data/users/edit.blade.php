@extends('layouts.app')

@section('page_title', 'Edit User')

@push('styles')

@endpush

@push('scripts')
<script src="{{ asset('custom/js/master_data/users/edit/select2.js') }}"></script>
<script src="{{ asset('custom/js/master_data/users/edit/submit_ajax.js') }}"></script>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <h3 class="card-title">Edit User Form - {{ $user_data["MA_USRACC_FULL_NAME"] }}</h3>
            </div>
            <form class="form" id="form" method="POST" action="{{ route("master_data_users_update") }}" data-form-success-redirect="{{ route("master_data_users_view") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="MA_USRACC_ID" value="{{ $user_data["MA_USRACC_ID"] }}">
                <div class="card-body">
                    <h3 class="font-size-lg text-dark font-weight-bold mb-6">1. Personal Data</h3>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>Full Name:</label>
                            <input type="text" maxlength="255" class="form-control max_length_input" name="MA_USRACC_FULL_NAME" placeholder="Masukkan nama lengkap pengguna akun" value="{{ $user_data["MA_USRACC_FULL_NAME"] }}">
                        </div>
                        <div class="col-lg-4">
                            <label>Email:</label>
                            <input type="email" maxlength="255" class="form-control max_length_input" name="MA_USRACC_EMAIL" placeholder="Masukkan alamat email pengguna akun" value="{{ $user_data["MA_USRACC_EMAIL"] }}">
                        </div>
                        <div class="col-lg-4">
                            <label>Plant Code:</label>
                            <div class="input-group">
                                <input type="text" maxlength="50" class="form-control max_length_input" name="MA_USRACC_PLANT_CODE" placeholder="Masukkan plant code pengguna akun " value="{{ $user_data["MA_USRACC_PLANT_CODE"] }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>User Role </label>
                                <select class="form-control" id="role_select2" name="MA_USRACC_ROLE">
                                    <option value="1" <?php echo ($user_data['MA_USRACC_ROLE']=='1')?'selected="selected"':'' ?>>Admin</option>
                                    <option value="2" <?php echo ($user_data['MA_USRACC_ROLE']=='2')?'selected="selected"':'' ?>>PPIC</option>
                                    <option value="3" <?php echo ($user_data['MA_USRACC_ROLE']=='3')?'selected="selected"':'' ?>>Warehouse Mobile</option>
                                    <option value="4" <?php echo ($user_data['MA_USRACC_ROLE']=='4')?'selected="selected"':'' ?>>Costing</option>
                                    <option value="5" <?php echo ($user_data['MA_USRACC_ROLE']=='5')?'selected="selected"':'' ?>>Warehouse</option>
                                    <option value="6" <?php echo ($user_data['MA_USRACC_ROLE']=='6')?'selected="selected"':'' ?>>Head PPIC</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>Is Active:</label>
                            <div class="row">
                                <div class="col-9 col-form-label">
                                    <div class="radio-inline">
                                        <label class="radio radio-primary">
                                            <input type="radio" value="1" name="MA_USRACC_IS_ACTIVE" <?php echo ($user_data['MA_USRACC_IS_ACTIVE'] == '1')?'checked':'' ?>>
                                            <span></span>Active</label>
                                        <label class="radio radio-primary">
                                            <input type="radio" value="0" name="MA_USRACC_IS_ACTIVE" <?php echo ($user_data['MA_USRACC_IS_ACTIVE'] == '0')?'checked':'' ?>>
                                            <span></span>InActive</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>Login Via SSO?:</label>
                            <div class="row">
                                <div class="col-9 col-form-label">
                                    <div class="radio-inline">
                                        <label class="radio radio-primary">
                                            <input type="radio" value="1" name="MA_USRACC_LOGIN_VIA_SSO" <?php echo ($user_data['MA_USRACC_LOGIN_VIA_SSO']=='1')?'checked':'' ?>>
                                            <span></span>Yes</label>
                                        <label class="radio radio-primary">
                                            <input type="radio" value="0" name="MA_USRACC_LOGIN_VIA_SSO" <?php echo ($user_data['MA_USRACC_LOGIN_VIA_SSO']=='0')?'checked':'' ?>>
                                            <span></span>No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label>Password:</label>
                            <div class="input-group">
                                <input type="password" maxlength="50" class="form-control max_length_input" name="MA_USRACC_PASSWORD" placeholder="Input Password (If Login With SSO No)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route("master_data_users_view") }}" class="btn btn-secondary">Back</a>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
