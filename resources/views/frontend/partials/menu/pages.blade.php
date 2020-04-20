<div class="menu-wrapper {{ request()->routeIs('page.team')  ? 'is-visible' : '' }} js-menu">
  <header class="menu-header">
    @include('frontend.partials.icons.logo')
  </header>
  <nav class="menu-site">
    @include('frontend.partials.menu.items.main')
  </nav>
</div>
