<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>Thiệp mời cưới —  Thành &amp; Thư | 03.04.2026</title>
    <meta name="description" content="Thiệp mời tham dự lễ vu quy và tiệc cưới Thành &amp; Thư. 03 tháng 04 năm 2026. Rất mong được đón bạn trong ngày vui của chúng mình.">
    {{-- Preload media quan trọng để giảm giật lần đầu khi scroll --}}
    <link rel="preload" as="video" href="{{ Vite::asset('resources/js/elements/202603160026.mp4') }}">
    <link rel="preload" as="image" href="{{ Vite::asset('resources/js/elements/TAH09800.jpg') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        html, body { height: 100%; overflow-x: hidden; margin: 0; }
    </style>
</head>
<body class="min-h-screen">

<div id="app"></div>

</body>
</html>