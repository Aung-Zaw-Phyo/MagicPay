@extends('frontend.layouts.app')
@section('title', 'Receive QR')
@section('extra_css')
    <style>
        #downloadBtn{
            font-size: 12px;
            font-weight: bold;
            background: #EDEDF5;
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
<div class="receive-qr">
    <div class="card my-card">
        <div class="card-body">
            <p class="text-center mb-0 h5">QR Scan to pay me</p>
            <div class="text-center my-4 d-flex flex-column align-items-center justify-content-center">
                {{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($authUser->phone)) !!} "> --}}
                <div id="qrcode" class="mb-3"></div>
                <span id="downloadBtn" class="border p-2">SAVE IMAGE</span>
            </div>
            <p class="text-center mb-1 fw-bold h5">{{ $authUser->name }}</p>
            <p class="text-center mb-0">{{ $authUser->phone }}</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
         $(document).ready(function () {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: "{{ $authUser->phone }}",
                width: 200,
                height: 200,
            });

            function downloadQRCode() {
                var dataUrl = document.getElementById("qrcode").getElementsByTagName("img")[0].src;
                var downloadLink = document.createElement("a");
                downloadLink.href = dataUrl;
                downloadLink.download = "qrcode.png";
                downloadLink.click();
            }
            document.getElementById("downloadBtn").addEventListener("click", downloadQRCode);
        });
    </script>
@endsection
