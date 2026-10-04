<div class="project-grid-{{$grid->layout->key}} js-project-grid">
  <div>
    @if (isset($grid->elements[0]))
      <figure class="visual-fit {{ $grid->elements[0]->image->is_plan ? 'is-plan' : ''}}">
        <x-image :image="$grid->elements[0]->image" preset="large" />
      </figure>
    @endif
  </div>
  <div>
    <div class="project-grid__stack">
      @if (isset($grid->elements[1]))
        <figure class="visual-fit is-half {{ $grid->elements[1]->image->is_plan ? 'is-plan' : ''}}">
          <x-image :image="$grid->elements[1]->image" preset="large" />
        </figure>
      @endif
      @if (isset($grid->elements[2]))
        <figure class="visual-fit is-half {{ $grid->elements[2]->image->is_plan ? 'is-plan' : ''}}">
          <x-image :image="$grid->elements[2]->image" preset="large" />
        </figure>
      @endif
    </div>
  </div>
</div>