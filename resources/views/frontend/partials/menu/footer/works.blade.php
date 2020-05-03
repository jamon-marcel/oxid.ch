<div class="menu-footer menu-footer--works">
  <h1 class="menu-footer__heading">Werkliste</h1>
  <nav class="menu-footer__nav">
    @include('frontend.partials.menu.items.works')
    <ul class="works-filter">
      <li><a href="javascript:;" class="js-filter-btn" data-filter="wood">Holz</a></li>
      <li><a href="javascript:;" class="js-filter-btn" data-filter="reuse">Umnutzung</a></li>
      <li><a href="javascript:;" class="js-filter-btn" data-filter="area">Areal</a></li>
    </ul>
  </nav>
  <div class="menu-footer__info">
    <a href="" class="icon-document" target="_blank" title="">Pdf</a>
  </div>
</div>
<nav class="menu-footer__dropdown js-dropdown">
  <ul>
    <li class="{{ request()->routeIs('page.works.authors') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.authors') ? 'javascript:;' : route('page.works.authors') }}" 
        class="{{ request()->routeIs('page.works.authors') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Autorenschaft
      </a>
    </li>
    <li class="{{ request()->routeIs('page.works.year') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.year') ? 'javascript:;' : route('page.works.year') }}" 
        class="{{ request()->routeIs('page.works.year') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Jahr
      </a>
    </li>
    <li class="{{ request()->routeIs('page.works.program') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.program') ? 'javascript:;' : route('page.works.program') }}" 
        class="{{ request()->routeIs('page.works.program') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Programm
      </a>
    </li>
    <li class="{{ request()->routeIs('page.works.state') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.state') ? 'javascript:;' : route('page.works.state') }}" 
        class="{{ request()->routeIs('page.works.state') ? 'btn-dropdown js-btn-dropdown' : '' }}">
        Status
      </a>
    </li>
  </ul>
</nav>