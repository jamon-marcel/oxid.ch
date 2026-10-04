<template>
  <div>
    <div class="ratio-boxes">
      <div v-if="layout == '1-1'">
        <div class="box-1-1">
          <div class="box__b">
            <div v-if="elements[0] && elements[0].position == '0'">
              <GridMedia :element="elements[0]" @destroy="deleteImage" />
            </div>
            <div v-else>
              <div class="box-buttons">
                <ButtonAdd @pick="pick(0)" />
              </div>
            </div>
          </div>
          <div class="box__b">
            <div v-if="elements[1] && elements[1].position == '1'">
              <GridMedia :element="elements[1]" @destroy="deleteImage" />
            </div>
            <div v-else>
              <div class="box-buttons">
                <ButtonAdd @pick="pick(1)" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="layout == '1'">
        <div class="box-1">
          <div class="box__c">
            <div v-if="elements[0] && elements[0].position == '0'">
              <GridMedia :element="elements[0]" @destroy="deleteImage" />
            </div>
            <div v-else>
              <div class="box-buttons">
                <ButtonAdd @pick="pick(0)" />
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
                <GridMedia :element="elements[0]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(0)" />
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[1] && elements[1].position == '1'">
                <GridMedia :element="elements[1]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(1)" />
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="box__b">
              <div v-if="elements[2] && elements[2].position == '2'">
                <GridMedia :element="elements[2]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(2)" />
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
                <GridMedia :element="elements[0]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(0)" />
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="box__a">
              <div v-if="elements[1] && elements[1].position == '1'">
                <GridMedia :element="elements[1]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(1)" />
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[2] && elements[2].position == '2'">
                <GridMedia :element="elements[2]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(2)" />
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
                <GridMedia :element="elements[0]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(0)" />
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[2] && elements[2].position == '2'">
                <GridMedia :element="elements[2]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(2)" />
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="box__a">
              <div v-if="elements[1] && elements[1].position == '1'">
                <GridMedia :element="elements[1]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(1)" />
                </div>
              </div>
            </div>
            <div class="box__a">
              <div v-if="elements[3] && elements[3].position == '3'">
                <GridMedia :element="elements[3]" @destroy="deleteImage" />
              </div>
              <div v-else>
                <div class="box-buttons">
                  <ButtonAdd @pick="pick(3)" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div :class="[isOverlayOpen ? 'is-visible' : '', 'overlay']">
      <div>
        <a href="javascript:;" @click.prevent="closeOverlay()" class="feather-icon upload-overlay__close" title="Schliessen">
          <PhX :size="24" weight="light" />
        </a>
        <div v-if="isOverlayOpen">
          <h1>Projektbild auswählen</h1>
          <div class="grid-image-selector">
            <div class="grid-image-selector__item">
              <div class="grid-image-selector__media">
                <figure v-for="image in images" :key="image.id">
                  <a href @click.prevent="storeImage(image.id)">
                    <img :src="imageUrl(image, 'thumbnail')" height="120" width="70">
                    <span>{{image.name}}</span>
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
<script setup>
import { ref } from 'vue';
import { notify } from '@kyvg/vue3-notification';
import { PhX } from '@phosphor-icons/vue';
import GridMedia from '@/components/grid/Media.vue';
import ButtonAdd from '@/components/grid/ButtonAdd.vue';
import { imageUrl } from '@/lib/images';
import http from '@/lib/http';

// One row of the project layout: its boxes, filled with project images
const props = defineProps({
  layout: { type: String, required: true },
  gridId: { type: Number, required: true },
  projectId: { type: Number, required: true },
});

// Images by position (sparse: empty boxes have none)
const elements = ref([]);

async function fetch() {
  const { data } = await http.get(`/api/project/grid/images/${props.gridId}`);
  const byPosition = [];
  (data.data ?? []).forEach(element => {
    if (element.project_image_id) {
      byPosition[element.position] = {
        id: element.id,
        position: element.position,
        image: element.image.name,
        coords: element.image.coords,
        caption: element.image.caption?.de ?? null,
      };
    }
  });
  elements.value = byPosition;
}

// Image picker for an empty box
const isOverlayOpen = ref(false);
const images = ref([]);
let position = null;

async function pick(at) {
  const { data } = await http.get(`/api/project/image/get/${props.projectId}`);
  images.value = data.data;
  position = at;
  openOverlay();
}

async function storeImage(imageId) {
  await http.post('/api/project/grid/image/store', {
    position,
    grid_id: props.gridId,
    project_image_id: imageId,
    project_id: props.projectId,
  });
  closeOverlay();
  notify({ type: 'success', text: 'Bild hinzugefügt' });
  fetch();
}

async function deleteImage(id) {
  await http.delete(`/api/project/grid/image/delete/${id}`);
  notify({ type: 'success', text: 'Bild gelöscht' });
  fetch();
}

function openOverlay() {
  document.documentElement.classList.add('has-overlay');
  isOverlayOpen.value = true;
}

function closeOverlay() {
  document.documentElement.classList.remove('has-overlay');
  isOverlayOpen.value = false;
}

fetch();
</script>
