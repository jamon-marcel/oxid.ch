<div class="menu-wrapper {{ request()->routeIs('page.projects') ? 'is-visible' : '' }} js-menu">
  <header class="menu-header">
    @include('frontend.partials.icons.logo')
  </header>
  <nav class="menu-pages">
    @include('frontend.partials.menu.items.main')
  </nav>
  <nav class="menu-page">
    @if ($pageFooter)
      @include('frontend.partials.menu.items.' . $pageFooter)
    @endif
  </nav>
</div>
