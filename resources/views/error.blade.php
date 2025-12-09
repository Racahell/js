<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger">404</h1>
        <p class="fs-4">Oops! Halaman yang kamu cari tidak ditemukan.</p>
        <a href="<?= url('/login') ?>" class="btn btn-danger mt-3">Kembali ke Halaman Login</a>
    </div>
</body>
</html>