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
              @if ($contact->google_maps_url)
                <a href="{{ $contact->google_maps_url }}" class="google-maps" target="_blank">Google Maps</a>
              @endif
            </address>
          @endif
          
          <span class="socials">
            <a href="https://www.linkedin.com/company/oxid-architektur/" target="_blank" rel="noopener noreferrer">
              @include('frontend.partials.icons.linkedin')
            </a>
            <a href="https://www.instagram.com/oxid_architektur/" target="_blank" rel="noopener noreferrer">
              @include('frontend.partials.icons.instagram')
            </a>
          </span>

          @if ($contact->contacts)
            <span class="contacts">
              {!! $contact->contacts !!}
            </span>
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