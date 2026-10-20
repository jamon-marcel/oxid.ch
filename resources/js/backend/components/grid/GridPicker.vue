<template>
  <Drawer :open="open" title="Bild wählen" size="lg" @close="emit('close')">
    <div class="flex flex-col gap-16 px-24 py-16">
      <div class="flex items-center gap-16">
        <FormInput v-model="search" name="picker_search" placeholder="Suchen…" class="flex-1" />
        <FormCheckbox id="picker_unplaced" v-model="hidePlaced">Nur unplatzierte</FormCheckbox>
      </div>
      <div v-if="!visible.length" class="text-sm text-gray-400 dark:text-warm-500">Keine Bilder vorhanden.</div>
      <div class="grid grid-cols-4 gap-8">
        <button
          v-for="image in visible"
          :key="image.id"
          type="button"
          class="relative aspect-square cursor-pointer overflow-hidden rounded-sm bg-gray-100 hover:ring-2 hover:ring-gray-900 dark:bg-warm-800 dark:hover:ring-warm-100"
          :title="displayName(image.name)"
          @click="emit('select', image.id)"
        >
          <img :src="imageUrl(image, 'thumbnail')" alt="" class="h-full w-full object-cover" loading="lazy">
          <span v-if="Number(image.is_grid) === 1" class="absolute top-6 right-6 rounded-full bg-white/90 px-6 py-2 text-[0.625rem] text-gray-700">platziert</span>
        </button>
      </div>
    </div>
  </Drawer>
</template>
<script setup>
import { ref, computed, watch } from 'vue';
import Drawer from '@/components/ui/drawer/Drawer.vue';
import FormInput from '@/components/ui/form/FormInput.vue';
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue';
import { imageUrl } from '@/lib/images';
import { displayName } from '@/lib/utils';

// Choose one of the project's images for a box
const props = defineProps({
  open: { type: Boolean, default: false },
  images: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'select']);

const search = ref('');
const hidePlaced = ref(0);

watch(() => props.open, open => open && (search.value = ''));

const visible = computed(() => {
  const term = search.value.toLowerCase();
  return props.images.filter(image => {
    if (hidePlaced.value && Number(image.is_grid) === 1) {
      return false;
    }
    return !term || `${image.name} ${image.caption?.de ?? ''}`.toLowerCase().includes(term);
  });
});
</script>
