@extends('frontend.layouts.app')
@section('title', 'Transfer Confirmation')
@section('content')
<div class="">
    <div class="card">
        <div class="card-body">
            @include('backend.layouts.flash')
            <form action="{{ route('transfer.complete') }}" method="POST" id="form">
                @csrf
                <input type="hidden" name="hash_value" value="{{ $hash_value }}">
                <input type="hidden" name="to_phone" value="{{ $to_account->phone }}">
                <input type="hidden" name="amount" value="{{ $amount }}">
                <input type="hidden" name="description" value="{{ $description }}">
                {{-- @error('amount')
                    <div class="alert alert-warning mb-3">{{ $message }}</div>
                @enderror
                @error('to_phone')
                    <div class="alert alert-warning mb-3">{{ $message }}</div>
                @enderror
                @error('fail')
                    <div class="alert alert-warning mb-3">{{ $message }}</div>
                @enderror --}}
                <div class="form-group mb-3">   
                    <label for="" class="mb-1"><strong>From</strong></label>
                    <p class="mb-1 text-secondary">{{ $from_account->name }}</p>
                    <p class="mb-1 text-secondary">{{ $from_account->phone }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="to_phone" class="mb-1"><strong>To</strong></label>
                    <p class="mb-1 text-secondary">{{ $to_account->name }}</p>
                    <p class="mb-1 text-secondary">{{ $to_account->phone }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="amount" class="mb-1"><strong>Amount (MMK)</strong></label>
                    <p class="mb-1 text-secondary">{{ number_format($amount) }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="description" class="mb-1"><strong>Description</strong></label>
                    <p class="mb-1 text-secondary">{{ $description }}</p>
                </div>
                <button class="btn btn-theme w-100 mt-3 confirm-btn">Confirm</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {  
        $('.confirm-btn').click(function (e) {  
            e.preventDefault();
            
            Swal.fire({
                title: 'Please enter your password',
                icon: 'info',
                html: '<input type="password" name="password" class="password form-control text-center">',
                showCancelButton: true,
                focusConfirm: false,
                inputAutoFocus: false,
                confirmButtonText:
                    'Confirm',
                cancelButtonText:
                    'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let password = $('.password').val();
                    $.ajax({
                        url: "{{ route('passwordCheck') }}",
                        data: {'password': password},
                        dataType: "json",
                        type: "GET",
                        success: function (res) {
                            if(res.status == 'success') {
                                $('#form').submit();
                            }else{
                                Swal.fire(
                                    'Oops...',
                                    res.message,
                                    'success'
                                )
                            }
                        }
                    })


                    
                }
            })

        })
    })
</script>
@endsection
