@extends('frontend.layouts.app')
@section('title', 'Update Password')
@section('content')
    <div class="update-password">

        <div class="card mb-3">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ asset('img/update_password.png') }}" alt="">
                </div>

                <form action="{{ route('update-password.store') }}" method="POST" id="update">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="old-password" class="form-label">Old Password</label>
                        <input type="password" class="form-control  @error('old_password') is-invalid @enderror" name="old_password" id="old-password">
                        @error('old_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
    
                    <div class="form-group mb-3">
                        <label for="new-password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new-password">
                    </div>
    
                    <button type="submit" class="btn btn-theme w-100 mt-3">Update Password</button>
                </form>

            </div>
        </div>

    </div>
@endsection

@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\UpdatePassword', '#update') !!}
    <script>
        
    </script>
@endsection