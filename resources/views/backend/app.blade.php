<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Oxid - Administration</title>
<meta name="csrf-token" content="{{ csrf_token() }}" />
@vite(['resources/sass/backend/app.scss', 'resources/js/backend/app.js'])
</head>
<body>
<div id="app"></div>
</body>
</html>
