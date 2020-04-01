<template>
  <div>
    <page-header />
    <project-form type="create"></project-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import ProjectForm from '@/components/projects/form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      ProjectForm: ProjectForm
    }
  }
</script>