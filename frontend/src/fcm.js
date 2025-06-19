import { messaging } from './firebase';
import { getToken } from 'firebase/messaging';
import axios from 'axios';

const vapidKey = 'BC9hL9G1LbHeiP55PHbBEnlKHFiPyxN1TDmz4mgrVmuLSpneNlwgzcKw9vYyD6EZdoEYhQbImriFkBe6V7EO4OE';

export async function registerFCMToken() {
  const token = localStorage.getItem('token');
  if (!token) return;

  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

  try {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
      console.log('Notification permission denied');
      return;
    }

    const currentToken = await getToken(messaging, { vapidKey });
    if (currentToken) {
      await axios.post('http://127.0.0.1:8000/api/fcm-token', {
        fcm_token: currentToken
      });
      console.log('✅ FCM token sent to backend');
    } else {
      console.log('No FCM token available');
    }
  } catch (err) {
    console.error('Error sending FCM token:', err);
  }
}
