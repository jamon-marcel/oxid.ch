<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Oxid - Administration</title>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="robots" content="noindex, nofollow">
@vite(['resources/css/backend.css', 'resources/js/backend/app.js'])
@if ($splash ?? null)
@php
  // Login background: plain url() first, for browsers without image-set() type()
  $background = function (int $size) use ($splash) {
    $set = collect(\App\Support\ImageSupport::modernFormats())
      ->map(fn ($format) => "url('{$splash->url($size, $format)}') type('image/{$format}')")
      ->push("url('{$splash->url($size)}')")
      ->implode(', ');

    return "background-image: url('{$splash->url($size)}'); background-image: image-set({$set});";
  };
@endphp
<style>
.container-auth { {!! $background(2000) !!} }
@media (max-width: 1400px) { .container-auth { {!! $background(1200) !!} } }
</style>
@endif
</head>
<body class="h-full bg-gray-100 font-sans text-sm tracking-wide text-gray-900 antialiased dark:bg-warm-950 dark:text-warm-100">
<div id="app"></div>
</body>
</html>
