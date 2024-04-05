import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { viteServerHost, viteServerPort } from "./config/index.js"


// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: viteServerHost,
    port: viteServerPort
  }
})
