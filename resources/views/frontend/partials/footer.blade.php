@vite('resources/js/frontend/app.js')
@if (request()->routeIs('page.contact'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}"></script>
@vite('resources/js/frontend/maps.js')
@endif
@production
<!-- Google tag (gtag.js) - Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-B6RDB30W93"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-B6RDB30W93');
</script>
@endproduction
</body>
<!-- made with ❤ by bivgrafik GmbH & marceli.to -->
</html>