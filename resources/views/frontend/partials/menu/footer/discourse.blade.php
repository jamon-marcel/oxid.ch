<div class="menu-footer menu-footer--discourse">
  <h1 class="menu-footer__heading">Diskurs</h1>
  <nav class="menu-footer__nav">
    @include('frontend.partials.menu.items.discourse')
  </nav>
  <div></div>
</div>
<nav class="menu-footer__dropdown js-dropdown">
  <ul>
    <li class="{{ request()->routeIs('page.discourse') ? 'is-visible' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse') ? 'javascript:;' : route('page.discourse') }}" 
        class="{{ request()->routeIs('page.discourse') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Alle
      </a>
    </li>
    <li class="{{ request()->routeIs('page.discourse.events') ? 'is-visible' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse.events') ? 'javascript:;' : route('page.discourse.events') }}" 
        class="{{ request()->routeIs('page.discourse.events') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Veranstaltungen
      </a>
    </li>
    <li class="{{ request()->routeIs('page.discourse.publications') ? 'is-visible' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse.publications') ? 'javascript:;' : route('page.discourse.publications') }}" 
        class="{{ request()->routeIs('page.discourse.publications') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Publikationen
      </a>
    </li>
    <li class="{{ request()->routeIs('page.discourse.research') ? 'is-visible' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse.research') ? 'javascript:;' : route('page.discourse.research') }}" 
        class="{{ request()->routeIs('page.discourse.research') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Recherche
      </a>
    </li>
  </ul>
</nav>