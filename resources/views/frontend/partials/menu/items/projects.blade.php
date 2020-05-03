<ul class="{{request()->routeIs('page.project*') ? 'is-visible' : ''}}">
  <li class="project-filter">
    <a href="javascript:;" class="js-filter-btn" data-filter="all">Alle</a>
    <a href="javascript:;" class="js-filter-btn" data-filter="wood">Holz</a>
    <a href="javascript:;" class="js-filter-btn" data-filter="reuse">Umnutzung</a>
    <a href="javascript:;" class="js-filter-btn" data-filter="area">Areal</a>
  </li>
  @if ($menuItems['project'])
    @foreach($menuItems['project'] as $item)
      <li>
      <a 
        href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
        title="{{$item->title_short}}" 
        class="{{ request()->segment(2) == $item->id ? 'is-active' : '' }} js-filter-item"
        data-filter-reuse="{{$item->is_filter_reuse}}" 
        data-filter-wood="{{$item->is_filter_wood}}"
        data-filter-area="{{$item->is_filter_area}}">
          {{$item->title_short}}, {{$item->location}}
        </a>
      </li>
    @endforeach
  @endif
</ul>

