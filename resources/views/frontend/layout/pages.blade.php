@include('frontend.partials.head')
@include('frontend.partials.menu.pages')
<main role="main">
  @yield('content')
</main>
@include('frontend.partials.menu.bar.pages')
@include('frontend.partials.footer')