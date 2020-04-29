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
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15">
          <polygon style="fill:#3259a1;" points="3.8 0 3.8 1.8 11.6 1.8 0 13.3 1.3 14.6 12.9 3 12.9 10.8 14.6 10.8 14.6 0 3.8 0"/>
        </svg>
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