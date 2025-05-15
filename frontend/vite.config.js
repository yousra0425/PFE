import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: '127.0.0.1',
    port: 5173, // optional, you can change it if needed
  },
  optimizeDeps: {
    include: ['fast-deep-equal']
  }
})
  