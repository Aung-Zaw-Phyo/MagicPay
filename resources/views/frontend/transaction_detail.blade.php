@extends('frontend.layouts.app')
@section('title', 'Transaction Detail')
@section('content')
<div class="transaction_detail">
    <div class="card my-card">
        <div class="card-body">
            <p class="text-center mb-3">
                <img src="{{asset('img/accept.png')}}" alt="">
            </p>

            @if (session('transfer_success'))
                <div class="alert alert-success text-center">
                    {{ session('transfer_success') }}
                </div>
            @endif

            @if ($transaction->type == 1)
                <h6 class="text-success text-center mb-3 fw-bold">{{ number_format($transaction->amount) }} <small>MMK</small></h6>
            @elseif ($transaction->type == 2)
                <h6 class="text-danger text-center mb-3 fw-bold">{{ number_format($transaction->amount) }} <small>MMK</small></h6>
            @endif
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">Trx Id</p>
                <p class="mb-0">{{ $transaction->trx_id }}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">Reference Number</p>
                <p class="mb-0">{{ $transaction->ref_no }}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">Type</p>
                <p class="mb-0">
                    @if ($transaction->type == 1)
                        <span class="badge bg-success p-2">Income</span>
                    @elseif ($transaction->type == 2)
                        <span class="badge bg-danger p-2">Expense</span>
                    @endif
                </p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">Amount</p>
                <p class="mb-0">{{ number_format($transaction->amount) }} <small>MMK</small></p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">Date and Time</p>
                <p class="mb-0">{{ $transaction->created_at }}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">
                    @if ($transaction->type == 1)
                        From
                    @elseif ($transaction->type == 2)
                        To
                    @endif
                </p>
                <p class="mb-0">{{ $transaction->source ? $transaction->source->name : '' }}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <p class="mb-0 text-secondary">Description</p>
                <p class="mb-0">{{ $transaction->description }}</p>
            </div>
            <hr>
        </div>
    </div>
</div>
@endsection
