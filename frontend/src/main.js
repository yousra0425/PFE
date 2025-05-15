import { createApp } from 'vue';
import App from './App.vue';
import router from './router.js';
import axios from 'axios';

import VueGoogleMaps from '@fawmi/vue-google-maps';
import './assets/Global.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

axios.defaults.baseURL  = 'http://127.0.0.1:8000';
axios.defaults.headers.common['Accept'] = 'application/json';

const stored = localStorage.getItem('token');
if (stored) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${stored}`;
}

const app = createApp(App);

app.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyDaTmX2tvirybRfIMtBql37AJhzrSJu3rY',
    libraries: 'places', // for autocomplete
  },
});
app.use(router);
app.mount('#app');
