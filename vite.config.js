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
            },
        }),
    ],

    resolve: { 
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },

    server: {
        host: '0.0.0.0', // Permitir conexiones externas
        port: 5189, // Puerto que deseas usar
        hmr: {
            host: '10.40.1.54', // IP de tu servidor
            protocol: 'ws',
        },
        cors: {
            origin: '*',
        },
    },
    
    
});
