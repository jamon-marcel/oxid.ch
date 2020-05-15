<template>
  <div>
    <div class="upload-listing is-files">
      <div>
        <figure
          :class="[file.publish == 0 ? 'is-disabled' : '', 'upload-item is-file']"
          v-for="file in files"
          :key="file.id"
        >
          <a :href="`/storage/uploads/${file.name}`" target="_blank">
            <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
          </a>
          <div class="upload__actions">
            <file-actions :file="file" :publish="file.publish"></file-actions>
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
            <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
            <figcaption v-if="overlayItem.caption.de || overlayItem.caption.en">
              <span v-if="overlayItem.caption.de">{{overlayItem.caption.de}}</span>
              <span v-if="overlayItem.caption.en">{{overlayItem.caption.en}}</span>
            </figcaption>
          </figure>
        </div>
        <div>
          <div class="form-row">
            <label>Datei:</label>
            <span>{{overlayItem.name}}</span>
          </div>
          <div class="form-row">
            <form-text 
              v-model="overlayItem.caption.de"
              :label="'Legende'"
            ></form-text>
          </div>
          <div class="form-row">
            <form-text 
              v-model="overlayItem.caption.en"
              :label="'Legende (en)'"
            ></form-text>
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
  </div>
</template>
<script>

// Actions
import FileActions from "@/components/global/files/Actions.vue";

// Form elements
import FormText from "@/components/global/input/Text.vue"

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

export default {
  components: {
    FileActions,
    FormText,
  },

  props: {
    files: Array,

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
      hasOverlayEdit: false,

      overlayItem: {
        name: '',
        caption: { de: null, en: null }
      },

      defaults: {
        item: {
          name: '',
          caption: { de: null, en: null}
        }
      }
    };
  },

  mounted() {
    window.addEventListener("keyup", event => {
      if (event.which === 27) {
        this.hideEdit();
      }
    });
  },

  methods: {
    toggle(file, $event) {
      this.$parent.toggleFile(file, $event)
    },

    destroy(file, $event) {
      this.$parent.destroyFile(file, $event)
    },

    update(file, $event) {
      this.$parent.updateFile(file, $event)
      this.hideEdit();
    },

    showEdit(file) {
      this.hasOverlayEdit = true;
      this.overlayItem = file;
    },

    hideEdit() {
      this.hasOverlayEdit = false;
      this.overlayItem = this.defaults.item;
    },
  }
};
</script>