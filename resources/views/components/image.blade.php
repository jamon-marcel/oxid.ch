@props(['image', 'preset', 'alt' => ''])
@php
  $sizes = config('images.presets.' . $preset);
  [$width, $height] = $image->displaySize() ?? [null, null];
@endphp
<picture>
  @foreach (\App\Support\ImageSupport::modernFormats() as $format)
    <source type="image/{{ $format }}" srcset="{{ $image->srcset($sizes, $format) }}">
  @endforeach
  <img srcset="{{ $image->srcset($sizes) }}" src="{{ $image->url($sizes[0]) }}" @if ($width) width="{{ $width }}" height="{{ $height }}" @endif alt="{{ $alt }}">
</picture>
