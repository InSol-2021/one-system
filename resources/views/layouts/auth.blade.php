<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') - CAS Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {!! RecaptchaV3::initJs() !!}
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
@yield('content')
</body>
</html>
