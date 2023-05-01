@extends('frontend.layouts.app')
@section('title', 'Scan & Pay Transfer')
@section('content')
<div class="transfer">
    <div class="card">
        <div class="card-body">
            @include('backend.layouts.flash')
            <form action="{{ url('scan-and-pay-confirm') }}" method="GET" id="transfer-form" >
                <div class="form-group mb-3">
                    <label for="" class="mb-1"><strong>From</strong></label>
                    <p class="mb-1 text-secondary">{{ $from_account->name }}</p>
                    <p class="mb-1 text-secondary">{{ $from_account->phone }}</p>
                </div>
                <input type="hidden" name="hash_value" id="hash_value"> 
                <input type="hidden" name="to_phone" id="to_phone" value="{{ $to_account->phone }}">
                <div class="form-group mb-3">
                    <label for="" class="mb-1"><strong>From</strong></label>
                    <p class="mb-1 text-secondary">{{ $to_account->name }}</p>
                    <p class="mb-1 text-secondary">{{ $to_account->phone }}</p>
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
