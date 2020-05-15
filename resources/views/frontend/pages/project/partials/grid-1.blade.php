<div class="project-grid-{{$grid->layout->key}} js-project-grid">
  @foreach($grid->elements as $element)
    <figure class="visual-fit {{ $element->image->is_plan ? 'is-plan' : ''}}">
      <img src="/image/project/{{$element->image->name}}" height="400" width="800">
    </figure>
  @endforeach
</div>