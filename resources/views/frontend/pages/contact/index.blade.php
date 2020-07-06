@extends('frontend.layout.pages')
@section('seo_title', 'Kontakt')
@section('seo_description', 'Oxid Architektur GmbH - Münstergasse 18a, 8001 Zürich')
@section('content')
<section class="content content--contact">
  <div class="contact">
    <div class="grid-contact">
      @if ($contact)
        <div class="contact__info">
          @if ($contact->address)
            <address>
              {!! $contact->address !!}
            </address>
          @endif
          <div>
            @if ($contact->info)
              {!! $contact->info !!}
            @endif
          </div>
          <div>
            <a href="javascript:;" class="js-btn-imprint">Impressum</a>
            <div style="display:none">
              @if ($contact->imprint)
                {!! $contact->imprint !!}
              @endif
            </div>
          </div>
        </div>
      @endif
      <div class="contact__map" id="js-maps"></div>
    </div>
  </div>
</section>
@endsection