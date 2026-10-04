<div class="project-grid-{{$grid->layout->key}}" data-project="grid">
  @foreach($grid->elements as $element)
    <figure class="visual-fit {{ $element->image->is_plan ? 'is-plan' : ''}}">
      <x-image :image="$element->image" preset="large" />
    </figure>
  @endforeach
</div>