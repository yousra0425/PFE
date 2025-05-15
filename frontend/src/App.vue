<template>

 <router-view></router-view>
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
    ProviderForm
  }
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
