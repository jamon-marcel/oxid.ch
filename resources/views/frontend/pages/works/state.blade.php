@extends('frontend.layout.pages')
@section('seo_title', 'Werkliste nach Status')
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
                {{__('settings.' . config('settings.state.' . $key))}}
              </a>
            </h1>
          </div>
          <div class="works__grid collapsible__content js-clpsbl-body" style="{{ $loop->first ? 'display: block' : 'display: none'}}">
            <div class="works__items js-filter-items">
              @foreach($group as $p)
                @if (isset($p->workImage[0]))
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