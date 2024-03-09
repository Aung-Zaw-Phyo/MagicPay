@extends('frontend.layouts.app_plain')
@section('title', 'Account Status')
@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh">
        <div class="col-md-6">
            <div class="px-2 text-center">
                @if ($status)
                    <h1>Your account has been verified successfully</h1>
                    <a href="{{ route('home') }}">Go Home</a>
                @else
                    <h1>Verification Failed!</h1>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
