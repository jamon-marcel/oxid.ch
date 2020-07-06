<template>
  <div>
    <div class="upload-listing">
      <div>
        <figure
          :class="[image.publish == 0 ? 'is-disabled' : '', 'upload-item']"
          v-for="image in images"
          :key="image.id"
        >
          <a :href="getSource(image.name, 'large')" target="_blank" class="upload__preview">
            <img :src="getSource(image.name, 'thumbnail')" height="300" width="300">
          </a>
          <div class="upload__actions">
            <image-actions :image="image" :publish="image.publish" :grid="image.is_grid"></image-actions>
          </div>
        </figure>
      </div>
    </div>
    <div :class="[hasOverlayEdit ? 'is-visible' : '', 'upload-overlay-edit']">
      <div>
        <a
          href="javascript:;"
          class="icon-close-overlay"
          @click.prevent="hideEdit()"
        ></a>
        <div>
          <figure v-if="hasOverlayEdit">
            <img :src="getSource(overlayItem.name, 'large')" height="300" width="300">
            <figcaption v-if="overlayItem.caption.de || overlayItem.caption.en">
              <span v-if="overlayItem.caption.de">{{overlayItem.caption.de}}</span>
              <span v-if="overlayItem.caption.en">{{overlayItem.caption.en}}</span>
            </figcaption>
          </figure>
        </div>
        <div>
          <div class="form-row">
            <form-text 
              v-model="overlayItem.caption.de"
              :label="'Bildlegende'"
            ></form-text>
          </div>
          <div class="form-row">
            <form-text 
              v-model="overlayItem.caption.en"
              :label="'Bildlegende (en)'"
            ></form-text>
          </div>
          <div class="form-row">
            <label>Vorschaubild für:</label>
            <input
              type="checkbox"
              class="visually-hidden"
              v-model="overlayItem.is_preview_navigation"
              name="is_preview_navigation"
              id="is_preview_navigation"
            >
            <label for="is_preview_navigation" class="form-control is-auto">Navigation</label>
            <input
              type="checkbox"
              class="visually-hidden"
              v-model="overlayItem.is_preview_works"
              name="is_preview_works"
              id="is_preview_works"
            >
            <label for="is_preview_works" class="form-control is-auto">Werkliste</label>
          </div>
          <div class="form-row">
            <label class="is-sm">Plan?</label>
            <div class="form-radio">
              <input
                v-model="overlayItem.is_plan"
                type="radio"
                name="is_plan_1"
                id="is_plan_1"
                value="1"
                class="visually-hidden"
              >
              <label for="is_plan_1" class="form-control">Ja</label>
              <input
                v-model="overlayItem.is_plan"
                type="radio"
                name="is_plan_0"
                id="is_plan_0"
                value="0"
                class="visually-hidden"
              >
              <label for="is_plan_0" class="form-control">Nein</label>
            </div>
          </div>
          <div class="form-row-button">
            <a
              href="javascript:;"
              class="btn-secondary"
              @click.prevent="hideEdit()"
            >Schliessen</a>
          </div>
        </div>
      </div>
    </div>

    <div :class="[hasOverlayCropper ? 'is-visible' : '', 'upload-overlay-cropper']">
      <div class="loader" v-if="isLoading">Bild wird geladen...</div>
      <div :class="'is-' + overlayItem.orientation" v-if="!isLoading">
        <a
          href="javascript:;"
          class="icon-close-overlay"
          @click.prevent="hideCropper()"
        ></a>
        <div>
          <span class="cropper-info">Neue Grösse:<br>{{ cropW }} x {{ cropH }}px</span>
          <cropper
            :src="cropImage"
            :defaultPosition="defaultPosition"
            :defaultSize="defaultSize"
            :stencilProps="{
              aspectRatio: this.ratio.w/this.ratio.h,
              linesClassnames: {
                default: 'line',
              },
              handlersClassnames: {
                default: 'handler'
              }
            }"
            @change="change"
          ></cropper>
          <div class="form-buttons">
            <a
              href="javascript:;"
              class="btn-secondary"
              @click.prevent="saveCoords(overlayItem)"
            >Speichern</a>
            <a href @click.prevent="hideCropper()">Abbrechen</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

// Global mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// Image mixin
import ImageCrop from "@/mixins/images/crop";
import ImageList from "@/mixins/images/listing";

// Action bar
import ImageActions from "@/components/global/images/Actions.vue";

// Form elements
import FormText from "@/components/global/input/Text.vue"

// Cropper
import { Cropper } from "vue-advanced-cropper";

export default {
  components: {
    ImageActions,
    FormText,
    Cropper,
  },

  props: {
    images: Array,
  },

  mixins: [Utils, Progress, ImageCrop, ImageList],

  data() {
    return {

      overlayItem: {
        name: '',
        caption: { de: null, en: null },
        is_preview_navigation: 0,
        is_preview_works: 0,
        is_plan: 0,
        is_grid: 0,
        orientation: null,
      },

      defaults: {
        item: {
          name: '',
          caption: { de: null, en: null},
          is_preview_navigation: 0,
          is_preview_works: 0,
          is_plan: 0,
          is_grid: 0,
          orientation: null,
        }
      },

      isLoading: false,

      ratio: {
        w: null,
        h: null,
      },
    };
  },

  methods: {
    showCropper(image) {
      this.hasOverlayCropper = true;
      this.overlayItem = image;
      this.isLoading = true;

      if (image.orientation) {

        if (image.orientation == 'l') {
          this.ratio.w = 16;
          this.ratio.h = 10;

          if (image.is_plan) {
            this.ratio.w = 16;
            this.ratio.h = 10.66666;
          }
        }

        if (image.orientation == 'p') {
          this.ratio.w = 12;
          this.ratio.h = 16;
        }
      }

      this.axios.get(this.getSource(image.name, "original")).then(response => {
        this.cropImage = response.config.url;
        this.isLoading = false;
      });
    },
  }
};
</script>