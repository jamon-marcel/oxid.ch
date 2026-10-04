{{-- Project index --}}
@if (!request()->routeIs('page.project'))
  <ul class="{{request()->routeIs('page.project*') ? 'is-visible' : ''}}">
    <li class="project-filter">
      <a href="javascript:;" class="is-active" data-filter="btn" data-filter-value="all">Alle</a>
      <a href="javascript:;" data-filter="btn" data-filter-value="wood">Holz</a>
      <a href="javascript:;" data-filter="btn" data-filter-value="reuse">Umnutzung</a>
      <a href="javascript:;" data-filter="btn" data-filter-value="area">Areal</a>
    </li>
    @if ($menuProjects)
      @foreach($menuProjects as $item)
        <li>
        <a 
          href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
          title="{{$item->title_short}}" 
          class="{{ request()->routeIs('page.projects') && $loop->first ? 'is-active' : '' }}" data-filter="item"
          data-filter-reuse="{{$item->is_filter_reuse}}" 
          data-filter-wood="{{$item->is_filter_wood}}"
          data-filter-area="{{$item->is_filter_area}}"
          data-project-id="{{$item->id}}">
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
        <a href="javascript:;" data-filter="btn" data-filter-value="all">Alle</a>
        <a href="javascript:;" class="is-active" data-filter="btn" data-filter-value="wood">Holz</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="reuse">Umnutzung</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="area">Areal</a>
      </li>
      @if ($menuProjects)
        @foreach($menuProjects as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }}" data-filter="item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              data-project-id="{{$item->id}}"@unless ($item->is_filter_wood) hidden @endunless>
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif

    @elseif ($project->is_filter_reuse)
      <li class="project-filter">
        <a href="javascript:;" data-filter="btn" data-filter-value="all">Alle</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="wood">Holz</a>
        <a href="javascript:;" class="is-active" data-filter="btn" data-filter-value="reuse">Umnutzung</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="area">Areal</a>
      </li>
      @if ($menuProjects)
        @foreach($menuProjects as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }}" data-filter="item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              data-project-id="{{$item->id}}"@unless ($item->is_filter_reuse) hidden @endunless>
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif

    @elseif ($project->is_filter_area)
      <li class="project-filter">
        <a href="javascript:;" data-filter="btn" data-filter-value="all">Alle</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="wood">Holz</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="reuse">Umnutzung</a>
        <a href="javascript:;" class="is-active" data-filter="btn" data-filter-value="area">Areal</a>
      </li>
      @if ($menuProjects)
        @foreach($menuProjects as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }}" data-filter="item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              data-project-id="{{$item->id}}"@unless ($item->is_filter_area) hidden @endunless>
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif

    @else
      <li class="project-filter">
        <a href="javascript:;" data-filter="btn" data-filter-value="all">Alle</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="wood">Holz</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="reuse">Umnutzung</a>
        <a href="javascript:;" data-filter="btn" data-filter-value="area">Areal</a>
      </li>
      @if ($menuProjects)
        @foreach($menuProjects as $item)
          <li>
            <a 
              href="{{ route('page.project', ['slug' => AppHelper::slug($item->title_short), 'project' => $item->id]) }}" 
              title="{{$item->title_short}}" 
              class="{{ $project->id == $item->id ? 'is-active' : '' }}" data-filter="item"
              data-filter-reuse="{{$item->is_filter_reuse}}" 
              data-filter-wood="{{$item->is_filter_wood}}"
              data-filter-area="{{$item->is_filter_area}}"
              data-project-id="{{$item->id}}">
              {{$item->title_short}}, {{$item->location}}
            </a>
          </li>
        @endforeach
      @endif
    @endif
  </ul>
@endif



