<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CV Analyzer AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #070711; color: #e5e7eb; }
        .blob { position: fixed; border-radius: 9999px; filter: blur(110px); opacity: .35; z-index: 0; pointer-events: none; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.09); backdrop-filter: blur(14px); }
        .grad-text { background: linear-gradient(90deg,#a78bfa,#60a5fa,#34d399); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .grad-btn { background: linear-gradient(90deg,#7c3aed,#2563eb); transition: .25s; }
        .grad-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 35px rgba(99,102,241,.45); }
        .fade-up { opacity: 0; transform: translateY(18px); animation: fadeUp .7s ease forwards; }
        @keyframes fadeUp { to { opacity: 1; transform: none; } }
        @media print {
            .no-print, .blob { display: none !important; }
            body { background: #fff; color: #000; }
            .glass { background: #fff; border-color: #ddd; }
        }
    </style>
</head>
<body class="min-h-screen relative overflow-x-hidden">
    <div class="blob w-96 h-96 bg-violet-600 -top-24 -left-24"></div>
    <div class="blob w-96 h-96 bg-blue-600 top-1/3 -right-32"></div>
    <div class="blob w-80 h-80 bg-emerald-500 bottom-0 left-1/4"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 py-10">
        <a href="{{ route('cv.create') }}" class="no-print flex items-center justify-center gap-2 mb-10">
            <span class="w-9 h-9 rounded-xl grad-btn flex items-center justify-center text-white font-bold">AI</span>
            <span class="text-xl font-extrabold tracking-tight">CV<span class="grad-text">Analyzer</span></span>
        </a>

        @yield('content')

        <p class="no-print text-center text-xs text-gray-500 mt-12">Built with Laravel + AI</p>
    </div>
</body>
</html>