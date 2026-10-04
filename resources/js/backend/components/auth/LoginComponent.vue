<template>
    <div class="container-auth">
        <main class="content-auth" role="main">
            <header class="auth-header">
                <h1>Login</h1>
            </header>
            <form method="POST" class="login" v-on:submit.prevent="submitLogin">
                <div class="form-row" :class="{ 'has-error': loginError }">
                    <label>E-Mail</label>
                    <input type="email" required autofocus autocomplete="email" v-model="email">
                </div>
                <div class="form-row" :class="{ 'has-error': loginError }">
                    <label>Password</label>
                    <input id="password" type="password" required v-model="password">
                </div>
                <div class="form-row is-error" v-if="loginError">
                    E-Mail oder Passwort ist falsch.
                </div>
                <div class="form-row is-last">
                    <div class="form-buttons">
                        <input type="submit" class="btn" value="Login">
                    </div>
                </div>
            </form>
        </main>
    </div>
</template>
<script>
    import store from '@/store';

    export default {
        data() {
            return {
                email: '',
                password: '',
                loginError: false,
            }
        },
        methods: {
            submitLogin() {
                this.loginError = false;
                this.axios.get('/sanctum/csrf-cookie').then(() => {
                    return this.axios.post('/api/auth/login', {
                        email: this.email,
                        password: this.password
                    });
                }).then(() => {
                    // Session cookie is set; reload so the page gets the new CSRF token
                    store.isLoggedIn = true;
                    window.location.href = '/admin/';
                }).catch(() => {
                    this.loginError = true;
                });
            }
        }
    }
</script>
