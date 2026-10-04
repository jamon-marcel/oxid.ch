<template>
  <div>
    <page-header />
    <profile-form type="edit"></profile-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import ProfileForm from '@/components/profile/text/Form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      ProfileForm: ProfileForm
    }
  }
</script>