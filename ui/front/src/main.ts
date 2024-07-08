import './assets/main.css';

import { createPinia } from 'pinia';
import { createApp, reactive } from 'vue';
import { createApi } from 'composables/useApi';
import { useGeneralStore } from 'stores/general';
import { useConfig } from 'composables/useConfig';
import useAuthGuard from 'composables/useAuthGuard';

import App from './App.vue';
import router from './router';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';

import ToastService from "primevue/toastservice";
import DialogService from "primevue/dialogservice";
import ConfirmationService from "primevue/confirmationservice";

import AutoComplete from "primevue/autocomplete";
import Badge from "primevue/badge";
import BadgeDirective from "primevue/badgedirective";
import Button from "primevue/button";
import Card from "primevue/card";
import Chart from "primevue/chart";
import Checkbox from "primevue/checkbox";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import ConfirmPopup from "primevue/confirmpopup";
import DataTable from "primevue/datatable";
import Divider from "primevue/divider";
import InputText from "primevue/inputtext";
import Menu from "primevue/menu";
import Panel from "primevue/panel";
import Password from "primevue/password";
import Ripple from "primevue/ripple";
import Select from 'primevue/select';
import StyleClass from "primevue/styleclass";
import Textarea from "primevue/textarea";
import Toast from "primevue/toast";
import ToggleSwitch from 'primevue/toggleswitch';
import Tooltip from 'primevue/tooltip';

const pinia = createPinia();
const generalStore = useGeneralStore(pinia);

fetch(`${import.meta.env.BASE_URL}config.json`)
	.then(res => res.json())
	.then(async (config) => {
		const conf = useConfig(config);
		await generalStore.$patch(conf);

		const app = createApp(App);

		app.config.globalProperties.$conf = reactive(conf);
		app.provide("$conf", app.config.globalProperties.$conf);

		app.use(pinia);
		app.use(PrimeVue, {
			theme: {
				preset: Aura
			}
		});

		useAuthGuard(router);

		app.use(ConfirmationService);
		app.use(ToastService);
		app.use(DialogService);
		app.use(router);

		app.provide("$toast", app.config.globalProperties.$toast);
		app.provide("$primevue", app.config.globalProperties.$primevue);

		app.config.globalProperties.$api = createApi(app.config.globalProperties.$conf.api.baseUrl);
		app.provide("$api", app.config.globalProperties.$api);

		app.directive("tooltip", Tooltip);
		app.directive("ripple", Ripple);
		app.directive("badge", BadgeDirective);
		app.directive("styleclass", StyleClass);

		app.component("AutoComplete", AutoComplete);
		app.component("Badge", Badge);
		app.component("Button", Button);
		app.component("Card", Card);
		app.component("Chart", Chart);
		app.component("Checkbox", Checkbox);
		app.component("Column", Column);
		app.component("ConfirmDialog", ConfirmDialog);
		app.component("ConfirmPopup", ConfirmPopup);
		app.component("DataTable", DataTable);
		app.component("Divider", Divider);
		app.component("InputText", InputText);
		app.component("Menu", Menu);
		app.component("Panel", Panel);
		app.component("Password", Password);
		app.component("Select", Select);
		app.component("Textarea", Textarea);
		app.component("Toast", Toast);
		app.component("ToggleSwitch", ToggleSwitch);

		app.mount("#app");

		return;
	});
