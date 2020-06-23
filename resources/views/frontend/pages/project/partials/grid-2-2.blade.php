<div class="project-grid-{{$grid->layout->key}} js-project-grid">
  <div>
    <div class="project-grid__stack">
      @if (isset($grid->elements[0]))
        <figure class="visual-fit is-half {{ $grid->elements[0]->image->is_plan ? 'is-plan' : ''}}">
          {{-- <img data-src="/image/project/{{$grid->elements[0]->image->name}}" height="400" width="800" class="lazyload"> --}}
          {!! ImageHelper::largeImage($grid->elements[0]->image, '') !!}
        </figure>
      @endif
      @if (isset($grid->elements[2]))
        <figure class="visual-fit is-half {{ $grid->elements[2]->image->is_plan ? 'is-plan' : ''}}">
          {{-- <img data-src="/image/project/{{$grid->elements[2]->image->name}}" height="400" width="800" class="lazyload"> --}}
          {!! ImageHelper::largeImage($grid->elements[2]->image, '') !!}
        </figure>
      @endif
    </div>
  </div>
  <div>
    <div class="project-grid__stack">
      @if (isset($grid->elements[1]))
        <figure class="visual-fit is-half {{ $grid->elements[1]->image->is_plan ? 'is-plan' : ''}}">
          {{-- <img data-src="/image/project/{{$grid->elements[1]->image->name}}" height="400" width="800" class="lazyload"> --}}
          {!! ImageHelper::largeImage($grid->elements[1]->image, '') !!}
        </figure>
      @endif
      @if (isset($grid->elements[3]))
        <figure class="visual-fit is-half {{ $grid->elements[3]->image->is_plan ? 'is-plan' : ''}}">
          {{-- <img data-src="/image/project/{{$grid->elements[3]->image->name}}" height="400" width="800" class="lazyload"> --}}
          {!! ImageHelper::largeImage($grid->elements[3]->image, '') !!}
        </figure>
      @endif
    </div>
  </div>
</div>