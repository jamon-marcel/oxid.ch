@include('frontend.partials.head')
@include('frontend.partials.menu.pages')
<main role="main">
  @yield('content')
</main>
@if (!request()->routeIs('page.discourse.detail'))
  @include('frontend.partials.menu.bar.pages')
@endif
@include('frontend.partials.footer')