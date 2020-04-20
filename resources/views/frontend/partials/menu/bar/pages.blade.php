<div class="menu-bar is-pages">
  <div class="menu-bar__open {{ request()->routeIs('page.team')  ? 'is-hidden' : '' }} js-menu-bar">
    @include('frontend.partials.icons.menu_open')
  </div>
  <div class="menu-bar__close {{ request()->routeIs('page.team')  ? '' : 'is-hidden' }} js-menu-bar">
    @include('frontend.partials.icons.menu_close')
    @include('frontend.partials.icons.logo_byline')
  </div>
</div>