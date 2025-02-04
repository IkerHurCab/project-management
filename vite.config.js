import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({ 
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    isCustomElement: (tag) => ['box-icon'].includes(tag),
                }
            },
        }),
    ],

    resolve: { 
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },

    server: {
        host: process.env.VITE_HOST || "0.0.0.0",
        port: parseInt(process.env.VITE_PORT),
        hmr: {
            host: process.env.VITE_HMR_HOST || "localhost",
            protocol: process.env.VITE_HMR_PROTOCOL || "ws",
        },
        cors: {
            origin: process.env.VITE_CORS_ORIGIN || '*',
        },
    },
});
