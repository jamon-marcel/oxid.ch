<template>
  <Lightbox :open="isOpen" title="Link" size="small" @close="onClose()">
    <form :id="formId" @submit.prevent="apply()">
      <div class="form-row">
        <label>Typ</label>
        <div class="select-wrapper">
          <select v-model="link.type">
            <option v-for="(label, type) in types" :key="type" :value="type">{{ label }}</option>
          </select>
        </div>
      </div>

      <div class="form-row" v-if="link.type === 'url'">
        <label>URL</label>
        <div class="editor-dialog__url">
          <div class="select-wrapper">
            <select v-model="link.protocol">
              <option value="https://">https://</option>
              <option value="http://">http://</option>
            </select>
          </div>
          <input type="text" v-model="link.value" placeholder="www.example.com">
        </div>
      </div>

      <div class="form-row" v-if="link.type === 'email'">
        <label>E-Mail-Adresse</label>
        <input type="text" v-model="link.value" placeholder="info@oxid-architektur.ch">
      </div>

      <div class="form-row" v-if="link.type === 'tel'">
        <label>Telefonnummer</label>
        <input type="text" v-model="link.value" placeholder="+41 44 000 00 00">
      </div>

      <div class="form-row">
        <label>Titel (optional)</label>
        <input type="text" v-model="link.title">
      </div>

      <div class="form-row" v-if="link.type === 'url'">
        <label class="editor-dialog__checkbox">
          <input type="checkbox" v-model="link.blank">
          <span>In neuem Fenster öffnen</span>
        </label>
      </div>
    </form>
    <template #footer>
      <button type="submit" class="btn-secondary" :form="formId">Übernehmen</button>
      <a href="javascript:;" v-if="isEditing" @click.prevent="remove()">Entfernen</a>
      <a href="javascript:;" @click.prevent="close()">Abbrechen</a>
    </template>
  </Lightbox>
</template>
<script setup>
import { ref, reactive } from 'vue';
import Lightbox from '@/components/ui/Lightbox.vue';

const props = defineProps({
  editor: { type: Object, required: true },
});

const types = { url: 'URL', email: 'E-Mail', tel: 'Telefon' };

const isOpen = ref(false);

// Several editors on a page: each link form needs its own id
const formId = `link-${Math.random().toString(36).slice(2)}`;
const isEditing = ref(false);
const link = reactive({ type: 'url', protocol: 'https://', value: '', title: '', blank: false });

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
