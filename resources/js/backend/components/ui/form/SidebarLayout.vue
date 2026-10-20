<template>
  <div class="flex flex-col gap-32 lg:flex-row lg:items-start">
    <div class="min-w-0 flex-1">
      <slot />
    </div>
    <aside class="w-full shrink-0 rounded-md border border-gray-200 bg-gray-50/50 p-16 lg:relative lg:top-24 lg:w-280 dark:border-warm-700 dark:bg-warm-800">
      <slot name="sidebar" />
    </aside>
  </div>
</template>
