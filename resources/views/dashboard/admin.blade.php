<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow-md mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Admin</h2>
            <p class="text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <a href="/admin/users" 
               class="bg-blue-600 text-white p-6 rounded-xl shadow hover:bg-blue-700 transition text-center">
                <h3 class="text-lg font-semibold">Manajemen User</h3>
                <p class="text-sm mt-1">Kelola akun ketua, reviewer, sekretariat</p>
            </a>
        </div>

        <form action="/logout" method="POST" class="mt-6">
            @csrf
            <button class="text-red-500 hover:underline text-sm">Logout</button>
        </form>
    </div>

</body>
</html>