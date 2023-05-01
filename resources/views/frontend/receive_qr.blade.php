@extends('frontend.layouts.app')
@section('title', 'Receive QR')
@section('content')
<div class="receive-qr">
    <div class="card my-card">
        <div class="card-body">
            <p class="text-center mb-0 h5">QR Scan to pay me</p>
            <div class="text-center my-3">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($authUser->phone)) !!} ">
            </div>
            <p class="text-center mb-1 fw-bold h5">{{ $authUser->name }}</p>
            <p class="text-center mb-0">{{ $authUser->phone }}</p>
        </div>
    </div>
</div>
@endsection
