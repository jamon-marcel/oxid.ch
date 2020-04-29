<ul class="{{request()->routeIs('page.project*') ? 'is-visible' : ''}}">
  <li class="project-filter">
    <a href="">Holz</a>
    <a href="">Umnutzung</a>
    <a href="">Areal</a>
  </li>
  @if ($menuItems['project'])
    @foreach($menuItems['project'] as $item)
      <li>
      <a href="{{ route('page.project', ['slug' => AppHelper::slug($item), 'project' => $item->id]) }}" title="{{$item->title_short}}" class="{{ request()->segment(2) == $item->id ? 'is-active' : '' }}">
          {{$item->title_short}}
        </a>
      </li>
    @endforeach
  @endif
</ul>