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
    <link rel="preload" as="image" href="{{ Vite::asset('resources/js/elements/section_2.webp') }}">
    <link rel="preload" as="image" href="{{ Vite::asset('resources/js/elements/section_8.webp') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        html, body { height: 100%; overflow-x: hidden; margin: 0; }
        /* Skeleton overlay cực nhẹ để hiển thị ngay cả khi JS chưa load */
        #initial-preload-overlay {
            position: fixed;
            inset: 0;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #4a3f35;
            color: #fff;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        #initial-preload-overlay-inner {
            text-align: center;
            padding: 0 1.5rem;
        }
        #initial-preload-overlay-bar {
            width: 260px;
            height: 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.35);
            overflow: hidden;
            margin: 0.75rem auto 0;
        }
        #initial-preload-overlay-bar-inner {
            width: 30%;
            height: 100%;
            border-radius: inherit;
            background: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body class="min-h-screen">

<div id="initial-preload-overlay">
    <div id="initial-preload-overlay-inner">
        <div>Đang chuẩn bị thiệp cưới cho bạn...</div>
        <div id="initial-preload-overlay-bar">
            <div id="initial-preload-overlay-bar-inner"></div>
        </div>
    </div>
</div>

<div id="app"></div>

</body>
</html>