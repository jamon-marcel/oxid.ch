<div class="project-grid-{{$grid->layout->key}} js-project-grid">
  @foreach($grid->elements as $element)
    <figure class="visual-fit">
      <img src="/image/large/{{$element->image->name}}" height="400" width="800">
    </figure>
  @endforeach
</div>