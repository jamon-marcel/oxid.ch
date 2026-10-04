<template>
  <div>
    <a v-if="sortable" href="" class="icon-view" @click.prevent="view = view === 'grid' ? 'list' : 'grid'">
      <span v-if="view === 'grid'">Grid Ansicht</span>
      <span v-else>Listen Ansicht</span>
    </a>
    <div class="upload-listing-rows" v-if="view === 'list'">
      <draggable
        v-model="images"
        item-key="name"
        ghost-class="draggable-ghost"
        draggable=".is-draggable"
        @end="order(images)"
      >
        <template #item="{ element: image }">
          <div class="upload-item-row is-draggable">
            <figure>
              <img :src="imageUrl(image, 'thumbnail')" height="300" width="300">
            </figure>
            <div>
              <PhDotsSixVertical :size="18" weight="light" />
            </div>
          </div>
        </template>
      </draggable>
    </div>
    <div class="upload-listing" v-else>
      <div>
        <figure
          v-for="image in images"
          :key="image.id ?? image.name"
          :class="[image.publish == 0 ? 'is-disabled' : '', 'upload-item']"
        >
          <a :href="imageUrl(image, 'large')" target="_blank" class="upload__preview">
            <img :src="imageUrl(image, 'thumbnail')" height="300" width="300">
          </a>
          <div class="upload__actions">
            <div class="list-item-actions">
              <a href="javascript:;" class="feather-icon" :title="image.publish == 1 ? 'Verbergen' : 'Publizieren'" @click.prevent="emit('toggle', image)">
                <PhEye v-if="image.publish == 1" :size="18" weight="light" />
                <PhEyeSlash v-else :size="18" weight="light" class="is-off" />
              </a>
              <a href="javascript:;" class="feather-icon" title="Bearbeiten" @click.prevent="openEdit(image)">
                <PhPencilSimple :size="18" weight="light" />
              </a>
              <a :href="imageUrl(image, 'large')" target="_blank" class="feather-icon" title="Öffnen">
                <PhArrowSquareOut :size="18" weight="light" />
              </a>
              <a
                href="javascript:;"
                :class="['feather-icon', { 'is-disabled': isProtected(image) }]"
                :title="isProtected(image) ? 'Im Layout verwendet' : 'Löschen'"
                @click.prevent="!isProtected(image) && emit('destroy', image)"
              >
                <PhTrash :size="18" weight="light" />
              </a>
              <a href="javascript:;" class="feather-icon" title="Zuschneiden" @click.prevent="openCropper(image)">
                <PhCrop :size="18" weight="light" />
              </a>
            </div>
          </div>
        </figure>
      </div>
    </div>

    <div :class="[editItem ? 'is-visible' : '', 'upload-overlay-edit']">
      <div v-if="editItem">
        <a href="javascript:;" class="feather-icon upload-overlay__close" title="Schliessen" @click.prevent="editItem = null">
          <PhX :size="24" weight="light" />
        </a>
        <div>
          <figure>
            <img :src="imageUrl(editItem, 'large')" height="300" width="300">
            <figcaption v-if="editItem.caption.de || editItem.caption.en">
              <span v-if="editItem.caption.de">{{ editItem.caption.de }}</span>
              <span v-if="editItem.caption.en">{{ editItem.caption.en }}</span>
            </figcaption>
          </figure>
        </div>
        <div>
          <div class="form-row">
            <label>Bildlegende</label>
            <input type="text" v-model="editItem.caption.de">
          </div>
          <div class="form-row">
            <label>Bildlegende (en)</label>
            <input type="text" v-model="editItem.caption.en">
          </div>
          <slot name="fields" :image="editItem" />
          <div class="form-row-button">
            <a v-if="saveOnClose" href="javascript:;" class="btn-secondary" @click.prevent="emit('update', editItem); editItem = null">Speichern</a>
            <a v-else href="javascript:;" class="btn-secondary" @click.prevent="editItem = null">Schliessen</a>
          </div>
        </div>
      </div>
    </div>

    <div :class="[cropItem ? 'is-visible' : '', 'upload-overlay-cropper']">
      <div class="loader" v-if="isCropperLoading">Bild wird geladen...</div>
      <div v-else-if="cropItem">
        <a href="javascript:;" class="feather-icon upload-overlay__close" title="Schliessen" @click.prevent="cropItem = null">
          <PhX :size="24" weight="light" />
        </a>
        <div>
          <span class="cropper-info">Neue Grösse:<br>{{ cropSize.w }} x {{ cropSize.h }}px</span>
          <Cropper
            :src="cropSrc"
            :default-position="defaultPosition"
            :default-size="defaultSize"
            :stencil-props="{
              aspectRatio: cropRatio,
              linesClassnames: { default: 'line' },
              handlersClassnames: { default: 'handler' },
            }"
            @change="change"
          />
          <div class="form-buttons">
            <a href="javascript:;" class="btn-secondary" @click.prevent="saveCrop()">Speichern</a>
            <a href="javascript:;" @click.prevent="cropItem = null">Abbrechen</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import draggable from 'vuedraggable';
import { Cropper } from 'vue-advanced-cropper';
import {
  PhEye, PhEyeSlash, PhPencilSimple, PhArrowSquareOut, PhTrash, PhCrop, PhX, PhDotsSixVertical,
} from '@phosphor-icons/vue';
import { imageUrl, preloadImage } from '@/lib/images';
import { useOrder } from '@/composables/useOrder';
import { useEscape } from '@/composables/useEscape';

const props = defineProps({
  // resource path below /api, for saving the order, e.g. 'discourse'
  endpoint: { type: String, required: true },

  // grid/list toggle with drag ordering in the list
  sortable: { type: Boolean, default: false },

  // the edit overlay saves (standalone image pages) instead of just closing
  // (forms save the images with the record)
  saveOnClose: { type: Boolean, default: false },

  // (image) => crop ratio (w / h), or null for a free crop
  ratio: { type: Function, default: () => 16 / 10 },

  // (image) => true if it can't be deleted (e.g. used in the project layout)
  isProtected: { type: Function, default: () => false },
});

const images = defineModel('images', { type: Array, required: true });

const emit = defineEmits(['toggle', 'destroy', 'save-coords', 'update']);

const view = ref('grid');

// Images without id aren't saved yet; their order goes with the record
const saveOrder = useOrder({ url: `/api/${props.endpoint}/image/order`, key: 'images', delay: 1000 });
function order(list) {
  list.some(image => image.id === null) ? list.forEach((image, index) => image.order = index) : saveOrder(list);
}

// Edit overlay
const editItem = ref(null);
function openEdit(image) {
  image.caption ??= { de: null, en: null };
  editItem.value = image;
}

// Cropper overlay
const cropItem = ref(null);
const isCropperLoading = ref(false);
const cropSrc = ref(null);
const cropRatio = ref(null);
const coords = reactive({ w: 0, h: 0, x: 0, y: 0 });
const cropSize = reactive({ w: null, h: null });

// Where the crop box starts for an image without coords
const cropDefaults = { w: 1600, h: 1000, x: 100, y: 100 };

async function openCropper(image) {
  cropItem.value = image;
  cropRatio.value = props.ratio(image);
  isCropperLoading.value = true;
  try {
    cropSrc.value = await preloadImage(imageUrl(image, 'original'));
  }
  finally {
    isCropperLoading.value = false;
  }
}

function change({ coordinates }) {
  coords.w = coordinates.width;
  coords.h = coordinates.height;
  coords.x = coordinates.left;
  coords.y = coordinates.top;
  cropSize.w = Math.floor(coordinates.width);
  cropSize.h = Math.floor(coordinates.height);
}

function defaultPosition() {
  return {
    left: cropItem.value.coords_x || cropDefaults.x,
    top: cropItem.value.coords_y || cropDefaults.y,
  };
}

function defaultSize() {
  return {
    width: cropItem.value.coords_w || cropDefaults.w,
    height: cropItem.value.coords_h || cropDefaults.h,
  };
}

function saveCrop() {
  const image = cropItem.value;
  image.coords_w = coords.w;
  image.coords_h = coords.h;
  image.coords_x = coords.x;
  image.coords_y = coords.y;
  emit('save-coords', image);
  cropItem.value = null;
}

useEscape(() => {
  editItem.value = null;
  cropItem.value = null;
});
</script>
