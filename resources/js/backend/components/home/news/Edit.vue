<template>
  <div>
    <page-header />
    <news-form type="edit"></news-form>
  </div>
</template>
<script>
import PageHeader from '@/layout/PageHeader.vue';
import newsForm from '@/components/home/news/Form.vue';
  export default {
    components: {
      PageHeader: PageHeader,
      newsForm: newsForm
    }
  }
</script>