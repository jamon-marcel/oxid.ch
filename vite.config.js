import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig(({ command }) => ({
  plugins: [
    laravel({
      input: [
        'resources/sass/frontend/app.scss',
        'resources/js/frontend/app.js',
        'resources/js/frontend/maps.js',
        'resources/sass/backend/app.scss',
        'resources/js/backend/app.js',
      ],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          // Keep absolute urls (/assets/...) as they are; don't import them
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: [
      { find: '@', replacement: fileURLToPath(new URL('./resources/js/backend', import.meta.url)) },
      // vuedraggable's UMD build require()s 'vue', which would pull in Vue's
      // CommonJS build and the template compiler with it
      { find: /^vue$/, replacement: 'vue/dist/vue.runtime.esm-bundler.js' },
    ],
  },
  // Dev only: serve public/ so the Sass's absolute /assets/... URLs resolve
  // on the Vite server. In a build, publicDir would prefix them with /build/.
  publicDir: command === 'serve' ? 'public' : false,
  css: {
    // Drop old-IE hacks (`*zoom: 1`) the minifier would otherwise reject;
    // browsers ignore them anyway
    lightningcss: { errorRecovery: true },
    preprocessorOptions: {
      scss: {
        // 12k lines of Sass still use @import; moving to @use is its own job
        silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'mixed-decls'],
      },
    },
  },
}));
