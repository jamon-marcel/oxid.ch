@php
  // Pages set seo_title, seo_description, and optionally og_image (absolute,
  // 1200 x 630), og_type, canonical and robots. Inline @section values
  // arrive escaped, so each is decoded once before it is echoed again.
  $seoYield = fn ($section) => html_entity_decode(trim($__env->yieldContent($section)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $seoSite = config('seo.title');
  $seoTitle = $seoYield('seo_title') ? $seoYield('seo_title') . ' - ' . $seoSite : $seoSite;
  $seoDescription = \App\Support\Seo::text($seoYield('seo_description') ?: config('seo.description'));
  $seoCanonical = $seoYield('canonical') ?: url()->current();
  $seoImage = $seoYield('og_image') ?: asset(config('seo.image'));
  $seoRobots = \App\Support\Seo::indexable() ? ($seoYield('robots') ?: 'index, follow') : 'noindex, nofollow';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:site_name" content="{{ $seoSite }}">
<meta property="og:locale" content="de_CH">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
@stack('head')
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="msapplication-TileColor" content="#aa4939">
<meta name="theme-color" content="#aa4939">
<meta name="csrf-token" value="{{ csrf_token() }}" />
<meta name="format-detection" content="telephone=no">
@vite('resources/sass/frontend/app.scss')
<script src="{{ asset('assets/js/modernizr.min.js') }}"></script>
</head>
<body>
