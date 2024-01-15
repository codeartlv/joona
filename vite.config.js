import { defineConfig } from 'vite';
import copy from 'rollup-plugin-copy';
import path from 'path';
import nunjucks from 'vite-plugin-nunjucks';

export default defineConfig({
	plugins: [
		copy({
			targets: [
				{
					src: 'resources/assets/images/*',
					dest: 'resources/assets/dist/images',
				},
			],
			hook: 'writeBundle',
			copyOnce: true,
		}),
		nunjucks(),
	],
	root: path.resolve(__dirname, 'resources/assets'),
	server: {
		port: 8080,
		hot: true,
	},
	resolve: {
		alias: {
			'@bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
		},
	},
	build: {
		outDir: './dist',
		lib: {
			entry: path.resolve(__dirname, 'resources/assets/js/app.js'),
			name: 'Joona',
			fileName: (format) => `joona.${format}.js`,
		},
		rollupOptions: {
			external: [],
		},
	},
});
