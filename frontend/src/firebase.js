// src/firebase.js
import { initializeApp } from "firebase/app";
import { getFirestore } from "firebase/firestore";
import { getAnalytics } from "firebase/analytics";
import { getStorage, ref as storageRef, uploadBytes, getDownloadURL } from "firebase/storage";

// ✅ Your Firebase config
const firebaseConfig = {
  apiKey: "AIzaSyC2RghPTfNTF2M1BTNJFIcl6rsgXYDF5Yg",
  authDomain: "domifix-40f3a.firebaseapp.com",
  projectId: "domifix-40f3a",
  storageBucket: "domifix-40f3a.firebasestorage.app",
  messagingSenderId: "77280078435",
  appId: "1:77280078435:web:06554e632679810e692656",
  measurementId: "G-7RF7QXV1VG"
};

// ✅ Initialize Firebase
const app = initializeApp(firebaseConfig);

// ✅ Initialize Firestore (what you'll use for chat)
const db = getFirestore(app);
const storage = getStorage(app);
const analytics = getAnalytics(app);

// ✅ Export Firestore
export { db };
export { storage, storageRef, uploadBytes, getDownloadURL };
