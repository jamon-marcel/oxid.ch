@extends('frontend.layout.pages')
@section('seo_title', 'Team')
@section('seo_description', '')
@section('content')
<section class="content">
  @if ($images)
    <div class="visual-list">
      @foreach($images as $image)
        <figure class="visual-fit">
          <img src="/image/large/{{$image->name}}" height="400" width="800">
        </figure>
      @endforeach
    </div>
  @endif
</section>
<div class="overlay-info js-info" data-visible-onload="1">
  <a href="javascript:;" class="btn-close js-btn-info"></a>
  <div>
    @if ($team['partner'])
      <h3>{{__('settings.partner')}}</h3>
      <div class="grid-team">
        @foreach($team['partner'] as $t)
          <article class="span team-member">
            <div class="team-member__heading">{{$t->firstname}} {{$t->name}}</div>
            <div class="team-member__info">
              @if ($t->position) {{$t->position}}<br>@endif
              @if ($t->role) {{$t->role}}<br>@endif
              @if ($t->email)<a href="mailto:{{$t->email}}">{{$t->email}}</a>@endif
              @if ($t->documents)
                @foreach($t->documents as $d)
                  <br>
                  <a href="{{asset('storage/uploads/' . $d->name)}}" class="icon-document-blue" target="_blank" title="Download Lebenslauf">
                    {{__('content.cv')}}
                  </a>
                @endforeach
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
    @if ($team['employee'])
      <h3>{{__('settings.employee')}}</h3>
      <div class="grid-team">
        @foreach($team['employee'] as $t)
          <article class="span team-member">
            <div class="team-member__heading">{{$t->firstname}} {{$t->name}}</div>
            <div class="team-member__info">
              @if ($t->role) {{$t->role}}<br>@endif
              @if ($t->email)<a href="mailto:{{$t->email}}">{{$t->email}}</a>@endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
    @if ($team['alumni'])
      <h3>{{__('settings.alumni')}}</h3>
      <div class="grid-team is-last">
        @foreach($team['alumni'] as $k => $ta)
          <article class="span {{ $loop->last  ? 'is-last' : '' }} team-member">
            <div class="team-member__heading">{{$k}}</div>
            <div class="team-member__info">
              @foreach($ta as $t)
                {{$t->firstname}} {{$t->name}}@if(!$loop->last),@endif
              @endforeach
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection