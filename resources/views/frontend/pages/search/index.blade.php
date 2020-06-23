@extends('frontend.layout.pages')
@section('seo_title', 'Suche')
@section('seo_description', '')
@section('content')
<section class="content" style="padding: 50px">
  <form method="POST" action="{{ route('page.search.index') }}">
    @csrf
    <input type="text" name="keyword">
    <input type="submit" value="Suche">
  </form>
  <br><br>
  @if ($results)
  <div>
    @if ($results['projects'])
      @foreach($results['projects'] as $p)
        <article style="margin-bottom: 20px">
          <h2>{{$p->title_short}}</h2>
        </article>
      @endforeach
    @endif

    @if ($results['discourse'])
      @foreach($results['discourse'] as $d)
        <article style="margin-bottom: 20px">
          <a href="{{ route('page.discourse.detail', ['slug' => AppHelper::slug($d->title), 'discourse' => $d->id]) }}">
            <strong>Diskurs</strong><br>
            <h2>{{$d->title}}</h2>
          </a>
        </article>
      @endforeach
    @endif
  </div>
  @endif
</section>
@endsection