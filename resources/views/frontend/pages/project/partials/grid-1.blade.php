<div class="project-grid-{{$grid->layout->key}} js-project-grid">
  @foreach($grid->elements as $element)
    <figure class="visual-fit {{ $element->image->is_plan ? 'is-plan' : ''}}">
      {!! ImageHelper::largeImage($element->image, '') !!}
    </figure>
  @endforeach
</div>