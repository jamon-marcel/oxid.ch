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
    <div :class="[hasOverlay ? 'is-visible' : '', 'upload-overlay']">
      <div>
        <a
          href="javascript:;"
          class="icon-close-overlay"
          @click.prevent="hideOverlay()"
        ></a>
        <div>
          <figure v-if="hasOverlay">
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
              @click.prevent="hideOverlay()"
            >Schliessen</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";
import ImageActions from "@/components/global/images/Actions.vue";
import FormText from "@/components/global/input/Text.vue"

export default {
  components: {
    ImageActions,
    FormText,
  },

  props: {
    images: Array,

    updateOnChange: {
      type: Boolean,
      default: false
    },

    categories: {
      type: Object,
      default: null,
    }
  },

  mixins: [Utils, Progress],

  data() {
    return {
      hasOverlay: false,

      overlayItem: {
        name: '',
        caption: { de: null, en: null },
        is_preview: 0,
      },

      defaults: {
        item: {
          name: '',
          caption: { de: null, en: null},
          is_preview: 0,
        }
      }
    };
  },

  mounted() {
    window.addEventListener("keyup", event => {
      if (event.which === 27) {
        this.hideOverlay();
      }
    });
  },

  methods: {
    toggle(image, $event) {
      this.$parent.toggleImage(image, $event)
    },

    destroy(image, $event) {
      this.$parent.destroyImage(image, $event)
    },

    update(image, $event) {
      this.$parent.updateImage(image, $event)
      this.hideOverlay();
    },

    showOverlay(image, $event) {
      this.hasOverlay = true;
      this.overlayItem = image;
    },

    hideOverlay() {
      this.hasOverlay = false;
      this.overlayItem = this.defaults.item;
    }
  }
};
</script>