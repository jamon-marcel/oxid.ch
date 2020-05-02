<article 
  class="js-filter-item"
  data-filter-reuse="{{$p->is_filter_reuse}}" 
  data-filter-wood="{{$p->is_filter_wood}}"
  data-filter-area="{{$p->is_filter_area}}">
  <img src="/image/small/{{$p->workImage[0]->name}}" height="400" width="800">
  @if ($p->has_detail)
    <h2>
      <a href="{{ route('page.project', ['slug' => AppHelper::slug($p), 'project' => $p->id]) }}" title="{{$p->title_short}}, {{$p->location}}">
        {{$p->title_short}}, {{$p->location}}
        @include('frontend.partials.icons.arrow-external-blue')
      </a>
    </h2>
  @else
    <h2>{{$p->title_short}}, {{$p->location}}</h2>
  @endif
  <div class="work-item__info">
    Autorenschaft: {!! __('content.author_heading_' . $p->author) !!}<br>
    @if ($p->year_works)
      Jahr: {{$p->year_works}}<br>
    @endif
    @if ($p->year_works)
      Auftraggeber: {{$p->client_works}}<br>
    @endif
    @if ($p->year_works)
      Bauherrschaft: {{$p->principal_works}}<br>
    @endif
  </div>
</article>