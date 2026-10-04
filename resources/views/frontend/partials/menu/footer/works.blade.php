<div class="menu-footer menu-footer--works">
  <h1 class="menu-footer__heading">Werkliste</h1>
  <nav class="menu-footer__nav">
    @include('frontend.partials.menu.items.works')
    <ul class="works-filter">
      <li><a href="javascript:;" data-filter="btn" data-filter-value="wood">Holz</a></li>
      <li><a href="javascript:;" data-filter="btn" data-filter-value="reuse">Umnutzung</a></li>
      <li><a href="javascript:;" data-filter="btn" data-filter-value="area">Areal</a></li>
    </ul>
  </nav>
  <div class="menu-footer__info">&nbsp;</div>
</div>
<nav class="menu-footer__dropdown" data-dropdown="root">
  <ul>
    <li class="{{ request()->routeIs('page.works.authors') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.authors') ? 'javascript:;' : route('page.works.authors') }}" 
        class="{{ request()->routeIs('page.works.authors') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.works.authors')) data-dropdown="btn"@endif>
        Autorenschaft
      </a>
    </li>
    <li class="{{ request()->routeIs('page.works.year') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.year') ? 'javascript:;' : route('page.works.year') }}" 
        class="{{ request()->routeIs('page.works.year') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.works.year')) data-dropdown="btn"@endif>
        Jahr
      </a>
    </li>
    <li class="{{ request()->routeIs('page.works.program') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.program') ? 'javascript:;' : route('page.works.program') }}" 
        class="{{ request()->routeIs('page.works.program') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.works.program')) data-dropdown="btn"@endif>
        Programm
      </a>
    </li>
    <li class="{{ request()->routeIs('page.works.state') ? 'is-selected' : '' }}">
      <a 
        href="{{ request()->routeIs('page.works.state') ? 'javascript:;' : route('page.works.state') }}" 
        class="{{ request()->routeIs('page.works.state') ? 'btn-dropdown' : '' }}"@if (request()->routeIs('page.works.state')) data-dropdown="btn"@endif>
        Status
      </a>
    </li>
  </ul>
</nav>