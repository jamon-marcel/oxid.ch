<ul class="{{request()->routeIs('page.office*') ? 'is-visible' : ''}}">
  <li>
    <a href="{{ route('page.office.team') }}" class="{{ request()->routeIs('page.office.team') ? 'is-active' : '' }}">Team</a>
  </li>
  <li>
    <a href="{{ route('page.office.profile') }}" class="{{ request()->routeIs('page.office.profile') ? 'is-active' : '' }}">Profil</a>
  </li>
  <li>
    <a href="{{ route('page.office.jobs') }}" class="{{ request()->routeIs('page.office.jobs') ? 'is-active' : '' }}">Jobs</a>
  </li>
</ul>