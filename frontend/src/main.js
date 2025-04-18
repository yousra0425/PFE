import { createApp } from 'vue'
import './assets/Global.css';
import App from './App.vue'
import router from './router.js';
import axios from "axios";

axios.defaults.baseURL = 'http://127.0.0.1:8000'; // Laravel backend
axios.defaults.withCredentials = true; // VERY IMPORTANT

import 'bootstrap/dist/css/bootstrap.min.css';

import 'bootstrap/dist/js/bootstrap.bundle.min.js';



const app = createApp(App);

app.use(router);
app.mount('#app');
