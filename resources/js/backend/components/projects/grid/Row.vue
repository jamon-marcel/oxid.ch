<template>
  <div>
    <div class="ratio-boxes">
      <div v-if="layout == '1-1'">
        <div class="box-1-1">
          <div class="box__b">
            <div v-if="elements[0] && elements[0].position == '0'">
              <grid-media :ratioW="43" :ratioH="56" :element="elements[0]"></grid-media>
            </div>
            <div v-else>
              <div class="box-buttons">
                <button-add :gridId="gridId" :gridPosition="0"></button-add>
              </div>
            </div>
          </div>
          <div class="box__b">
            <div v-if="elements[1] && elements[1].position == '1'">
              <grid-media :ratioW="43" :ratioH="56" :element="elements[1]"></grid-media>
            </div>
            <div v-else>
              <div class="box-buttons">
                <button-add :gridId="gridId" :gridPosition="1"></button-add>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="layout == '1'">
        <div class="box-1">
          <div class="box__c">
            <div v-if="elements[0] && elements[0].position == '0'">
              <grid-media :ratioW="86" :ratioH="56" :element="elements[0]"></grid-media>
            </div>
            <div v-else>
              <div class="box-buttons">
                <button-add :gridId="gridId" :gridPosition="0"></button-add>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="layout == '2-1'">
        <div class="box-2-1">
          <div>
            <div class="box__a">
              <div v-if="elements[0] && elements[0].position == '0'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[0]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="0"></button-add>
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[1] && elements[1].position == '1'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[1]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="1"></button-add>
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="box__b">
              <div v-if="elements[2] && elements[2].position == '2'">
                <grid-media :ratioW="43" :ratioH="56" :element="elements[2]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="2"></button-add>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="layout == '1-2'">
        <div class="box-1-2">
          <div>
            <div class="box__b">
              <div v-if="elements[0] && elements[0].position == '0'">
                <grid-media :ratioW="43" :ratioH="56" :element="elements[0]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="0"></button-add>
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="box__a">
              <div v-if="elements[1] && elements[1].position == '1'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[1]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="1"></button-add>
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[2] && elements[2].position == '2'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[2]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="2"></button-add>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="layout == '2-2'">
        <div class="box-2-2">
          <div>
            <div class="box__a">
              <div v-if="elements[0] && elements[0].position == '0'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[0]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="0"></button-add>
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[1] && elements[1].position == '1'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[1]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="1"></button-add>
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="box__a">
              <div v-if="elements[2] && elements[2].position == '2'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[2]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="2"></button-add>
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[3] && elements[3].position == '3'">
                <grid-media :ratioW="43" :ratioH="28" :element="elements[3]"></grid-media>
              </div>
              <div v-else>
                <div class="box-buttons">
                  <button-add :gridId="gridId" :gridPosition="3"></button-add>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div :class="[hasOverlay ? 'is-visible': '', 'overlay']">
      <a href="javascript:;" @click.prevent="toggleOverlay()" class="icon-close-overlay"></a>
      <div>
        <div v-if="showMedia">
          <h1>Projektbild auswählen</h1>
          <div class="grid-image-selector">
            <div class="grid-image-selector__item">
              <div class="grid-image-selector__media">
                <figure v-for="image in images" :key="image.id">
                  <a href @click.prevent="storeImage(image.id)">
                    <img :src="getSource(image.name, 'tiny')" height="120" width="70">
                  </a>
                </figure>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import GridMedia from "@/components/projects/grid/Media.vue";
import ButtonAdd from "@/components/projects/grid/ButtonAdd.vue";
import Utils from "@/mixins/utils";

export default {
  components: {
    ButtonAdd: ButtonAdd,
    GridMedia: GridMedia
  },

  mixins: [Utils],

  data() {
    return {
      hasOverlay: false,
      showMedia: false,
      tmpGridId: null,
      tmpPosition: null,

      // Project images
      images: [],

      // Grid elements
      elements: []
    };
  },

  props: {
    layout: String,
    gridId: Number,
    projectId: Number
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      let uri = `/api/project/grid/images/${this.$props.gridId}`;

      this.axios.get(uri).then(response => {
        let els = [];
        if (response.data.data) {
          response.data.data.forEach(e => {
            if (e.project_image_id) {
              let img = e.image;
              let el = {
                id: e.id,
                position: e.position,
                projectId: img.project.id || null,
                image: img.name || null,
                imageId: img.id,
                caption: `${img.caption.de}` || null
              };
              els[e.position] = el;
            }
          });

          if (els.length > 0) {
            this.elements = els;
          } else if (els.length == 0) {
            this.elements = [];
          }
        }
      });
    },

    getImages(gridId, position) {
      let uri = `/api/project/image/get/${this.$props.projectId}`;
      this.axios.get(uri).then(response => {
        this.images = response.data.data;
        this.toggleOverlay();
        this.showMedia = true;
        this.tmpGridId = gridId;
        this.tmpPosition = position;
      });
    },

    storeImage(imageId) {
      let data = {
        position: this.tmpPosition,
        grid_id: this.tmpGridId,
        project_image_id: imageId,
        project_id: this.$props.projectId
      };

      let uri = "/api/project/grid/image/store";
      this.axios.post(uri, data).then(response => {
        this.showMedia = false;
        this.toggleOverlay();
        this.$notify({ type: "success", text: "Bild hinzugefügt" });
        this.fetch();
      });
    },

    deleteImage(id, btn) {
      let uri = `/api/project/grid/image/delete/${id}`;
      btn.classList.add("is-loading");
      this.axios.delete(uri).then(response => {
        btn.classList.remove("is-loading");
        this.$notify({ type: "success", text: "Bild gelöscht" });
        this.fetch();
      });
    },

    toggleOverlay() {
      let html = document.querySelector("html");
      html.classList.toggle("has-overlay");
      this.hasOverlay = this.hasOverlay ? false : true;
    }
  }
};
</script>