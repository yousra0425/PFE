import { createApp } from 'vue';
import App from './App.vue';
import router from './router.js';
import axios from 'axios';

import './assets/Global.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';


axios.defaults.baseURL = 'http://127.0.0.1:8000/api'; // Adjust if your API prefix is /api
axios.defaults.withCredentials = true;

const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);
app.use(router);
app.mount('#app');
