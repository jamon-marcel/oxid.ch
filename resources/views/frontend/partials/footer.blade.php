@vite('resources/js/frontend/app.js')
@if (request()->routeIs('page.contact'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}"></script>
@vite('resources/js/frontend/maps.js')
@endif
@production
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-171670650-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-171670650-1');
</script>
@endproduction
</body>
<!-- made with ❤ by bivgrafik GmbH & marceli.to -->
</html>