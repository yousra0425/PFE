import { createApp } from 'vue';
import App from './App.vue';
import router from './router.js';
import axios from 'axios';

import VueGoogleMaps from '@fawmi/vue-google-maps';
import '@fortawesome/fontawesome-free/css/all.css';
import './assets/Global.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';

import { messaging } from './firebase.js';
import { onMessage } from 'firebase/messaging';

// Axios base config
axios.defaults.baseURL = 'http://127.0.0.1:8000';
axios.defaults.headers.common['Accept'] = 'application/json';

// Attach token if exists
const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Global 401 handler
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      localStorage.clear();
      router.push('/login');
    }
    return Promise.reject(error);
  }
);

// Foreground Firebase Messaging handler
onMessage(messaging, (payload) => {
  console.log('Foreground message received:', payload);

  if (Notification.permission === 'granted') {
    const { title, body, icon } = payload.notification || {};
    new Notification(title, {
      body: body || '',
      icon: icon || '/firebase-logo.png',
    });
  }

  const data = payload.data || {};
  if (data.booking_id && data.status) {
    window.dispatchEvent(new CustomEvent('bookingStatusUpdated', {
      detail: {
        bookingId: data.booking_id,
        status: data.status,
      }
    }));
  }
});

// Create and mount Vue app
const app = createApp(App);

app.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyDaTmX2tvirybRfIMtBql37AJhzrSJu3rY',
    libraries: 'places, marker',
    v: 'weekly',
  },
});

app.use(router);
app.mount('#app');
