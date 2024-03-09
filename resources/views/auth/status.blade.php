@extends('frontend.layouts.app_plain')
@section('title', 'Account Status')
@section('extra_css')
    <style>
        #resend-btn {
            color: #5842e3;
            padding: 4px;
            outline: none;
            background: none;
            border: none;
            border-bottom: 1px solid #5842e3;
        }

        .disabled {
            color: #333 !important;
            border-bottom: 1px solid #333 !important;
        }
    </style>

@endsection
@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh">
        <div class="col-md-6">
            <div class="px-2">
                <h3 class="text-center mb-4">Your account created successfully!</h3>
                <h5 class="text-center mb-4">
                    We send to your email <small>({{ $email }})</small> with account verification link, please verify that!
                </h5>
                <div class="text-center">
                    <button id="resend-btn">Resend Verify Link</button>
                </div>
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                        <strong class="me-auto">Message</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body" id="warning-message">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        
        $(document).ready(function () {
            const toastLiveExample = document.getElementById('liveToast')
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)

            $('#resend-btn').on('click', function () {  
                $('#resend-btn').attr("disabled", true).addClass('disabled');
                let phone = "{{ $phone }}"
                $.ajax({
                    url: "{{ route('resendEmail') }}",
                    data: {'phone': phone},
                    dataType: "json",
                    type: "GET",
                    success: function (res) {
                        if(res.status) {
                            $('#warning-message').text(res.message)
                        }
                        toastBootstrap.show()
                        $('#resend-btn').attr("disabled", false).removeClass('disabled');
                    }
                })
            })
        })
    </script>
@endsection
