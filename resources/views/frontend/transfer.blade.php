@extends('frontend.layouts.app')
@section('title', 'Transfer')
@section('content')
<div class="transfer">
    <div class="card">
        <div class="card-body">
            @include('backend.layouts.flash')
            <form action="{{ route('transfer.confirm') }}" method="GET" id="transfer-form" >
                <div class="form-group mb-3">
                    <label for="" class="mb-1"><strong>From</strong></label>
                    <p class="mb-1 text-secondary">{{ $user->name }}</p>
                    <p class="mb-1 text-secondary">{{ $user->phone }}</p>
                </div>
                <input type="hidden" value="" name="hash_value" id="hash_value"> 
                <div class="form-group mb-3">
                    <label for="to_phone" class="mb-1"><strong>To </strong> <span class="phone-user-name text-danger"></span> </label>
                    <div class="input-group">
                        <input  type="number" class="form-control check-phone @error('to_phone') is-invalid @enderror" name="to_phone" id="to_phone" value="{{ old('to_phone') }}" aria-describedby="basic-addon2">
                        <span class="input-group-text btn btn-secondary check-btn" id="basic-addon2"><i class="fas fa-check-circle"></i></span>
                    </div>
                    @error('to_phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="amount" class="mb-1"><strong>Amount (MMK)</strong></label>
                    <input type="number" class="form-control  @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" id="amount">
                    @error('amount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="description" class="mb-1"><strong>Description</strong></label>
                    <textarea class="form-control" name="description"  id="description">{{ old('description') }}</textarea>
                </div>
                <button class="btn btn-theme w-100 mt-3 submit-btn">Continue</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- {!! JsValidator::formRequest('App\Http\Requests\TransferFormValidatioin', '#transfer') !!} --}}
    <script>
        $(document).ready(function () {
            $('.check-btn').on('click', function () {  
                let phone = $('.check-phone').val()
                if(phone.length == 0) {
                    $('.check-phone').focus()
                    return true
                }
                $.ajax({
                    url: "{{ route('toAccountVerify') }}",
                    data: {'phone': phone},
                    dataType: "json",
                    type: "GET",
                    success: function (res) {
                        if(res.status == 'success') {
                            console.log(res)
                            $('.phone-user-name').text(' ( '+ res.data.name +' )' )
                        }else{
                            console.log('fail')
                            $('.phone-user-name').text(' ( '+ res.message +' )' )
                        }
                    }
                })
            })

            $('.submit-btn').on('click', function (e) {
                e.preventDefault();

                let to_phone = $('#to_phone').val();
                let amount = $('#amount').val();
                let description = $('#description').val();

                $.ajax({
                    url: "{{ url('transfer-data-encryption') }}",
                    data: {'to_phone': to_phone, 'amount': amount, 'description': description},
                    dataType: "json",
                    type: "GET",
                    success: function (res) {
                        if(res.status == 'success') {
                            $('#hash_value').val(res.data)
                            $('#transfer-form').submit();
                        }
                    }
                })
            })
        })
    </script>
@endsection
