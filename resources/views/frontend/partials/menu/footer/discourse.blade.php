<div class="menu-footer menu-footer--discourse">
  <h1 class="menu-footer__heading">Diskurs</h1>
  <nav class="menu-footer__nav">
    @include('frontend.partials.menu.items.discourse')
  </nav>
  <div></div>
</div>
<nav class="menu-footer__dropdown" data-dropdown="root">
  <ul>
    <li class="{{ request()->routeIs('page.discourse') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse') ? 'javascript:;' : route('page.discourse') }}" 
        class="{{ request()->routeIs('page.discourse') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.discourse')) data-dropdown="btn"@endif>
        Alle
      </a>
    </li>
    <li class="{{ request()->routeIs('page.discourse.events') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse.events') ? 'javascript:;' : route('page.discourse.events') }}" 
        class="{{ request()->routeIs('page.discourse.events') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.discourse.events')) data-dropdown="btn"@endif>
        Veranstaltungen
      </a>
    </li>
    <li class="{{ request()->routeIs('page.discourse.publications') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse.publications') ? 'javascript:;' : route('page.discourse.publications') }}" 
        class="{{ request()->routeIs('page.discourse.publications') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.discourse.publications')) data-dropdown="btn"@endif>
        Publikationen
      </a>
    </li>
    <li class="{{ request()->routeIs('page.discourse.research') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.discourse.research') ? 'javascript:;' : route('page.discourse.research') }}" 
        class="{{ request()->routeIs('page.discourse.research') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.discourse.research')) data-dropdown="btn"@endif>
        Recherche
      </a>
    </li>
  </ul>
</nav>