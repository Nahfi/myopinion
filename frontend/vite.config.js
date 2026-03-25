import vue from '@vitejs/plugin-vue'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [vue()],
  css: {
    postcss: './postcss.config.cjs',
  },
  server: {
    host: '0.0.0.0',  // expose outside container
    port: 5171,        // must match docker-compose port
    strictPort: true,  // fail if port is busy
  },
})