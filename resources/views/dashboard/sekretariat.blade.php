<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Sekertariat</title>
</head>
<body>
    <h2>Dashboard Sekertariat</h2>
    <p>Selamat datang, {{ Auth::user()->name }}</p>
    <form action="/logout" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>