<template>
  <div>
    <page-header />
    <contact-form type="create"></contact-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import ContactForm from '@/components/contact/Form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      ContactForm: ContactForm
    }
  }
</script>