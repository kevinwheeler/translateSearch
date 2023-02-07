import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/googleStuff.js', 'resources/js/helpers.js', 'resources/js/homepage.js', 'resources/js/resultsPageRecaptcha.js', 'resources/js/resultsGET.js'],
            refresh: true,
        }),
    ],
    server: {
      hmr: {
        host: 'localhost',
      }
    }
});
