@extends('frontend.layouts.app')
@section('title', 'Transaction')
@section('content')
<div class="transaction">

    <div class="card my-card mb-3">
        <div class="card-body">
            <h6><i class="fas fa-filter"></i> Filter</h6>
            <div class="row">
                <div class="col-6">
                    <div class="input-group">
                        <label class="input-group-text p-1" for="inputGroupSelect01">Type</label>
                        <input value="{{ request()->date }}" type="text" class="form-control" id="date" placeholder="All" autocomplete="off">
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group">
                        <label class="input-group-text p-1">Type</label>
                        <select class="form-select" id="type">
                          <option value="">All</option>
                          <option value="1" {{ request()->type == 1 ? 'selected' : null }}>Income</option>
                          <option value="2" {{ request()->type == 2 ? 'selected' : null }}>Expense</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h6 class="fw-bold">Transactions</h6>
    <div class="infinite-scroll">
        @foreach ($transactions as $transaction)
            <a href="{{ url('/transaction', $transaction->trx_id) }}">
                <div class="card my-card mb-2">
                    <div class="card-body py-2">
                        <div class=" d-flex justify-content-between">
                            <h6 class="mb-1">Trx Id: {{ $transaction->trx_id }}</h6>
                            <p class="mb-1 @if($transaction->type == 1) text-success @elseif($transaction->type == 2) text-danger @endif">{{ $transaction->amount }} <small>MMK</small></p>
                        </div>
                        <p class="text-secondary mb-1">
                            @if ($transaction->type == 1)
                            From 
                            @elseif ($transaction->type == 2)
                            To
                            @endif
                            {{ $transaction->source ? $transaction->source->name: '' }}
                        </p>
                        <p class="text-secondary mb-1">
                            {{ $transaction->created_at }}
                        </p>
                    </div>
                </div>
            </a>
        @endforeach

        <div class="mt-3">
        {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            // loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            loadingHtml: '<div class="text-center">Loading.....</div>',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').remove();
            }
        });

        // Data Picker 

        $('#date').daterangepicker({
            "singleDatePicker": true,
            "autoApply": false,
            "autoUpdateInput": false,
            "locale": {
                "format": "YYYY-MM-DD",
            }
        });

        $('#date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'))
            let type = $('#type').val()
            let date = $('#date').val()
            history.pushState(null, '', `?date=${date}&type=${type}`)
            location.reload();
        });

        $('#date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('')
            let type = $('#type').val()
            let date = $('#date').val()
            history.pushState(null, '', `?date=${date}&type=${type}`)
            location.reload();
        });

        $('#type').change(function () {
            let type = $('#type').val()
            let date = $('#date').val()
            history.pushState(null, '', `?date=${date}&type=${type}`)
            location.reload();
        })

    });
</script>
@endsection