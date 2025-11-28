import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        host: true, // слушать 0.0.0.0 в контейнере
        port: 5173,
        hmr: { host: "localhost", port: 5173 }, // браузер с хоста
        watch: { usePolling: true }, // стабильный вотч на volume
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
