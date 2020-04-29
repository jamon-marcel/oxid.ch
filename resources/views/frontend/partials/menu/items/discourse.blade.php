
<ul class="{{request()->routeIs('page.discourse*') ? 'is-visible' : ''}}">
  <li>
    <a href="{{ route('page.discourse') }}" class="{{ request()->routeIs('page.discourse') ? 'is-active' : '' }}">Alle</a>
  </li>
  <li>
    <a href="{{ route('page.discourse.events') }}" class="{{ request()->routeIs('page.discourse.events') ? 'is-active' : '' }}">Veranstaltungen</a>
  </li>
  <li>
    <a href="{{ route('page.discourse.publications') }}" class="{{ request()->routeIs('page.discourse.publications') ? 'is-active' : '' }}">Publikationen</a>
  </li>
  <li>
    <a href="{{ route('page.discourse.research') }}" class="{{ request()->routeIs('page.discourse.research') ? 'is-active' : '' }}">Recherche</a>
  </li>
</ul>