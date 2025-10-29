import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';


export default defineConfig({
    server: {
        host: '0.0.0.0',   // This allows access from any IP
        port: 5173,        // Changed from 8000 to 5173
        strictPort: true,  // optional: lock sa 5173
        hmr: {
            host: '192.168.100.181',  // Use your real LAN IP here!
            protocol: 'ws'    // Use WebSocket protocol
        },
        cors: {
            origin: '*',      // Allow all origins
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            allowedHeaders: ['Content-Type', 'X-Requested-With', 'X-CSRF-TOKEN'],
            credentials: true
        }
    },
    plugins: [
        laravel({
            input: [
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/js/app.jsx'
        ],
        refresh: true,
        }),
    ],
});
