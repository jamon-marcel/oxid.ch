<template>
  <div>
    <page-header />
    <contact-form type="edit"></contact-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import ContactForm from '@/components/contact/form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      ContactForm: ContactForm
    }
  }
</script>