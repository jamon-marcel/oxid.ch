<template></template>
<script>
    import store from '@/store';

    export default {
        mounted () {
            this.axios.post('/api/auth/logout').catch(() => {}).then(() => {
                store.isLoggedIn = false;
                this.$router.push({ name: 'login' });
            });
        }
    }
</script>
