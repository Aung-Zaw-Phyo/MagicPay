@extends('frontend.layouts.app')
@section('title', 'Scan QR')
@section('content')
<div class="scan-qr">
    <div class="card my-card">
        <div class="card-body text-center">
            @include('backend.layouts.flash')
            <p class="text-center mb-1">
                <img width="220px" src="{{ asset('img/qr-scan-with-code.png') }}" alt="">
            </p>
            <p class="mb-3">
                Click button, put QR code in the frame and pay
            </p>
            <button class="btn btn-theme btn-sm"  data-bs-toggle="modal" data-bs-target="#scanModel">Scan</button>

            <!-- Modal -->
            <div class="modal fade" id="scanModel" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="scanModalLabel">Scan QR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <video id="scanner" width="100%" height="220px"></video>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('frontend/js/qr-scanner.umd.min.js')}}"></script>
<script>

    let videoElem = document.getElementById('scanner')
    let myModalEl = document.getElementById('scanModel')

    const qrScanner = new QrScanner(
        videoElem,
        result => {
            if(result){
                console.log(result)
                qrScanner.stop();
                $('#scanModel').modal('hide')
                window.location.replace(`scan-and-pay-form?to_phone=${result}`)
            }
        },
    );

    myModalEl.addEventListener('show.bs.modal', function (event) {
        qrScanner.start();
    })
    myModalEl.addEventListener('hidden.bs.modal', function (event) {
        qrScanner.stop();
    })

</script>
@endsection