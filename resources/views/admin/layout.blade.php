<!doctype html>
<html lang="en">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

<div class="flex h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r p-5">
        <div class="font-bold text-lg mb-6">🦌 MooseJobs</div>

        <nav class="space-y-2 text-sm">
            <a href="/admin/dashboard" class="block p-2 hover:bg-gray-100 rounded">Dashboard</a>
            <a href="/admin/announcements" class="block p-2 hover:bg-gray-100 rounded">Announcements</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-6">
        <h1 class="text-xl font-bold mb-4">@yield('title')</h1>
        @yield('content')
    </main>

</div>

</body>
</html>