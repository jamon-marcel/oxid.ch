import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command }) => ({
  plugins: [
    laravel({
      input: [
        'resources/sass/frontend/app.scss',
        'resources/js/frontend/app.js',
        'resources/js/frontend/maps.js',
      ],
      refresh: true,
    }),
  ],
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
