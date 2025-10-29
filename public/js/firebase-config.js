// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyCnB2trGxbrmMafupPhghdG-AE1BXvfSAM",
  authDomain: "caps-e43f5.firebaseapp.com",
  projectId: "caps-e43f5",
  databaseURL: "https://caps-e43f5-default-rtdb.firebaseio.com/",
  storageBucket: "caps-e43f5.appspot.com",
  messagingSenderId: "444977409280",
  appId: "1:444977409280:web:2c274cbb1b264411b1d7e5",
  measurementId: "G-340Q8ENF6V"
};

// Initialize Firebase (for v8 compatibility)
firebase.initializeApp(firebaseConfig);
const storage = firebase.storage();