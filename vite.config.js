import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const configuredDevServerUrl = env.VITE_DEV_SERVER_URL?.trim();
    const appUrl = env.APP_URL?.trim();
    let appUrlLooksLikeNgrok = false;
    try {
        appUrlLooksLikeNgrok = Boolean(appUrl && /ngrok(-free)?\.app$/i.test(new URL(appUrl).hostname));
    } catch {
        appUrlLooksLikeNgrok = false;
    }
    const devServerUrl = configuredDevServerUrl || (appUrlLooksLikeNgrok ? appUrl : undefined);

    let hmr;
    let origin;
    if (devServerUrl) {
        try {
            const u = new URL(devServerUrl);
            const isHttps = u.protocol === 'https:';
            const port = u.port ? Number(u.port) : (isHttps ? 443 : 5173);
            origin = `${u.protocol}//${u.host}`;
            hmr = {
                host: u.hostname,
                protocol: isHttps ? 'wss' : 'ws',
                clientPort: port,
            };
        } catch {
            hmr = undefined;
        }
    }

    return {
        server: {
            // 127.0.0.1 evita `public/hot` con [::] (a veces falla en Windows/Laragon).
            host: devServerUrl ? '0.0.0.0' : '127.0.0.1',
            port: 5173,
            strictPort: true,
            hmr,
            origin,
        },
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
        ],
    };
});