<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        html, body { height: 100%; overflow-x: hidden; margin: 0; }
    </style>
</head>
<body class="min-h-screen">

<div id="app"></div>

</body>
</html>