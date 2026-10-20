<template>
  <div class="flex min-h-dvh">
    <!-- A random published home image (backend/app.blade.php sets it) -->
    <div class="container-auth relative hidden bg-gray-200 bg-cover bg-center lg:block lg:w-1/2">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="relative z-10 flex h-full items-end p-48 text-white">
        <OxidLogo class="w-160" />
      </div>
    </div>
    <div class="flex w-full items-center justify-center bg-white px-32 py-48 lg:w-1/2 dark:bg-warm-900">
      <div class="w-full max-w-sm">
        <div class="mb-32 w-96 text-gray-900 lg:hidden dark:text-warm-100">
          <OxidLogo />
        </div>
        <h1 class="mb-4 text-lg font-medium text-gray-900 dark:text-warm-100">Anmelden</h1>
        <p class="mb-24 text-sm text-gray-400 dark:text-warm-500">Melden Sie sich mit Ihrem Konto an.</p>
        <form method="POST" class="space-y-16" @submit.prevent="login">
          <FormGroup>
            <FormLabel for="email" :error="failed ? 'E-Mail oder Passwort ist falsch.' : null">E-Mail</FormLabel>
            <FormInput id="email" v-model="email" type="email" :has-error="failed" required autofocus autocomplete="username" />
          </FormGroup>
          <FormGroup>
            <FormLabel for="password">Passwort</FormLabel>
            <FormInput id="password" v-model="password" type="password" :has-error="failed" required autocomplete="current-password" />
          </FormGroup>
          <div class="mt-24 flex items-center justify-end">
            <FormButton type="submit" :disabled="isSending">Anmelden</FormButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import OxidLogo from '@/components/layout/OxidLogo.vue';
import FormGroup from '@/components/ui/form/FormGroup.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormInput from '@/components/ui/form/FormInput.vue';
import FormButton from '@/components/ui/form/FormButton.vue';
import http from '@/lib/http';

const email = ref('');
const password = ref('');
const failed = ref(false);
const isSending = ref(false);

async function login() {
  failed.value = false;
  isSending.value = true;
  try {
    await http.get('/sanctum/csrf-cookie');
    await http.post('/api/auth/login', { email: email.value, password: password.value });
    // Session cookie is set; reload so the page gets the new CSRF token
    window.location.href = '/admin/home/news';
  }
  catch {
    failed.value = true;
    isSending.value = false;
  }
}
</script>
