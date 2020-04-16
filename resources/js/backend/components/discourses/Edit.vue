<template>
  <div>
    <page-header />
    <discourse-form type="edit"></discourse-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import DiscourseForm from '@/components/discourses/form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      DiscourseForm: DiscourseForm
    }
  }
</script>