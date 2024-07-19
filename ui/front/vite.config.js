import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import tsconfigPaths from "vite-tsconfig-paths";
import Components from "unplugin-vue-components/vite";
import Icons from "unplugin-icons/vite";
import { PrimeVueResolver } from "@primevue/auto-import-resolver";
import IconsResolver from "unplugin-icons/resolver";
import resolve from "@rollup/plugin-node-resolve";

const customResolver = resolve({
	extensions: [".esm.js", ".css", ".scss"],
});

// https://vitejs.dev/config/
export default defineConfig({
	plugins: [
		vue(),
		tsconfigPaths(),
		Components({
			resolvers: [
				IconsResolver({
					alias: {
						// fas: "fa6-solid",
						// fab: "fa6-brands",
						// far: "fa6-regular",
						// hero: "heroicons",
					},
				}),
				PrimeVueResolver(),
			],
			dts: true,
		}),
		Icons({
			compiler: "vue3",
			autoInstall: true,
		}),
	],
	ssgOptions: {
		script: "async",
		formatting: "prettify",

		includedRoutes(paths, routes) {
			return routes
				.filter((route) => {
					if (route.meta.static && !route.path.includes(":") && !route.path.includes("*") && !route.path.includes(".html")) {
						return route.path;
					}
				})
				.map((i) => i.path);
		},

		crittersOptions: {
			preload: "swap-high",
			reduceInlineStyles: false,
			allowRules: [/^\.xl/, /^\.lg/, /^\.md/, /^\.sm/],
		},
	},
	resolve: {
		alias: [
			{
				find: "primevue",
				customResolver: customResolver,
				replacement: function (alias) {
					return alias;
				},
			},
		],
	},
});
