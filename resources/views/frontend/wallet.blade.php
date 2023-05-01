@extends('frontend.layouts.app')
@section('title', 'Wallet')
@section('content')
<div class="wallet">
    <div class="card my-card">
        <div class="card-body">
            <div class="mb-4">
                <span>Balance</span>
                <h3>{{ number_format($user->wallet ? $user->wallet->amount : '-') }} <span>MMK</span></h3>
            </div>
            <div class="mb-4">
                <span>Account Number</span>
                <h4>{{ $user->wallet ? $user->wallet->account_number : '-' }}</h4>
            </div>
            <div>
                <p>{{ $user->name }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
