<template>
  <div>
    <page-header />
    <team-form type="create"></team-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import TeamForm from '@/components/team/team/form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      TeamForm: TeamForm
    }
  }
</script>