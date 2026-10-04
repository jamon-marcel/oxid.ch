<template>
  <div>
    <job-form type="create"></job-form>
  </div>
</template>
<script>
import JobForm from '@/components/jobs/Form.vue';
  export default {
    components: {
      JobForm: JobForm
    }
  }
</script>