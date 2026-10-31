import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Sceny do nagrania filmów z hero (tylko /studio/*, środowisko local).
                'resources/css/studio.css',
                'resources/js/studio.js',
                'resources/js/studio-whatsapp.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
