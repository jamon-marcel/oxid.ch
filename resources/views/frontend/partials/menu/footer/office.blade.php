<div class="menu-footer menu-footer--office">
  <h2 class="menu-footer__heading">Büro</h2>
  <nav class="menu-footer__nav">
    @include('frontend.partials.menu.items.office')
  </nav>
  <div class="menu-footer__info">
    <a href="javascript:;" class="anchor-ul is-active js-btn-info">Info</a>
  </div>
</div>
<nav class="menu-footer__dropdown js-dropdown">
  <ul>
    <li class="{{ request()->routeIs('page.office.team') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.office.team') ? 'javascript:;' : route('page.office.team') }}" 
        class="{{ request()->routeIs('page.office.team') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Team
      </a>
    </li>
    <li class="{{ request()->routeIs('page.office.profile') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.office.profile') ? 'javascript:;' : route('page.office.profile') }}" 
        class="{{ request()->routeIs('page.office.profile') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Profil
      </a>
    </li>
    <li class="{{ request()->routeIs('page.office.jobs') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.office.jobs') ? 'javascript:;' : route('page.office.jobs') }}" 
        class="{{ request()->routeIs('page.office.jobs') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Jobs
      </a>
    </li>
  </ul>
</nav>