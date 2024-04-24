@extends('layouts.master')
@section('title')
    @lang('translation.settings')
@endsection
@section('content')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="{{ URL::asset('build/images/auth-one-bg.webp') }}" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    @if (Session::has('success'))
                        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('success') }}</p>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="m-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3">
            <div class="card mt-n5">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                            <img src="@if (Auth::user()->avatar != '') {{ URL::asset(Auth::user()->avatar) }}@else{{ URL::asset('build/images/users/avatar.svg') }} @endif"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image  shadow image-hover"
                                alt="user-profile-image" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            @if (Auth::user()->company_logo)
                                <div class="company-logo-image image-hover">
                                    <img src="{{ Auth::user()->company_logo }}"
                                        class="rounded-circle avatar-sm img-thumbnail user-profile-image  shadow"
                                        alt="company-logo-image" data-bs-toggle="modal" data-bs-target="#companyLogoModal">
                                </div>
                            @endif

                        </div>

                        <h5 class="fs-16 mb-1 text-capitalize">{{ $user->first_name }} {{ $user->last_name }}</h5>

                        @if (Auth::user()->job_title && Auth::user()->company_name)
                            <span class="badge bg-light text-primary mb-0" style="font-size: 12px;">
                                {{ $user->job_title }} at {{ $user->company_name }}
                            </span>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i>
                                Personal Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i>
                                Change Password
                            </a>
                        </li>

                        @if (Auth::user()->hasRole('admin'))
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#setting" role="tab">
                                    <i class="far fa-user"></i>
                                    Settings
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form action="{{ route('updateProfile', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="firstnameInput" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="firstnameInput"
                                                placeholder="Enter your first name" name="first_name"
                                                value="{{ $user->first_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="lastnameInput" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lastnameInput"
                                                placeholder="Enter your last name" name="last_name"
                                                value="{{ $user->last_name }}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="emailInput" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="emailInput"
                                                placeholder="Enter your email" name="email" value="{{ $user->email }}"
                                                required readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="phoneInput" class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="phoneInput"
                                                placeholder="Enter your phone number" name="phone"
                                                value="{{ $user->phone }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="companyNameInput" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="companyNameInput"
                                                placeholder="Enter your company name" name="company_name"
                                                value="{{ $user->company_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="companyJobTitleInput" class="form-label">Job Title</label>
                                            <input type="text" class="form-control" id="companyJobTitleInput"
                                                placeholder="Enter your job title" name="job_title"
                                                value="{{ $user->job_title }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="companyDepartmentInput" class="form-label">Department</label>
                                            <input type="text" class="form-control" id="companyDepartmentInput"
                                                placeholder="Enter your department" name="department"
                                                value="{{ $user->department }}" required>
                                        </div>
                                    </div>
                                    @if (!Auth::user()->company_logo)
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="companyDepartmentInput" class="form-label">Company
                                                    Logo</label>
                                                <input type="button" class="form-control"
                                                    placeholder="Enter your department" value="Upload Company Logo"
                                                    style="text-align: left;" data-bs-toggle="modal"
                                                    data-bs-target="#companyLogoModal">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="{{ route('home') }}" class="btn btn-soft-success">Cancel</a>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>

                        </form>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form action="{{ route('updatePassword', $user->id) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="oldpasswordInput" class="form-label">Old
                                                Password*</label>
                                            <input type="password" name="current_password" class="form-control"
                                                id="oldpasswordInput" placeholder="Enter current password">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="newpasswordInput" class="form-label">New
                                                Password*</label>
                                            <input type="password" name="password" class="form-control"
                                                id="newpasswordInput" placeholder="Enter new password">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Confirm
                                                Password*</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                id="confirmpasswordInput" placeholder="Confirm password">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Change
                                                Password</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>

                        @if (Auth::user()->hasRole('admin'))
                            <div class="tab-pane" id="setting" role="tabpanel">
                                <form action="{{ route('setting.store') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Enter Days Before Expiration is Contract End Date
                                            </label>
                                            <div class="form-group">
                                                <input class="form-control" required
                                                    value="{{ settings('expiration_days') }}" type="number"
                                                    placeholder="i.e 90" name="expiration_days">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label class="form-label">Upload Company Logo</label>
                                                <div class="input-group">
                                                    <input type="file" name="company_logo" class="form-control"
                                                        accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="text-start mt-3">
                                                <button type="submit" class="btn btn-success">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="modal fade" id="avatarModal" tabindex="-1" role="dialog" aria-labelledby="avatarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avatarUploadModalLabel">Upload New Avatar</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('avatar.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="avatar" id="newAvatarInput" class="form-control" required>
                        <div class="image-modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="companyLogoModal" tabindex="-1" role="dialog" aria-labelledby="avatarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avatarUploadModalLabel">Upload New Company Logo</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('company.logo.update', $user->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="company_logo" id="newCompanyLogoInput" class="form-control"
                            required>
                        <div class="image-modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/js/pages/profile-setting.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
