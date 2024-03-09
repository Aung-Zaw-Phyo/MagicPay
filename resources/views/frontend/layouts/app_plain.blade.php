<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
    @yield('extra_css')
</head>
<body>
    @yield('content')

    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>

    {{-- bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script>
  
        $(document).ready(function () {
            let token = document.querySelector('meta[name=csrf-token]')
            if(token) {
                $.ajaxSetup({
                    headers: { 
                        'X-CSRF-TOKEN': token.content
                    }
                });
            }else {
                console.error('Token not found!');
            }
            

            var firebaseConfig = {
                apiKey: "AIzaSyCzAPl3yxJkIwLh6zByZDh--TaaKQOm9ew", 
                authDomain: "laravelfcm-b29ba.firebaseapp.com",
                databaseURL: "https://XXXX.firebaseio.com",
                projectId: "laravelfcm-b29ba",
                storageBucket: "laravelfcm-b29ba.appspot.com",
                messagingSenderId: "4333973945",
                appId: "1:4333973945:web:775e456ac4358cb8554668",                                                                                                      
                measurementId: "G-44YTE2STYX"
            };
            
            firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();
        
            function initFirebaseMessagingRegistration() {
                    messaging
                    .requestPermission()
                    .then(function () {
                        return messaging.getToken()
                    })
                    .then(function(token) {
                        document.getElementById("device_token_input").setAttribute("value", token)
                    }).catch(function (err) {
                        console.log('User Chat Token Error'+ err);
                    });
            }  

            initFirebaseMessagingRegistration()

            $('#register').on('submit', function (e) {
                // initFirebaseMessagingRegistration()
            })
            
            messaging.onMessage(function(payload) {
                const noteTitle = payload.notification.title;
                const noteOptions = {
                    body: payload.notification.body,
                    icon: payload.notification.icon,
                };
                new Notification(noteTitle, noteOptions);
            });
        })
       
    </script>

    @yield('scripts')

</body>
</html>
