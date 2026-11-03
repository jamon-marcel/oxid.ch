@if ($composition['columns'])
<div class="project-grid project-grid-{{ $grid->layout->key }}" data-project="grid" style="--r: {{ round($composition['ratio'], 4) }}; --c: {{ round($composition['gaps'], 4) }}">
  <div class="project-grid__composition">
    @foreach ($composition['columns'] as $column)
      <div class="project-grid__col" style="--cr: {{ round($column['ratio'], 4) }}; --cn: {{ $column['gaps'] }}">
        @foreach ($column['images'] as $item)
          <figure class="project-grid__image {{ $item['image']->is_plan ? 'is-plan' : '' }}" style="--fr: {{ round($item['share'], 4) }}">
            <x-image :image="$item['image']" preset="large" />
          </figure>
        @endforeach
      </div>
    @endforeach
  </div>
</div>
@endif
