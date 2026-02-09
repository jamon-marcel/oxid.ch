<ul>
  <li>
    <a href="{{ route('page.projects') }}" 
       class="{{request()->routeIs('page.project*') ? 'is-active' : ''}} js-menu-parent">
     Projekte
    </a>
    @include('frontend.partials.menu.items.projects')
  </li>
  <li>
    <a href="{{ route('page.works.authors') }}" 
       class="{{request()->routeIs('page.works*') ? 'is-active' : ''}} js-menu-parent">
       Werkliste
    </a>
    @include('frontend.partials.menu.items.works')
  </li>
  <li>
    <a href="{{ route('page.discourse') }}" 
       class="{{request()->routeIs('page.discourse*') ? 'is-active' : ''}} js-menu-parent">
       Diskurs
    </a>
    @include('frontend.partials.menu.items.discourse')
  </li>
</ul>
<ul>
  <li>
    <a href="{{ route('page.history') }}" 
        class="{{request()->routeIs('page.history') ? 'is-active' : ''}}">
        Geschichte
    </a>
  </li>
  <li>
    <a href="{{ route('page.office.team') }}" 
       class="{{request()->routeIs('page.office*') ? 'is-active' : ''}} js-menu-parent">
     Büro
    </a>
    @include('frontend.partials.menu.items.office')
  </li>
  <li>
    <a href="{{ route('page.contact') }}" 
       class="{{request()->routeIs('page.contact') ? 'is-active' : ''}}">
     Kontakt
    </a>
  </li>
</ul>
<ul class="meta">
  <li>
    <a href="{{ route('page.search.index') }}" class="icon-magnifier"></a>
  </li>
  <li class="social"> 
    <a href="https://www.linkedin.com/company/oxid-architektur/" target="_blank" rel="noopener noreferrer">
      @include('frontend.partials.icons.linkedin')
    </a>
    <a href="https://www.instagram.com/oxid_architektur/" target="_blank" rel="noopener noreferrer">
      @include('frontend.partials.icons.instagram')
    </a>
  </li>
</ul>
{{-- <ul class="language">
  <li>
    <a href="">De</a>/<a href="">En</a>
  </li>
</ul> --}}