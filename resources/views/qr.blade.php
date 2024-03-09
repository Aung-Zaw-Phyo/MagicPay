<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>QR CODE</h1>
    <div id="qrcode"></div>
    <button id="downloadBtn">Download QR Code</button>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        $(document).ready(function () {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: "09968548024",
                width: 128,
                height: 128,
            });
        });

        function downloadQRCode() {
            // Get the data URL of the QR code
            var dataUrl = document.getElementById("qrcode").getElementsByTagName("img")[0].src;

            // Create a temporary anchor element
            var downloadLink = document.createElement("a");
            downloadLink.href = dataUrl;
            downloadLink.download = "qrcode.png";

            // Trigger a click on the anchor to start the download
            downloadLink.click();
        }

        // Attach the download action to the button click
        document.getElementById("downloadBtn").addEventListener("click", downloadQRCode);
    </script>
</body>
</html>