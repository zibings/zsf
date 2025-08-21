import type { UserConfig } from 'vite';
import { fileURLToPath } from 'node:url';
import { mergeConfig, defineConfig, configDefaults } from 'vitest/config';

const viteConfig = (await import('./vite.config.js')).default as UserConfig;

export default defineConfig(async () => {
	return mergeConfig(
		viteConfig,
		{
			test: {
				environment: 'jsdom',
				exclude: [...configDefaults.exclude, 'e2e/**'],
				root: fileURLToPath(new URL('./', import.meta.url))
			}
		}
	)
});
