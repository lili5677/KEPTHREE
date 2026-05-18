@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, {{ Auth::user()->name }}</p>
</body>
</html>

@endsection