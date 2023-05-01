@extends('frontend.layouts.app')
@section('title', 'Profile')
@section('content')
    <div class="account">
        <div class="profile text-center mb-3">
            <img src="https://ui-avatars.com/api/?background=5842e3&color=fff&name={{ $user->name }}" alt="">
        </div>

        <div class="card mb-3">
            <div class="card-body pe-0">
                <div class="d-flex justify-content-between">
                    <span>Username</span>
                    <span class="me-3">{{ $user->name }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Email</span>
                    <span class="me-3">{{ $user->email }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Phone</span>
                    <span class="me-3">{{ $user->phone }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body pe-0">
                <a href="{{ route('update-password') }}" class="d-flex justify-content-between">
                    <span>Update password</span>
                    <i class="fas fa-angle-right me-3"></i>
                </a>
                <hr>
                <a href="#" class="d-flex justify-content-between" id="logout">
                    <span>Logout</span>
                    <i class="fas fa-angle-right me-3"></i>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#logout', function (e){
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure, you want to logout?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                denyButtonText: `Don't save`,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('logout') }}",
                        type: "POST",
                        success: function () {
                            window.location.reload()
                        }
                    })
                }
            })
            
        })
    </script>
@endsection