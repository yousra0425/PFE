import { initializeApp } from "firebase/app";
import { getFirestore } from "firebase/firestore";
import { getAnalytics } from "firebase/analytics";
import { getStorage, ref as storageRef, uploadBytes, getDownloadURL } from "firebase/storage";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
  apiKey: "AIzaSyC2RghPTfNTF2M1BTNJFIcl6rsgXYDF5Yg",
  authDomain: "domifix-40f3a.firebaseapp.com",
  projectId: "domifix-40f3a",
  storageBucket: "domifix-40f3a.appspot.com",
  messagingSenderId: "77280078435",
  appId: "1:77280078435:web:06554e632679810e692656",
  measurementId: "G-7RF7QXV1VG"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const storage = getStorage(app);
const analytics = getAnalytics(app);
const messaging = getMessaging(app);

const requestFirebaseNotificationPermission = async () => {
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      const currentToken = await getToken(messaging, {
        vapidKey: 'BC9hL9G1LbHeiP55PHbBEnlKHFiPyxN1TDmz4mgrVmuLSpneNlwgzcKw9vYyD6EZdoEYhQbImriFkBe6V7EO4OE'
      });
      if (currentToken) {
        console.log('FCM Token:', currentToken);
        // TODO: Send this token to backend here
        return currentToken;
      } else {
        console.log('No registration token available.');
        return null;
      }
    } else {
      console.log('Notification permission denied');
      return null;
    }
  } catch (error) {
    console.error('Error retrieving token: ', error);
    return null;
  }
};

onMessage(messaging, (payload) => {
  console.log('Message received in foreground: ', payload);
  const { title, body, icon } = payload.notification || {};
  if (Notification.permission === 'granted' && title) {
    new Notification(title, {
      body,
      icon: icon || '/firebase-logo.png',
    });
  }
});

export { db, storage, storageRef, uploadBytes, getDownloadURL, messaging, requestFirebaseNotificationPermission };
