<template>
  <div>
    <page-header />
    <job-form type="edit"></job-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import JobForm from '@/components/jobs/form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      JobForm: JobForm
    }
  }
</script>