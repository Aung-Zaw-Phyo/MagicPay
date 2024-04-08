@extends('frontend.layouts.app_plain')
@section('title', 'Magic Pay | Register')
@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh">
        <div class="col-md-6 col-lg-5">
            <div class="card px-2 auth-form">
                <div class="card-body">
                    <h3 class="text-center">Register</h3>
                    <p class="text-center text-muted content-fm">Fill the form to register</p>
                    <form method="POST" action="{{ route('register') }}" class="">
                        @csrf

                        <input type="hidden" class="form-control" name="device_token" id="device_token_input">
                        @error('device_token')
                            <div class="alert alert-warning">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name"  value="{{ old('name') }}" autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email"  value="{{ old('email') }}" autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone"  value="{{ old('phone') }}" autocomplete="phone">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" >
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" >
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-theme w-100">
                                Register
                            </button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a class="" href="{{ route('login') }}">Already have an account?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
