<template>
  <div>
    <FormLabel :error="error ? 'Mindestens ein Bild hochladen' : null">{{ label }}</FormLabel>
    <div class="mt-8 flex flex-col gap-16">
      <MediaUploader v-bind="imageUpload" :compact="images.length > 0" @uploaded="emit('uploaded', $event)" />
      <draggable
        v-if="images.length"
        v-model="images"
        :item-key="image => image.id ?? image.name"
        :disabled="!sortable"
        :class="['grid gap-20', wide ? 'grid-cols-3 lg:grid-cols-5' : 'grid-cols-2 sm:grid-cols-3']"
        ghost-class="opacity-30"
        :animation="150"
        @end="order"
      >
        <template #item="{ element: image }">
          <div :class="sortable ? 'cursor-grab active:cursor-grabbing' : ''">
            <MediaCard
              :src="imageUrl(image, 'thumbnail')"
              :href="imageUrl(image, 'large')"
              :label="displayName(image.name)"
              :meta="image.caption?.de ?? ''"
              :hidden="Number(image.publish) === 0"
              :locked="isProtected(image)"
              @toggle="emit('toggle', image)"
              @edit="openEdit(image)"
              @crop="cropping = image"
              @delete="emit('destroy', image)"
            >
              <template #badges>
                <slot name="badges" :image="image" />
              </template>
            </MediaCard>
          </div>
        </template>
      </draggable>
    </div>

    <MediaEdit :item="editing" :saves="saveOnEdit" @close="editing = null" @save="emit('update', $event); editing = null">
      <template #default="{ item }">
        <slot name="fields" :image="item" />
      </template>
    </MediaEdit>
    <MediaCrop :image="cropping" :ratio="cropping ? ratio(cropping) : null" @close="cropping = null" @save="emit('save-coords', $event); cropping = null" />
  </div>
</template>
<script setup>
import { ref } from 'vue';
import draggable from 'vuedraggable';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import MediaUploader from '@/components/media/MediaUploader.vue';
import MediaCard from '@/components/media/MediaCard.vue';
import MediaEdit from '@/components/media/MediaEdit.vue';
import MediaCrop from '@/components/media/MediaCrop.vue';
import { useOrder } from '@/composables/useOrder';
import { imageUpload } from '@/composables/useImages';
import { imageUrl } from '@/lib/images';
import { displayName } from '@/lib/utils';

/**
 * Upload + manage the images of a record (or an image page): cards with
 * publish, edit (drawer), crop and delete; drag ordering when sortable.
 * The parent does the API work (useImages / useImageLibrary) on the events.
 */
const props = defineProps({
  label: { type: String, default: 'Bilder' },
  // resource path below /api, for saving the order, e.g. 'discourse'
  endpoint: { type: String, required: true },
  sortable: { type: Boolean, default: false },
  // the edit drawer saves (image pages) instead of keeping the changes on
  // the record (forms)
  saveOnEdit: { type: Boolean, default: false },
  // (image) => crop ratio (w / h), or null for a free crop
  ratio: { type: Function, default: () => 16 / 10 },
  // (image) => true if it can't be deleted (e.g. used in the project layout)
  isProtected: { type: Function, default: () => false },
  // no form sidebar next to it: more columns
  wide: { type: Boolean, default: false },
  // no image yet, but the record needs one
  error: { type: Boolean, default: false },
});

const images = defineModel('images', { type: Array, required: true });

const emit = defineEmits(['uploaded', 'toggle', 'destroy', 'save-coords', 'update']);

// Images without id aren't saved yet; their order goes with the record
const saveOrder = useOrder({ url: `/api/${props.endpoint}/image/order`, key: 'images', delay: 1000 });
function order() {
  const list = images.value;
  list.some(image => image.id === null) ? list.forEach((image, index) => image.order = index) : saveOrder(list);
}

const editing = ref(null);
const cropping = ref(null);

function openEdit(image) {
  image.caption ??= { de: null, en: null };
  editing.value = image;
}
</script>
