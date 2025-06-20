<template>
  <div id="app" class="app-container">
    <Header />

    <main class="main-content">
      <router-view></router-view>
    </main>

    <ChatBubble @toggle="toggleChat" />

    <ChatPopup v-if="chatOpen" @close="toggleChat">
      <template #default>
        <ChatRoom />
      </template>
      <template #input>
        
      </template>
    </ChatPopup>

    <Footer />
  </div>
</template>


<script>
import Header from './components/Header.vue';
import Signup from './pages/Signup.vue';
import Footer from './components/Footer.vue';
import CinVerif from './components/CinVerif.vue';
import CINVerificationPanel from './components/CINVerificationPanel.vue';
import ClientDashboard from './pages/ClientDashboard.vue';
import Map from './components/Map.vue';
import Services from './pages/Services.vue';
import BecomeTutor from './components/BecomeTutor.vue';

// Import chat components
//import ChatBubble from './components/ChatBubble.vue';
//import ChatPopup from './components/ChatPopup.vue';
//import ChatRoom from './components/ChatRoom.vue';
import FilterBar from './components/FilterBar.vue';
import FilterSelect from './components/FilterSelect.vue';
import StepOne from './components/BecomeTutor/StepOne.vue';
import StepTwo from './components/BecomeTutor/StepTwo.vue';
import BookLesson from './components/BookLesson.vue';
import MyTutorAccount from './components/TutorDashboard/MyTutorAccount.vue';
import StepThree from './components/BecomeTutor/StepThree.vue';
import StepFour from './components/BecomeTutor/StepFour.vue';


export default {
  name: 'App',
  components: {
    Header,
    Footer,
    CinVerif,
    Signup,
    ClientDashboard,
    Services,
    Map,
    CINVerificationPanel,
    BecomeTutor,
    FilterBar,
    FilterSelect,
    StepOne,
    StepTwo,
    StepThree,
    StepFour,
    MyTutorAccount,

    // Register chat components
    //ChatBubble,
    //ChatPopup,
    //ChatRoom,
    BookLesson,
  },
  data() {
    return {
      chatOpen: false,
    };
  },
  methods: {
    toggleChat() {
      this.chatOpen = !this.chatOpen;
    },
  },
}
</script>

<style>
:root {
  --primary-color: #1ca79b;
  --primary-hover: #1ca79bce;
  --success-color: #10b981;
    --error-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --background-light: #f9fafb;
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    color: #1ca79b68;
}

html, body, #app {
  height: 100%;
  margin: 0;
}

.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* full viewport height */
}

.main-content {
  flex: 1; /* take all remaining vertical space */
  /* Optional: add padding or margin as needed */
}

.container {
  margin-top: 60px;
}
</style>
