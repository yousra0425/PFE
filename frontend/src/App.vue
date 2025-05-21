<template>
  <div>
    <!-- Your existing app layout -->

    <Header />
    
    <!-- Main content rendered by vue-router -->
    <router-view></router-view>
    
    

    <!-- Add ChatBubble and ChatPopup here -->
    <ChatBubble @toggle="toggleChat" />

    <ChatPopup v-if="chatOpen" @close="toggleChat">
      <!-- Pass your chat component in the default slot -->
      <template #default>
        <ChatRoom />
      </template>
      <template #input>
        <!-- You can put a message input here or inside ChatRoom -->
      </template>
    </ChatPopup>
    
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
import AdminPanel from './components/AdminPanel.vue';
import ProviderForm from './components/ProviderForm.vue';

// Import chat components
import ChatBubble from './components/ChatBubble.vue';
import ChatPopup from './components/ChatPopup.vue';
import ChatRoom from './components/ChatRoom.vue';

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
    AdminPanel,
    ProviderForm,

    // Register chat components
    ChatBubble,
    ChatPopup,
    ChatRoom,
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
  --secondary-color: #6c757d;
  --background-light: #f4f7fa;
  --text-dark: #333;
}

.container {
  margin-top: 60px;
}
</style>
