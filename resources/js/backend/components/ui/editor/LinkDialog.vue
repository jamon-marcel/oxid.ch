<template>
  <AppDialog :open="isOpen" title="Link" size="md" :dismissible="false" @close="onClose()">
    <form :id="formId" class="flex flex-col gap-16" @submit.prevent="apply()">
      <FormGroup>
        <FormLabel :for="`${formId}-type`">Typ</FormLabel>
        <FormSelect :id="`${formId}-type`" v-model="link.type" :options="typeOptions" />
      </FormGroup>

      <FormGroup v-if="link.type === 'url'">
        <FormLabel :for="`${formId}-value`">URL</FormLabel>
        <div class="flex gap-8">
          <div class="w-110 shrink-0">
            <FormSelect :id="`${formId}-protocol`" v-model="link.protocol" :options="protocolOptions" />
          </div>
          <FormInput :id="`${formId}-value`" v-model="link.value" class="min-w-0 flex-1" placeholder="www.example.com" />
        </div>
      </FormGroup>

      <FormGroup v-else>
        <FormLabel :for="`${formId}-value`">{{ link.type === 'email' ? 'E-Mail-Adresse' : 'Telefonnummer' }}</FormLabel>
        <FormInput :id="`${formId}-value`" v-model="link.value" :placeholder="link.type === 'email' ? 'info@oxid-architektur.ch' : '+41 44 000 00 00'" />
      </FormGroup>

      <FormGroup>
        <FormLabel :for="`${formId}-title`">Titel (optional)</FormLabel>
        <FormInput :id="`${formId}-title`" v-model="link.title" />
      </FormGroup>

      <FormCheckbox v-if="link.type === 'url'" :id="`${formId}-blank`" v-model="blank">In neuem Fenster öffnen</FormCheckbox>
    </form>
    <template #footer>
      <div class="flex items-center gap-16">
        <button v-if="isEditing" type="button" class="mr-auto cursor-pointer text-xs text-gray-400 transition-colors hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100" @click="remove()">Link entfernen</button>
        <div class="ml-auto flex gap-16">
          <FormButton variant="secondary" @click="close()">Abbrechen</FormButton>
          <FormButton type="submit" :form="formId">Übernehmen</FormButton>
        </div>
      </div>
    </template>
  </AppDialog>
</template>
<script setup>
import { ref, reactive, computed } from 'vue';
import AppDialog from '@/components/ui/dialog/AppDialog.vue';
import FormGroup from '@/components/ui/form/FormGroup.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormInput from '@/components/ui/form/FormInput.vue';
import FormSelect from '@/components/ui/form/FormSelect.vue';
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue';
import FormButton from '@/components/ui/form/FormButton.vue';

const props = defineProps({
  editor: { type: Object, required: true },
});

const typeOptions = [
  { value: 'url', label: 'URL' },
  { value: 'email', label: 'E-Mail' },
  { value: 'tel', label: 'Telefon' },
];

const protocolOptions = [
  { value: 'https://', label: 'https://' },
  { value: 'http://', label: 'http://' },
];

const isOpen = ref(false);

// Several editors on a page: each link form needs its own id
const formId = `link-${Math.random().toString(36).slice(2)}`;
const isEditing = ref(false);
const link = reactive({ type: 'url', protocol: 'https://', value: '', title: '', blank: false });

// FormCheckbox stores 1/0
const blank = computed({
  get: () => link.blank ? 1 : 0,
  set: value => link.blank = value === 1,
});

// Fill the form from an existing link's href. Relative links (/projekt/…)
// stay as they are: the protocol select is skipped for them.
function parse(href) {
  if (href.startsWith('mailto:')) {
    return { type: 'email', value: href.slice(7) };
  }
  if (href.startsWith('tel:')) {
    return { type: 'tel', value: href.slice(4) };
  }
  const match = href.match(/^(https?:\/\/)(.*)$/);
  return { type: 'url', protocol: match?.[1] ?? 'https://', value: match?.[2] ?? href };
}

function open() {
  const attributes = props.editor.getAttributes('link');
  isEditing.value = !!attributes.href;
  Object.assign(link, { type: 'url', protocol: 'https://', value: '', title: '', blank: false });

  if (attributes.href) {
    Object.assign(link, parse(attributes.href), {
      title: attributes.title ?? '',
      blank: attributes.target === '_blank',
    });
  }

  isOpen.value = true;
}

function href() {
  const value = link.value.trim();
  if (!value) {
    return null;
  }
  switch (link.type) {
    case 'email': return `mailto:${value}`;
    case 'tel': return `tel:${value.replace(/\s+/g, '')}`;
    default: return /^(https?:\/\/|\/|#|\.\.?\/)/.test(value) ? value : link.protocol + value;
  }
}

function apply() {
  const url = href();
  if (!url) {
    return;
  }
  const blank = link.blank && link.type === 'url';
  props.editor.chain().focus().extendMarkRange('link').setLink({
    href: url,
    title: link.title.trim() || null,
    target: blank ? '_blank' : null,
    rel: blank ? 'noopener' : null,
  }).run();
  close();
}

function remove() {
  props.editor.chain().focus().extendMarkRange('link').unsetLink().run();
  close();
}

function close() {
  isOpen.value = false;
}

// Also fired when the dialog is torn down with an already destroyed editor
function onClose() {
  isOpen.value = false;
  if (!props.editor.isDestroyed) {
    props.editor.commands.focus();
  }
}

defineExpose({ open });
</script>
