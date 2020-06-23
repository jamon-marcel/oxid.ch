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
            <image-actions :image="image" :publish="image.publish"></image-actions>
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
          <div class="form-row" v-if="categories">
            <label>Kategorie</label>
            <div class="select-wrapper">
              <select v-model="overlayItem.category" name="category">
                <option v-for="(c,i) in categories" :key="i" :value="i">{{ c }}</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <label>Vorschaubild?</label>
            <div class="form-radio">
              <input type="radio" class="visually-hidden" v-model="overlayItem.is_preview" value="1" id="is_preview_1">
              <label for="is_preview_1" class="form-control">Ja</label>
              <input type="radio" class="visually-hidden" v-model="overlayItem.is_preview" value="0" id="is_preview_0">
              <label for="is_preview_0" class="form-control">Nein</label>
            </div>
          </div>
          <div class="form-row">
            <label>Theme?</label>
            <div class="form-radio">
              <input type="radio" class="visually-hidden" v-model="overlayItem.theme" value="1" id="theme_1">
              <label for="theme_1" class="form-control">light</label>
              <input type="radio" class="visually-hidden" v-model="overlayItem.theme" value="0" id="theme_0">
              <label for="theme_0" class="form-control">dark</label>
            </div>
          </div>

          <div class="form-row-button" v-if="updateOnChange">
            <a
              href="javascript:;"
              class="btn-secondary"
              @click.prevent="update(overlayItem, $event)"
            >Speichern</a>
          </div>
          <div class="form-row-button" v-else>
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
      <div v-if="!isLoading">
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
              aspectRatio: this.$props.cropRatioW/this.$props.cropRatioH,
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

export default {
  components: {
    ImageActions,
    FormText,
  },

  props: {
    images: Array,

    cropRatioW: {
      type: Number,
      default: 16,
    },

    cropRatioH: {
      type: Number,
      default: 10,
    },

    updateOnChange: {
      type: Boolean,
      default: false
    },

    categories: {
      type: Object,
      default: null,
    }
  },

  mixins: [Utils, Progress, ImageCrop, ImageList],

  data() {
    return {

      overlayItem: {
        name: '',
        caption: { de: null, en: null },
        is_preview: 0,
        theme: 0,
      },

      defaults: {
        item: {
          name: '',
          caption: { de: null, en: null},
          is_preview: 0,
          theme: 0,
        }
      }
    };
  },
};
</script>