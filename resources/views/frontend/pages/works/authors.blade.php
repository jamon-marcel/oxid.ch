@extends('frontend.layout.pages')
@section('seo_title', 'Werkliste nach Autorenschaft')
@section('seo_description', '')
@section('content')
<section class="content content--works">
  @if ($projects)
    <div class="works">
      @foreach($projects as $key => $group)
        <div class="collapsible {{ $loop->first ? 'is-expanded' : ''}} js-clpsbl">
          <div class="works__heading">
            <h1>
              <a href="javascript:;" class="btn-collapsible js-clpsbl-btn">
                {!! __('content.author_heading_' . $key) !!}
              </a>
            </h1>
          </div>
          <div class="works__grid collapsible__content js-clpsbl-body" style="{{ $loop->first ? 'display: block' : 'display: none'}}">
            <span class="works__authors">{!! __('content.author_description_' . $key) !!}</span>
            <div class="works__items {{$loop->last ? 'is-last' : ''}} js-filter-items">
              @foreach($group as $p)
                @if (isset($p->workImage))
                  @include('frontend.pages.works.partials.item')
                @endif
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
   </div>
  @endif
</section>
@endsection