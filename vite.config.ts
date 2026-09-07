import Symfony from '@symfony/reprise/vite';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

const CADDY_CERTIFICATE_DIR = '/data/caddy/certificates/local/localhost';

export default defineConfig(({ command }) => ({
  input: {
    app: './assets/app.js',
  },
  server:
    command === 'serve'
      ? {
          host: true,
          port: 5173,
          strictPort: true,
          // Caddy serves the pages over HTTPS, so plain HTTP assets would be blocked as mixed content.
          // Vite shares the container with Caddy and reuses the certificate it issued for localhost.
          https: {
            cert: `${CADDY_CERTIFICATE_DIR}/localhost.crt`,
            key: `${CADDY_CERTIFICATE_DIR}/localhost.key`,
          },
        }
      : undefined,
  plugins: [
    tailwindcss(),
    Symfony({
      stimulus: 'assets/controllers.json',
      // The server binds to 0.0.0.0 inside the container, which the browser cannot reach
      devServerOrigin: 'https://localhost:5173',
      // Without it the manifest keys would be prefixed with "build/", and every asset() call would have to be rewritten
      manifestKeyPrefix: '',
      copy: [
        {
          from: 'assets/images',
          to: 'images',
          pattern: /\.(avif|gif|ico|jpe?g|png|svg|webp)$/,
        },
      ],
    }),
  ],
}));
