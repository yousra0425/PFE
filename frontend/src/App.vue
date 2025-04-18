<template>

 <router-view></router-view>
</template>

<script>
import Header from './components/Header.vue';
import Signup from './pages/Signup.vue';
import Courses from './components/Courses.vue';
import Footer from './components/Footer.vue';
export default {
  name: 'App',
  components: {
    Header,
    Courses,
    Footer
  }
}
</script>



<style>
:root {
  --primary-color: #17732f;
  --primary-hover: #177330e3;
  --secondary-color: #6c757d;
  --background-light: #f4f7fa;
  --text-dark: #333;
}

.container {
  margin-top: 60px;
}



</style>
