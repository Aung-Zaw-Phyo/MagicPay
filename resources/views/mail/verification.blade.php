<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Your account has been created successfully</h2>
    <h4>Please verify that, <a href="{{ route('account.verification', $key) }}">verify your account</a></h4>
</body>
</html>