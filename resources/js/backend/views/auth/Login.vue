<template>
  <div class="container-auth">
    <main class="content-auth" role="main">
      <header class="auth-header">
        <h1>Login</h1>
      </header>
      <form method="POST" class="login" @submit.prevent="login">
        <div class="form-row" :class="{ 'has-error': failed }">
          <label>E-Mail</label>
          <input type="email" required autofocus autocomplete="email" v-model="email">
        </div>
        <div class="form-row" :class="{ 'has-error': failed }">
          <label>Password</label>
          <input id="password" type="password" required v-model="password">
        </div>
        <div class="form-row is-error" v-if="failed">
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
<script setup>
import { ref } from 'vue';
import http from '@/lib/http';

const email = ref('');
const password = ref('');
const failed = ref(false);

async function login() {
  failed.value = false;
  try {
    await http.get('/sanctum/csrf-cookie');
    await http.post('/api/auth/login', { email: email.value, password: password.value });
    // Session cookie is set; reload so the page gets the new CSRF token
    window.location.href = '/admin/';
  }
  catch {
    failed.value = true;
  }
}
</script>
