@extends('backend.layouts.app')

@section('title', 'Edit Admin User')
@section('admin-user-active', 'mm-active')

@section('content')

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="metismenu-icon pe-7s-users"></i>
            </div>
            <div>
                Edit Admin User
            </div>
        </div>
    </div>
</div> 

<div class="content pt-3">
    <div class="card">
        <div class="card-body">
            @include('backend.layouts.flash')
            <form action="{{ route('admin.admin-user.update', $admin_user->id) }}" class="content-fm" method="POST" id="update">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="" class="mb-2">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $admin_user->name }}">
                </div>
                <div class="form-group mb-3">
                    <label for="" class="mb-2">Email</label>
                    <input type="email" name="email" class="form-control"  value="{{ $admin_user->email }}">
                </div>
                <div class="form-group mb-3">
                    <label for="" class="mb-2">Phone</label>
                    <input type="number" name="phone" class="form-control"  value="{{ $admin_user->phone }}">
                </div>
                <div class="form-group  mb-3">
                    <label for="" class="mb-2">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div>
                    <button class="btn btn-secondary me-3 content-fm back-btn">Cancel</button>
                    <button class="btn btn-primary content-fm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
@endsection

@section('script')

{!! JsValidator::formRequest('App\Http\Requests\UpdateAdminUser', '#update') !!}
<script>
    $(document).ready(function () {
        
    });
</script>
@endsection