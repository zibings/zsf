import { createApp, reactive, watchEffect } from "vue";
import App from "./App.vue";
import router from "./router";

import PrimeVue from "primevue/config";
import AutoComplete from "primevue/autocomplete";
import Badge from "primevue/badge";
import BadgeDirective from "primevue/badgedirective";
import Button from "primevue/button";
import Calendar from "primevue/calendar";
import Card from "primevue/card";
import Chart from "primevue/chart";
import Checkbox from "primevue/checkbox";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import ConfirmPopup from "primevue/confirmpopup";
import ConfirmationService from "primevue/confirmationservice";
import DataTable from "primevue/datatable";
import DialogService from "primevue/dialogservice";
import Divider from "primevue/divider";
import Dropdown from "primevue/dropdown";
import InputSwitch from "primevue/inputswitch";
import InputText from "primevue/inputtext";
import Menu from "primevue/menu";
import Panel from "primevue/panel";
import Password from "primevue/password";
import Ripple from "primevue/ripple";
import StyleClass from "primevue/styleclass";
import Textarea from "primevue/textarea";
import Toast from "primevue/toast";
import ToastService from "primevue/toastservice";
import Tooltip from "primevue/tooltip";

import "@/assets/styles.scss";

import { createHead } from "@vueuse/head";
import { createPinia } from "pinia";
import { createApi } from "@/composables/useApi.js";
import { configParse } from "@/boot/config.js";
import { useLayout } from "@/layout/composables/layout";
import { toggleDarkMode } from "@/composables/toggleDarkMode.js";
import { useGeneralStore } from "@/stores/general-store.js";
import { useDark, useStorage, useToggle } from "@vueuse/core";

const pinia = createPinia();
const generalStore = useGeneralStore(pinia);
const { layoutConfig } = useLayout();

fetch(import.meta.env.BASE_URL + "config.json")
	.then((response) => response.json())
	.then(async (config) => {
		const head = createHead();
		const admConfig = configParse(config);

		await generalStore.$patch(admConfig);

		// const darkStore = useStorage("adm-dark-mode", generalStore.darkMode ? "dark" : "auto");
		const isDark = useDark({ onChanged: (dark) => (generalStore.darkMode = dark), storageKey: "adm-dark-mode" });

		const app = createApp(App);

		app.config.globalProperties.$admConfig = reactive(admConfig);
		app.provide("$admConfig", app.config.globalProperties.$admConfig);

		app.provide("$toggleDark", useToggle(isDark));

		app.use(pinia);
		app.use(head);
		app.use(PrimeVue, { ripple: generalStore.ripple });
		app.use(ConfirmationService);
		app.use(ToastService);
		app.use(DialogService);
		app.use(router);

		toggleDarkMode(generalStore.darkMode);

		// watchers after store is patched
		// layoutWatchers();
		watchEffect(() => {
			layoutConfig.menuMode.value = generalStore.menuMode;
			layoutConfig.inputStyle.value = generalStore.inputStyle;
			layoutConfig.theme.value = generalStore.theme;
			layoutConfig.darkTheme.value = generalStore.darkMode;
			layoutConfig.ripple.value = generalStore.ripple;
		});

		app.provide("$toast", app.config.globalProperties.$toast);
		app.provide("$primevue", app.config.globalProperties.$primevue);

		app.config.globalProperties.$api = createApi(app.config.globalProperties.$admConfig.api.baseUrl);
		app.provide("$api", app.config.globalProperties.$api);

		app.directive("tooltip", Tooltip);
		app.directive("ripple", Ripple);
		app.directive("badge", BadgeDirective);
		app.directive("styleclass", StyleClass);

		app.component("AutoComplete", AutoComplete);
		app.component("Badge", Badge);
		app.component("Button", Button);
		app.component("Calendar", Calendar);
		app.component("Card", Card);
		app.component("Chart", Chart);
		app.component("Checkbox", Checkbox);
		app.component("Column", Column);
		app.component("ConfirmDialog", ConfirmDialog);
		app.component("ConfirmPopup", ConfirmPopup);
		app.component("DataTable", DataTable);
		app.component("Divider", Divider);
		app.component("Dropdown", Dropdown);
		app.component("InputSwitch", InputSwitch);
		app.component("InputText", InputText);
		app.component("Menu", Menu);
		app.component("Panel", Panel);
		app.component("Password", Password);
		app.component("Textarea", Textarea);
		app.component("Toast", Toast);

		app.mount("#app");
	});

// function layoutWatchers() {
// watch(
// 	generalStore.ripple,
// 	(ripple) => {
// 		layoutConfig.ripple = ripple;
// 	},
// 	{ immediate: true }
// );
// watch(generalStore.darkMode, (darkMode) => {
// 	layoutConfig.darkMode = darkMode;
// });
// watch(generalStore.theme, (theme) => {
// 	layoutConfig.theme = theme;
// });
// watch(generalStore.inputStyle, (inputStyle) => {
// 	layoutConfig.inputStyle = inputStyle;
// });
// watch(generalStore.menuMode, (menuMode) => {
// 	layoutConfig.menuMode = menuMode;
// });
// }
