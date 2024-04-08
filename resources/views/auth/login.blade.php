@extends('frontend.layouts.app_plain')
@section('title', 'Magic Pay | Login')
@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh">
        <div class="col-md-6 col-lg-5">
            <div class="card px-2 auth-form">
                <div class="card-body">
                    <h3 class="text-center">Login</h3>
                    <p class="text-center text-muted content-fm">Fill the form to login</p>
                    <form method="POST" action="{{ route('login') }}" class="">
                        @csrf

                        <div class="form-group mb-4">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone"  value="{{ old('phone') }}" autocomplete="phone" autofocus>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password"  autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <button type="submit" class="btn btn-theme w-100">
                                {{ __('Login') }}
                            </button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a class="" href="{{ route('register') }}">Register Now</a>
                            @if (Route::has('password.request'))
                                <a class="" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
