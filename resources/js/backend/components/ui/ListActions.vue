<template>
  <div class="list-item-action" :data-icons="count">
    <a v-if="hasToggle" href="javascript:;" class="feather-icon" :title="record.publish == 1 ? 'Verbergen' : 'Publizieren'" @click.prevent="emit('toggle', record.id)">
      <PhEye v-if="record.publish == 1" :size="18" weight="light" />
      <PhEyeSlash v-else :size="18" weight="light" class="is-off" />
    </a>
    <router-link v-if="editRoute" :to="{ name: editRoute, params: { id: record.id } }" class="feather-icon" title="Bearbeiten">
      <PhPencilSimple :size="18" weight="light" />
    </router-link>
    <a v-if="hasDestroy" href="javascript:;" class="feather-icon" title="Löschen" @click.prevent="emit('destroy', record.id)">
      <PhTrash :size="18" weight="light" />
    </a>
  </div>
</template>
<script setup>
import { computed } from 'vue';
import { PhEye, PhEyeSlash, PhPencilSimple, PhTrash } from '@phosphor-icons/vue';

const props = defineProps({
  record: { type: Object, required: true },
  editRoute: { type: String, default: null },
  hasToggle: { type: Boolean, default: true },
  hasDestroy: { type: Boolean, default: true },
});

const emit = defineEmits(['toggle', 'destroy']);

const count = computed(() => [props.hasToggle, props.editRoute, props.hasDestroy].filter(Boolean).length);
</script>
