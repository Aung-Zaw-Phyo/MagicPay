@extends('frontend.layouts.app')
@section('title', 'Magic Pay')
@section('content')
<div class="home">
    <div class="profile text-center mb-3">
        <img src="https://ui-avatars.com/api/?background=5842e3&color=fff&name={{ $user->name }}" alt="">
        <h6 class="mt-2">{{ $user->name }}</h6>
        <div>{{ $user->wallet ? number_format($user->wallet->amount) : '0' }} MMK</div>
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <a href="{{ url('scan-and-pay') }}">
                <div class="card short-cut-box">
                    <div class="card-body p-3 d-flex align-items-center">
                        <img src="{{ asset('img/qr-code-scan.png') }}" alt="">
                        <span>Scan & Pay</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 mb-3">
            <a href="{{ url('receive-qr') }}">
                <div class="card short-cut-box">
                    <div class="card-body p-3 d-flex align-items-center">
                        <img src="{{ asset('img/qr-code.png') }}" alt="">
                        <span>Receive QR</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12">
            <div class="card function-box">
                <div class="card-body pe-0">
                    <a href="{{ route('transfer') }}" class="d-flex justify-content-between">
                        <span><img src="{{ asset('img/money-transfer.png') }}" alt=""> Trsnsfer</span>
                        <i class="fas fa-angle-right me-3"></i>
                    </a>
                    <hr>
                    <a href="{{ route('wallet') }}" class="d-flex justify-content-between">
                        <span><img src="{{ asset('img/wallet.png') }}" alt=""> Wallet</span>
                        <i class="fas fa-angle-right me-3"></i>
                    </a>
                    <hr>
                    <a href="{{url('transaction')}}" class="d-flex justify-content-between">
                        <span><img src="{{ asset('img/transaction.png') }}" alt=""> Transaction</span>
                        <i class="fas fa-angle-right me-3"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
