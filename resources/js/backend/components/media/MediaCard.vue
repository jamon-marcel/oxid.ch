<template>
  <div class="group relative overflow-hidden rounded-md border border-gray-200 bg-white dark:border-warm-700 dark:bg-warm-800">
    <div class="flex aspect-square items-center justify-center overflow-hidden" :class="hidden ? 'opacity-40' : ''">
      <div v-if="file" class="flex flex-col items-center gap-8 text-gray-400 dark:text-warm-500">
        <PhFilePdf :size="32" weight="light" />
        <span class="text-xs uppercase">{{ extension }}</span>
      </div>
      <img v-else :src="src" alt="" loading="lazy" class="h-full w-full object-cover">
    </div>

    <div class="border-t border-gray-100 px-10 py-8 dark:border-warm-700">
      <div class="truncate text-xs text-gray-900 dark:text-warm-100" :title="label">{{ label }}</div>
      <div v-if="meta" class="mt-2 truncate text-xs text-gray-400 dark:text-warm-500" :title="meta">{{ meta }}</div>
      <div v-if="hidden || $slots.badges" class="mt-6 flex flex-wrap items-center gap-4">
        <Badge v-if="hidden">Verborgen</Badge>
        <slot name="badges" />
      </div>
    </div>

    <!-- Actions: a bar sliding down from the top on hover -->
    <div class="absolute inset-x-8 top-8 -translate-y-[calc(100%+8px)] overflow-hidden rounded-md transition-transform duration-150 group-focus-within:translate-y-0 group-hover:translate-y-0">
      <div class="flex items-center justify-around bg-black/80 px-10 py-8">
        <a :href="href" target="_blank" class="text-white/70 hover:text-white" title="In neuem Tab öffnen">
          <PhArrowSquareOut :size="15" weight="light" />
        </a>
        <button type="button" class="cursor-pointer text-white/70 hover:text-white" :title="hidden ? 'Veröffentlichen' : 'Verbergen'" @click.stop="emit('toggle')">
          <PhEyeSlash v-if="hidden" :size="15" weight="light" />
          <PhEye v-else :size="15" weight="light" />
        </button>
        <button type="button" class="cursor-pointer text-white/70 hover:text-white" title="Bearbeiten" @click.stop="emit('edit')">
          <PhPencil :size="15" weight="light" />
        </button>
        <button v-if="!file" type="button" class="cursor-pointer text-white/70 hover:text-white" title="Zuschneiden" @click.stop="emit('crop')">
          <PhCrop :size="15" weight="light" />
        </button>
        <button
          type="button"
          class="cursor-pointer text-white/70 hover:text-white disabled:cursor-not-allowed disabled:opacity-30"
          :disabled="locked"
          :title="locked ? 'Im Layout verwendet' : 'Löschen'"
          @click.stop="emit('delete')"
        >
          <PhTrash :size="15" weight="light" />
        </button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { computed } from 'vue';
import { PhTrash, PhPencil, PhCrop, PhArrowSquareOut, PhFilePdf, PhEye, PhEyeSlash } from '@phosphor-icons/vue';
import Badge from '@/components/ui/badge/Badge.vue';

// An image or a file: preview, name and a second line, the actions in a bar
// that slides in on hover
const props = defineProps({
  src: { type: String, default: null },
  // opens in a new tab
  href: { type: String, required: true },
  label: { type: String, default: '' },
  meta: { type: String, default: '' },
  hidden: { type: Boolean, default: false },
  // can't be deleted (an image used in the project layout)
  locked: { type: Boolean, default: false },
  // a document: an icon instead of the image, no crop
  file: { type: Boolean, default: false },
});

const emit = defineEmits(['edit', 'crop', 'toggle', 'delete']);

const extension = computed(() => props.label.split('.').pop());
</script>
