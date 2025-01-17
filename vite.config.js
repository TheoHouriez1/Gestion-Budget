export default defineConfig({
    plugins: [
      symfonyPlugin(),
    ],
    build: {
      manifest: true,
      outDir: 'public/build',
      rollupOptions: {
        input: 'assets/app.js',
        external: ['@symfony/ux-react'], // Ajoutez cette ligne
      },
    },
  });  