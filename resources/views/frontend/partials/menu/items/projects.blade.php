{{-- Project index --}}
@if (!request()->routeIs('page.project'))
  <ul class="{{request()->routeIs('page.project*') ? 'is-visible' : ''}}">
    <li class="project-filter">
      <a href="javascript:;" class="js-filter-btn is-active" data-filter="wood">Holz</a>
      <a href="javascript:;" class="js-filter-btn" data-filter="reuse">Umnutzung</a>
      <a href="javascript:;" class="js-filter-btn" data-filter="area">Areal</a>
    </li>
    @if ($menuItems['project'])
      @foreach($menuItems['project'] as $item)
        <li>
        <a 
          href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
          title="{{$item->title_short}}" 
          class="{{ request()->routeIs('page.projects') && $loop->first ? 'is-active' : '' }} js-filter-item"
          data-filter-reuse="{{$item->is_filter_reuse}}" 
          data-filter-wood="{{$item->is_filter_wood}}"
          data-filter-area="{{$item->is_filter_area}}"
          style="{{$item->is_filter_wood ? 'display: block' : 'display: none'}}">
            {{$item->title_short}}, {{$item->location}}
          </a>
        </li>
      @endforeach
    @endif
  </ul>
@endif

{{-- Project show --}}
@if (request()->routeIs('page.project'))
  <ul class="{{request()->routeIs('page.project*') ? 'is-visible' : ''}}">
    
    @if ($project->is_filter_wood)
      <li class="project-filter">
        <a href="javascript:;" class="js-filter-btn is-active" data-filter="wood">Holz</a>
        <a href="javascript:;" class="js-filter-btn" data-filter="reuse">Umnutzung</a>
        <a href="javascript:;" class="js-filter-btn" data-filter="area">Areal</a>
      </li>
      @if ($menuItems['project'])
        @foreach($menuItems['project'] as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }} js-filter-item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              style="{{$item->is_filter_wood ? 'display: block' : 'display: none'}}">
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif

    @elseif ($project->is_filter_reuse)
      <li class="project-filter">
        <a href="javascript:;" class="js-filter-btn" data-filter="wood">Holz</a>
        <a href="javascript:;" class="js-filter-btn is-active" data-filter="reuse">Umnutzung</a>
        <a href="javascript:;" class="js-filter-btn" data-filter="area">Areal</a>
      </li>
      @if ($menuItems['project'])
        @foreach($menuItems['project'] as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }} js-filter-item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              style="{{$item->is_filter_reuse ? 'display: block' : 'display: none'}}">
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif

    @elseif ($project->is_filter_area)
      <li class="project-filter">
        <a href="javascript:;" class="js-filter-btn" data-filter="wood">Holz</a>
        <a href="javascript:;" class="js-filter-btn" data-filter="reuse">Umnutzung</a>
        <a href="javascript:;" class="js-filter-btn is-active" data-filter="area">Areal</a>
      </li>
      @if ($menuItems['project'])
        @foreach($menuItems['project'] as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }} js-filter-item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              style="{{$item->is_filter_area ? 'display: block' : 'display: none'}}">
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif

    @else
      <li class="project-filter">
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
              class="{{ $project->id == $item->id ? 'is-active' : '' }} js-filter-item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}">
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif
    @endif
  </ul>
@endif



