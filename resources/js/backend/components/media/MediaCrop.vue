<template>
  <Teleport to="body">
    <div v-if="image" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
      <div class="mx-16 flex max-h-[90vh] w-full max-w-3xl flex-col rounded-2xl bg-white shadow-xl dark:bg-warm-900" role="dialog" aria-modal="true" aria-label="Bild zuschneiden">
        <div class="flex items-center justify-between px-24 pt-16">
          <h2 class="text-sm font-medium text-gray-900 dark:text-warm-100">Bild zuschneiden</h2>
          <button type="button" class="cursor-pointer text-gray-400 transition-colors hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100" title="Schliessen" @click="close">
            <PhX :size="16" weight="light" />
          </button>
        </div>

        <div class="min-h-0 flex-1 overflow-hidden bg-white p-16 dark:bg-warm-900 [&_.vue-advanced-cropper]:!bg-gray-100 dark:[&_.vue-advanced-cropper]:!bg-warm-800">
          <div v-if="isLoading" class="flex h-[60vh] items-center justify-center text-sm text-gray-400 dark:text-warm-500">Laden...</div>
          <Cropper
            v-else
            class="h-[60vh]"
            :src="src"
            :stencil-props="{ aspectRatio: ratio }"
            :default-position="defaultPosition"
            :default-size="defaultSize"
            image-restriction="fit-area"
            @change="change"
          />
        </div>

        <div class="flex items-center justify-between px-24 pb-16">
          <span class="text-xs text-gray-400 tabular-nums dark:text-warm-500">
            <template v-if="size.w">Neue Grösse: {{ size.w }} × {{ size.h }} px</template>
          </span>
          <div class="flex gap-12">
            <FormButton variant="secondary" @click="close">Abbrechen</FormButton>
            <FormButton @click="save">Speichern</FormButton>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
<script setup>
import { ref, reactive, watch, onMounted, onUnmounted } from 'vue';
import { PhX } from '@phosphor-icons/vue';
import { Cropper } from 'vue-advanced-cropper';
import FormButton from '@/components/ui/form/FormButton.vue';
import { imageUrl, preloadImage } from '@/lib/images';

// Crop an image to its ratio; saves coords_w/h/x/y on the image
const props = defineProps({
  image: { type: Object, default: null },
  // w / h, or null for a free crop
  ratio: { type: Number, default: null },
});

const emit = defineEmits(['close', 'save']);

const src = ref(null);
const isLoading = ref(false);
const coords = reactive({ w: 0, h: 0, x: 0, y: 0 });
const size = reactive({ w: null, h: null });

// Where the crop box starts for an image without coords
const defaults = { w: 1600, h: 1000, x: 100, y: 100 };

watch(() => props.image, async image => {
  size.w = size.h = null;
  if (!image) {
    return;
  }
  isLoading.value = true;
  try {
    src.value = await preloadImage(imageUrl(image, 'original'));
  }
  finally {
    isLoading.value = false;
  }
});

function change({ coordinates }) {
  Object.assign(coords, { w: coordinates.width, h: coordinates.height, x: coordinates.left, y: coordinates.top });
  size.w = Math.floor(coordinates.width);
  size.h = Math.floor(coordinates.height);
}

function defaultPosition() {
  return { left: props.image.coords_x || defaults.x, top: props.image.coords_y || defaults.y };
}

function defaultSize() {
  return { width: props.image.coords_w || defaults.w, height: props.image.coords_h || defaults.h };
}

function save() {
  const image = props.image;
  Object.assign(image, { coords_w: coords.w, coords_h: coords.h, coords_x: coords.x, coords_y: coords.y });
  emit('save', image);
}

function close() {
  emit('close');
}

function onKeydown(event) {
  if (event.key === 'Escape' && props.image) {
    document.activeElement?.blur();
    close();
  }
}

onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));
</script>
