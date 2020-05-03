<div class="discourse__grid">
  @foreach($discourse as $d)
    <article class="discourse-item">
      <header class="discourse-item__header">
        <span>{{$d->heading}}, {{$d->date}}</span>
        <h2>{{$d->title}}</h2>
      </header>
      <div class="discourse-item__body">
        @if (isset($d->previewImage))
          <figure>
            <a href="{{ route('page.discourse.detail', ['slug' => AppHelper::slug($d->title), 'discourse' => $d->id]) }}">
              <img src="/image/clsmd/{{$d->previewImage->name}}" width="1000" height="680" alt="{{$d->title}}">
            </a>
          </figure>
        @endif
        <div>{!! $d->description_short !!}</div>
      </div>
    </article>
  @endforeach
</div>