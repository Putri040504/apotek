<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir POS - Apotek Zema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/kasir-pos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/barcode-scanner.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="pos-body">
    @yield('content')
    @include('components.barcode-scanner-modal')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('js/barcode-scanner.js') }}"></script>
    <script src="{{ asset('js/kasir-pos.js') }}"></script>
    @stack('scripts')
</body>
</html>
