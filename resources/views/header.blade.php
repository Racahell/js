<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ session('role') ? 'Dashboard • Alat Musik' : 'Login & Register' }}</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    
</head>
<body class="{{ session('role') ? '' : 'auth-page' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">