<div class="menu-footer menu-footer--office">
  <h2 class="menu-footer__heading">Büro</h2>
  <nav class="menu-footer__nav">
    @include('frontend.partials.menu.items.office')
  </nav>
  <div class="menu-footer__info">
    <a href="javascript:;" class="anchor-ul is-active" data-overlay="btn">Info</a>
  </div>
</div>
<!--
<nav class="menu-footer__dropdown" data-dropdown="root">
  <ul>
    <li class="{{ request()->routeIs('page.office.team') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.office.team') ? 'javascript:;' : route('page.office.team') }}" 
        class="{{ request()->routeIs('page.office.team') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.office.team')) data-dropdown="btn"@endif>
        Team
      </a>
    </li>
    <li class="{{ request()->routeIs('page.office.profile') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.office.profile') ? 'javascript:;' : route('page.office.profile') }}" 
        class="{{ request()->routeIs('page.office.profile') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.office.profile')) data-dropdown="btn"@endif>
        Profil
      </a>
    </li>
    <li class="{{ request()->routeIs('page.office.jobs') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.office.jobs') ? 'javascript:;' : route('page.office.jobs') }}" 
        class="{{ request()->routeIs('page.office.jobs') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.office.jobs')) data-dropdown="btn"@endif>
        Jobs
      </a>
    </li>
  </ul>
</nav>
-->