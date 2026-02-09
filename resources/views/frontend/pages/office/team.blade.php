@extends('frontend.layout.pages')
@section('seo_title', 'Team')
@section('seo_description', 'Team, Profil, Jobs - Yves Schihin, Urs Rinklef')
@section('content')
<section class="content">
  @if ($images)
    <a href="javascript:;" class="btn-scroll is-prev js-btn-scroll-prev" style="display:none"></a>
    <a href="javascript:;" class="btn-scroll is-next js-btn-scroll-next"></a>
    <div class="visual-list">
      @foreach($images as $image)
        <figure class="visual-fit js-scroll-item">
          {!! ImageHelper::largeImage($image, $image->title) !!}
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
              @if ($t->phone)<br><a href="tel:{{$t->phone}}">{{$t->phone}}</a>@endif
              @if ($t->documents)
                @foreach($t->documents as $d)
                  <br>
                  <a href="{{asset('storage/uploads/' . $d->name)}}" class="icon-document-cv" target="_blank" title="Download Lebenslauf">
                    @include('frontend.partials.icons.document')
                    <div><span>{{__('content.cv')}}</span></div>
                  </a>
                @endforeach
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif

    @if ($team['associate'])
      <h3>{{__('settings.associate')}}</h3>
      <div class="grid-team">
        @foreach($team['associate'] as $t)
          <article class="span team-member">
            <div class="team-member__heading">{{$t->firstname}} {{$t->name}}</div>
            <div class="team-member__info">
              @if ($t->role) {{$t->role}}<br>@endif
              @if ($t->email)<a href="mailto:{{$t->email}}">{{$t->email}}</a>@endif
              @if ($t->phone)<br><a href="tel:{{$t->phone}}">{{$t->phone}}</a>@endif
              @if ($t->documents)
                @foreach($t->documents as $d)
                  <br>
                  <a href="{{asset('storage/uploads/' . $d->name)}}" class="icon-document-cv" target="_blank" title="Download Lebenslauf">
                    @include('frontend.partials.icons.document')
                    <div><span>{{__('content.cv')}}</span></div>
                  </a>
                @endforeach
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif

    @if ($team['seniorStaff'])
      <h3>{{__('settings.senior-staff')}}</h3>
      <div class="grid-team">
        @foreach($team['seniorStaff'] as $t)
          <article class="span team-member">
            <div class="team-member__heading">{{$t->firstname}} {{$t->name}}</div>
            <div class="team-member__info">
              @if ($t->role) {{$t->role}}<br>@endif
              @if ($t->email)<a href="mailto:{{$t->email}}">{{$t->email}}</a>@endif
              @if ($t->phone)<br><a href="tel:{{$t->phone}}">{{$t->phone}}</a>@endif
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
              @if ($t->phone)<br><a href="tel:{{$t->phone}}">{{$t->phone}}</a>@endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
    @if ($team['alumni'])
      <h3>{{__('settings.alumni')}}</h3>
      <div class="grid-team is-last">
        <div>
          @foreach($team['alumni'][0] as $k => $ta)
            <article class="span span-alumni {{ $loop->last  ? 'is-last' : '' }} team-member">
              <div class="team-member__heading">{{$k}}</div>
              <div class="team-member__info">
                @foreach($ta as $t)
                  {{$t->firstname}} {{$t->name}}@if(!$loop->last),@endif
                @endforeach
              </div>
            </article>
          @endforeach
        </div>
        <div>
          @foreach($team['alumni'][1] as $k => $ta)
            <article class="span span-alumni {{ $loop->last  ? 'is-last' : '' }} team-member">
              <div class="team-member__heading">{{$k}}</div>
              <div class="team-member__info">
                @foreach($ta as $t)
                  {{$t->firstname}} {{$t->name}}@if(!$loop->last),@endif
                @endforeach
              </div>
            </article>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@endsection