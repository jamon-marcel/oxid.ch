<article 
  class="js-filter-item"
  data-filter-reuse="{{$p->is_filter_reuse}}" 
  data-filter-wood="{{$p->is_filter_wood}}"
  data-filter-area="{{$p->is_filter_area}}">
  @if ($p->workImage)
  <img src="/image/work/{{$p->workImage->name}}" width="1000" height="680" alt="{{$p->title_short}}">
  @endif
  @if ($p->has_detail)
    <h2>
      <a href="{{ route('page.project', ['slug' => AppHelper::slug($p->title_short), 'project' => $p->id]) }}" title="{{$p->title_short}}, {{$p->location}}">
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