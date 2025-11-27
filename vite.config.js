import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
        VitePWA({
            registerType: 'autoUpdate',
            outDir: 'public/build',
            manifest: {
                name: 'BudgetBoss',
                short_name: 'BudgetBoss',
                theme_color: '#1f2937', // Cor escura para combinar com o tema
                background_color: '#111827',
                display: 'standalone',
                orientation: 'portrait',
                scope: '/',
                start_url: '/',
                icons: [
                    {
                        src: '/images/icon-192x192.png', // Você precisará criar essas imagens na pasta public/images
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/images/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                ],
            },
        }),
    ],
});
