<div class="menu-bar is-pages">
  <div class="menu-bar__open {{ request()->routeIs('page.projects')  ? 'is-hidden' : '' }} js-menu-bar">
    @include('frontend.partials.icons.menu_open')
    @if ($pageFooter)
      @include('frontend.partials.menu.footer.' . $pageFooter)
    @endif
  </div>
  <div class="menu-bar__close {{ request()->routeIs('page.projects')  ? '' : 'is-hidden' }} js-menu-bar">
    @include('frontend.partials.icons.menu_close')
    @include('frontend.partials.icons.logo_byline')
  </div>
</div>