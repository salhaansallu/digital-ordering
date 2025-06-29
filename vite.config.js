import { defineConfig } from 'vite';
import vue from "@vitejs/plugin-vue";
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/views/pos/sass/pos.scss',
                'resources/views/pos/sass/app.scss',
                'resources/js/app.js',
                'resources/views/pos/js/pos.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
          'vue': 'vue/dist/vue.esm-bundler',
        },
    },
});
