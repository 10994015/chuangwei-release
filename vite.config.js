import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/components.css', 'resources/js/app.js', 'resources/js/carousel-element.js', 'resources/js/map-element.js'],
            refresh: true,
        }),
    ],
});
