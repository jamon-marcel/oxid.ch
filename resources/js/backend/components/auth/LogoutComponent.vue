<template></template>
<script>
    import store from '../../store'
    export default {
        mounted () {
            axios.post('/api/auth/logout').catch(() => {}).then(() => {
                store.commit('logoutUser')
                this.$router.push({ name: 'login' })
            })
        }
    }
</script>