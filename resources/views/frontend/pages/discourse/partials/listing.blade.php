<div class="discourse__grid">
  @foreach($discourse as $d)
    <article class="discourse-item">
      <header class="discourse-item__header">
        <span>{{$d->heading}}, {{$d->date}}</span>
        <h2>{{$d->title}}</h2>
      </header>
      <div class="discourse-item__body">
        @if (isset($d->publishedImages[0]))
          <figure>
            <img src="/image/small/{{$d->publishedImages[0]->name}}" height="400" width="800">
          </figure>
        @endif
        <div>
          {!! $d->description_short !!}
        </div>
      </div>
    </article>
  @endforeach
</div>