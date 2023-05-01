@extends('backend.layouts.app')

@section('title', 'Add Amount')
@section('wallet-active', 'mm-active')

@section('content')

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="metismenu-icon pe-7s-users"></i>
            </div>
            <div>
                Add Amount
            </div>
        </div>
    </div>
</div> 

<div class="content pt-3">
    <div class="card">
        <div class="card-body">
            @include('backend.layouts.flash')
            <form action="{{ url('admin/wallet/add/amount') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" class="form-control user-id" id="user-id">
                        <option></option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : null }} >{{ $user->name }} ({{ $user->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" class="form-control" name="amount" value="{{ old('amount') }}" id="amount">
                </div>
                <div class="form-group mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="5" id="description">{{ old('description') }}</textarea>
                </div>
                <div>
                    <button class="btn btn-secondary me-3 content-fm back-btn">Cancel</button>
                    <button class="btn btn-primary content-fm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('.user-id').select2({
            placeholder: "--- Please Choose ---",
            allowClear: true,
            theme: 'bootstrap4',
        });
    });
</script>
@endsection