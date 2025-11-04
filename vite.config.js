import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: 'localhost', // más estable con Laragon
        port: 5173,
        strictPort: true,
        https: false,
        watch: { usePolling: true },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true, // fuerza reload completo
        }),

        // 👇 Este plugin hace que el navegador se recargue
        // cuando guardas cualquier archivo .blade.php
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        }
    ],
});
