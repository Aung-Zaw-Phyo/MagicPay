@extends('frontend.layouts.app_plain')
@section('title', 'OTP Verification')
@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh">
        <div class="col-md-6 col-lg-5">
            <div class="card px-2 auth-form">
                <div class="card-body">
                    <h3 class="text-center">OTP Verification</h3>
                    <form method="POST" action="{{ route('post_register') }}" class="">
                        @csrf
                        <input type="text" name="name" value="{{ old('name', $data?$data['name']:null) }}" hidden>
                        <input type="text" name="phone" value="{{ old('phone', $data?$data['phone']:null) }}" hidden>
                        <input type="text" name="email" value="{{ old('email', $data?$data['email']:null) }}" hidden>
                        <input type="text" name="password" value="{{ old('password', $data?$data['password']:null) }}" hidden>
                        <input type="hidden" class="form-control" name="device_token" id="device_token_input">
                        @error('device_token')
                            <div class="alert alert-warning">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-group mb-4">
                            <label for="otp" class="form-label">OTP</label>
                            <input type="number" class="form-control @error('otp') is-invalid @enderror" name="otp" id="otp"  value="{{ old('otp') }}" autocomplete="otp" autofocus>
                            @error('otp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <button type="submit" class="btn btn-theme w-100">
                                Submit
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
