import { createApp } from 'vue';
import AppComponent from './App.vue';
import router from './router';
import store from './store';
import http from './http';

import auth from './plugins/auth.js';

import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/saga-blue/theme.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Ripple from 'primevue/ripple';
import Tooltip from 'primevue/tooltip';

import { library } from '@fortawesome/fontawesome-svg-core';
import { fontAwesomeIcons } from '@/font-awesome';
import { primeVueLocaleOptions } from '@/primevue';

require('@/bootstrap');

//= ==========================================

library.add(...fontAwesomeIcons);

export const app = createApp(AppComponent);

app.use(store);
app.use(router);

app.use(http);
app.use(auth);

// PrimeVue==
app.directive('ripple', Ripple);
app.directive('tooltip', Tooltip);
app.use(PrimeVue, {
   ripple: true,
   locale: primeVueLocaleOptions
});
app.use(ToastService);
app.use(ConfirmationService);
// PrimeVue==


app.mount('#app');
