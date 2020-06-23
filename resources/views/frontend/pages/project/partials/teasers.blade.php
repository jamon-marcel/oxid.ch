@foreach($teasers as $t)
  @if ($t->teaserImage)
    <figure class="project-teaser-image" data-project-teaser="{{$t->id}}">
      {!! ImageHelper::teaserImage($t->teaserImage) !!}
    </figure>
  @endif
@endforeach
