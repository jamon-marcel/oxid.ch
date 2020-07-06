<script src="{{ asset('assets/js/app.js') }}" type="text/javascript"></script>
@if (request()->routeIs('page.contact'))
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxU6MLGw6kEAd9ejc8GfqXx38qvm0oHPI"></script>
<script src="{{ asset('assets/js/maps.js') }}" type="text/javascript"></script>
@endif
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-171670650-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-171670650-1');
</script>
</body>
<!-- made with ❤ by bivgrafik GmbH & marceli.to -->
</html>