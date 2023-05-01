<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    {{-- fontawesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Date Picker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- custom css  --}}
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
    @yield('extra_css')
</head>
<body>
        <div class="header-menu">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-2 text-center">
                            @if (!request()->is('/'))
                            <a href="#" class="back-btn">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                            @endif
                        </div>
                        <div class="col-8 text-center">
                               <h3>@yield('title')</h3>
                        </div>
                        <div class="col-2 text-center">
                            <a href="{{ url('notification') }}" class="position-relative">
                                <i class="fa-solid fa-bell"></i><span style="right: -25px" class="position-absolute top-0 translate-middle badge rounded-pill bg-danger ">{{ $unread_noti_count }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content row">
            <div class="col-md-8 mx-auto">
                @yield('content')
            </div>
        </div>

        <div class="bottom-menu bg-light" style="z-index: 999">
            <div class="scan-tab">
                <a href="{{ url('scan-and-pay') }}" class="inside">
                    <i class="fa-solid fa-qrcode"></i>
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-3 text-center">
                            <a href="{{ route('home') }}">
                                <i class="fa-solid fa-house"></i>
                                <p>Home</p>
                            </a>
                        </div>
                        <div class="col-3 text-center">
                            <a href="{{ route('wallet') }}">
                                <i class="fa-solid fa-wallet"></i>
                                <p>Wallet</p>
                            </a>
                        </div>
                        <div class="col-3 text-center">
                            <a href="{{url('transaction')}}">
                                <i class="fa-solid fa-exchange-alt"></i>
                                <p>Transaction</p>
                            </a>
                        </div>
                        <div class="col-3 text-center">
                            <a href="{{ route('profile') }}">
                                <i class="fa-regular fa-user"></i>
                                <p>Account</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- jquery --}}
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

        <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ url('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

        {{-- sweetalert --}}
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- bootstrap --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        
        {{-- Jquery infinite scroll --}} 
        <script src="{{ asset('frontend/js/jscroll.min.js') }}"></script>

        {{-- Date Picker --}}
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        @yield('scripts')

        <script>
            $(document).ready(function () {
                let token = document.head.querySelector('meta[name="csrf-token"]')
                if(token) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF_TOKEN': token.content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    })
                }

                const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
            })

            @if (session('create'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('create') }}"
            })
            @endif

            @if (session('update'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('update') }}"
            })
            @endif

            @if (session('error'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('error') }}"
            })
            @endif

            // @if (session('transfer_success'))
            // Toast.fire({
            //     icon: 'success',
            //     title: "{{ session('transfer_success') }}"
            // })
            // @endif

            $('.back-btn').on('click', function (e) {
                e.preventDefault();
                window.history.go(-1)
            })
            })
        </script>
</body>
</html>
