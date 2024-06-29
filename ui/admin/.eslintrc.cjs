/* eslint-env node */
require("@rushstack/eslint-patch/modern-module-resolution");

module.exports = {
	root: true,
	plugins: ["vue"],
	extends: [
		"eslint:recommended",
		// "plugin:vue/vue3-essential",
		"plugin:vue/vue3-strongly-recommended",
		"prettier",
	],
	parserOptions: {
		ecmaVersion: "latest",
		sourceType: "module",
	},
	env: {
		node: true,
		browser: true,
		"vue/setup-compiler-macros": true,
	},
	rules: {
		"vue/multi-word-component-names": "off",
		"vue/no-reserved-component-names": "off",
		"vue/component-tags-order": [
			"error",
			{
				order: ["template", "script", "style"],
			},
		],

		// allow debugger during development only
		"no-debugger": process.env.NODE_ENV === "production" ? "error" : "off",
	},

	globals: {
		ga: "readonly", // Google Analytics
		cordova: "readonly",
		__statics: "readonly",
		process: "readonly",
		Capacitor: "readonly",
		chrome: "readonly",
	},
};
