import vue from "@vitejs/plugin-vue";
import { defineConfig } from "vite";
// import legacy from "@vitejs/plugin-legacy";
import path from "path";
import { fileURLToPath, URL } from "url";

// https://vitejs.dev/config/
const target = process.env.APP_TARGET ?? "dist";
console.log("output target: ", target);
const outDir = target;

export default defineConfig(async () => {
	return {
		resolve: {
			alias: {
				"#src": path.resolve("./src"),
				"@": fileURLToPath(new URL("./src", import.meta.url)),
			},
		},
		plugins: [
			vue({
				// reactivityTransform: true,
			}),
			// legacy({
			// 	targets: ["defaults", "not IE 11"],
			// }),
		],
		server: {
			host: true,
			port: 5173,
			watch: {
				usePolling: true,
			},
		},
		build: {
			outDir: outDir,
			rollupOptions: {
				output: {
					manualChunks: {
						zxcvbn: ["@zxcvbn-ts/core", "@zxcvbn-ts/matcher-pwned"],
						zxcvbnlang: ["@zxcvbn-ts/language-common", "@zxcvbn-ts/language-en"],
						// 			primevue: [
						// 				"primevue/config",
						// 				"primevue/autocomplete",
						// 				"primevue/accordion",
						// 				"primevue/accordiontab",
						// 				"primevue/avatar",
						// 				"primevue/avatargroup",
						// 				"primevue/badge",
						// 				"primevue/badgedirective",
						// 				"primevue/button",
						// 				"primevue/breadcrumb",
						// 				"primevue/calendar",
						// 				"primevue/card",
						// 				"primevue/carousel",
						// 				"primevue/chart",
						// 				"primevue/checkbox",
						// 				"primevue/chip",
						// 				"primevue/chips",
						// 				"primevue/colorpicker",
						// 				"primevue/column",
						// 				"primevue/confirmdialog",
						// 				"primevue/confirmpopup",
						// 				"primevue/confirmationservice",
						// 				"primevue/contextmenu",
						// 				"primevue/datatable",
						// 				"primevue/dataview",
						// 				"primevue/dataviewlayoutoptions",
						// 				"primevue/dialog",
						// 				"primevue/divider",
						// 				"primevue/dropdown",
						// 				// ],
						// 				// primevue2: [
						// 				"primevue/fieldset",
						// 				"primevue/fileupload",
						// 				"primevue/image",
						// 				"primevue/inlinemessage",
						// 				"primevue/inplace",
						// 				"primevue/inputmask",
						// 				"primevue/inputnumber",
						// 				"primevue/inputswitch",
						// 				"primevue/inputtext",
						// 				"primevue/knob",
						// 				"primevue/galleria",
						// 				"primevue/listbox",
						// 				"primevue/megamenu",
						// 				"primevue/menu",
						// 				"primevue/menubar",
						// 				"primevue/message",
						// 				"primevue/multiselect",
						// 				"primevue/orderlist",
						// 				"primevue/organizationchart",
						// 				"primevue/overlaypanel",
						// 				"primevue/paginator",
						// 				"primevue/panel",
						// 				"primevue/panelmenu",
						// 				"primevue/password",
						// 				"primevue/picklist",
						// 				"primevue/rating",
						// 				"primevue/radiobutton",
						// 				"primevue/ripple",
						// 				"primevue/selectbutton",
						// 				"primevue/scrollpanel",
						// 				"primevue/scrolltop",
						// 				"primevue/slider",
						// 				"primevue/sidebar",
						// 				"primevue/splitbutton",
						// 				"primevue/splitter",
						// 				"primevue/splitterpanel",
						// 				"primevue/steps",
						// 				"primevue/styleclass",
						// 				"primevue/tabmenu",
						// 				"primevue/tag",
						// 				"primevue/textarea",
						// 				"primevue/timeline",
						// 				"primevue/toast",
						// 				"primevue/toastservice",
						// 				"primevue/toolbar",
						// 				"primevue/tabview",
						// 				"primevue/tabpanel",
						// 				"primevue/tooltip",
						// 				"primevue/togglebutton",
						// 				"primevue/tree",
						// 				"primevue/treeselect",
						// 				"primevue/treetable",
						// 				"primevue/tristatecheckbox",
						// 			],
					},
				},
				// 	// external: ["config.js"],
			},
		},
	};
});
