@extends('frontend.layout.home')
@section('seo_title', 'Home')
@section('seo_description', 'Burkhalter Sumi Architekten wird Oxid Architektur. Baut auf den Werten auf, die Yves Schihin und Urs Rinklef als langjährige Partner von Burkhalter Sumi Architekten mitgeprägt haben: die umsichtige Gestaltung des gemeinsamen Lebensraums. Oxid beschreibt unser neues Denken. Oxid reagiert, Oxid schafft neue und mutige Verbindungen. Oxid Architektur fokussiert auf die grossen Themen der Zeit: Klima und Gesellschaft. Wir denken  systemisch, wo immer sinnvoll in Holz (Reduce), und nutzen den Bestand (Re-Use).')
@section('content')
<section class="content-home grid-home">
  @if ($image)
    <figure class="span visual-fit is-home">
      <img src="/img/home/{{$image->name}}" height="400" width="800">
    </figure>
    
  @endif
  <div class="span news-listing">
    @if ($news)
      @foreach($news as $n)
        <article class="news">
          <header>
            <h2>{{$n->title}}</h2>
          </header>
          @if ($n->subtitle)
            <h3>{{$n->subtitle}}</h3>
          @endif
          {!! $n->text !!}
        </article>
      @endforeach
    @endif
  </div>
</section>
@endsection