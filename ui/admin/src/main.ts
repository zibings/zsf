import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import router from './router';

import ToastService from 'primevue/toastservice';
import DialogService from 'primevue/dialogservice';
import ConfirmationService from 'primevue/confirmationservice';

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(ToastService);
app.use(DialogService);
app.use(ConfirmationService);

app.mount('#app');
