@include('frontend.partials.head')
@include('frontend.partials.header')
@include('frontend.partials.menu.home')
<main role="main">
  @yield('content')
</main>
@include('frontend.partials.menu.bar.home')
@include('frontend.partials.footer')