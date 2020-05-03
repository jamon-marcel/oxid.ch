<div class="project-grid-{{$grid->layout->key}} js-project-grid">
  <div>
    <div class="project-grid__stack">
      @if (isset($grid->elements[0]))
        <figure class="visual-fit is-half {{ $grid->elements[0]->image->is_plan ? 'is-plan' : ''}}">
          <img src="/image/large/{{$grid->elements[0]->image->name}}" height="400" width="800">
        </figure>
      @endif
      @if (isset($grid->elements[1]))
        <figure class="visual-fit is-half {{ $grid->elements[1]->image->is_plan ? 'is-plan' : ''}}">
          <img src="/image/large/{{$grid->elements[1]->image->name}}" height="400" width="800">
        </figure>
      @endif
    </div>
  </div>
  <div>
    @if (isset($grid->elements[2]))
      <figure class="visual-fit {{ $grid->elements[2]->image->is_plan ? 'is-plan' : ''}}">
        <img src="/image/large/{{$grid->elements[2]->image->name}}" height="400" width="800">
      </figure>
    @endif
  </div>
</div>