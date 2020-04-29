
<ul class="{{request()->routeIs('page.works*') ? 'is-visible' : ''}}">
  <li>
    <a href="{{ route('page.works.authors') }}" class="{{ request()->routeIs('page.works.authors') ? 'is-active' : '' }}">Autorenschaft</a>
  </li>
  <li>
    <a href="{{ route('page.works.year') }}" class="{{ request()->routeIs('page.works.year') ? 'is-active' : '' }}">Jahr</a>
  </li>
  <li>
    <a href="{{ route('page.works.program') }}" class="{{ request()->routeIs('page.works.program') ? 'is-active' : '' }}">Programm</a>
  </li>
  <li>
    <a href="{{ route('page.works.state') }}" class="{{ request()->routeIs('page.works.state') ? 'is-active' : '' }}">Status</a>
  </li>
</ul>