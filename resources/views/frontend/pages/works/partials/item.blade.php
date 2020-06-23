<article 
  class="js-filter-item"
  data-filter-reuse="{{$p->is_filter_reuse}}" 
  data-filter-wood="{{$p->is_filter_wood}}"
  data-filter-area="{{$p->is_filter_area}}">
  @if ($p->workImage)
    {!! ImageHelper::previewImage($p->workImage, $p->title_short) !!}
  @endif
  @if ($p->has_detail)
    <h2>
      <a href="{{ route('page.project', ['slug' => AppHelper::slug($p->title_short), 'project' => $p->id]) }}" title="{{$p->title_short}}, {{$p->location}}">
        {{$p->title_short}}, {{$p->location}}
      </a>
    </h2>
  @else
    <h2>{{$p->title_short}}, {{$p->location}}</h2>
  @endif
  <div class="work-item__info">
    @if ($p->year_works)
      Jahr: {{$p->year_works}}<br>
    @endif
    @if ($p->client_works)
      Auftrag: {{$p->client_works}}<br>
    @endif
    @if ($p->principal_works)
      Bauherrschaft: {{$p->principal_works}}<br>
    @endif
    @if ($p->author_works)
      Autorenschaft: {{$p->author_works}}<br>
    @endif
  </div>
</article>