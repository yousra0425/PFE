// firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyC2RghPTfNTF2M1BTNJFIcl6rsgXYDF5Yg",
  authDomain: "domifix-40f3a.firebaseapp.com",
  projectId: "domifix-40f3a",
  storageBucket: "domifix-40f3a.appspot.com",
  messagingSenderId: "77280078435",
  appId: "1:77280078435:web:06554e632679810e692656",
  measurementId: "G-7RF7QXV1VG"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    // optionally icon: payload.notification.icon
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
