@foreach($teasers as $t)
  @if ($t->teaserImage)
    <figure class="project-teaser-image" data-project-teaser="{{$t->id}}">
      <x-image :image="$t->teaserImage" preset="teaser" />
    </figure>
  @endif
@endforeach
