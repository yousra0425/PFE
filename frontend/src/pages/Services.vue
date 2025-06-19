<template>
  <div class="tutoring-page">
    <Hero />

    <div id="how-it-works">
      <HowItWorks />
    </div>

    <section id="provider-form">
    <BecomeTutor
      :image="becomeTeacherImage"
      button-link="/tutor-signup"
      button-text="Learn more"
    />
  </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import HowItWorks from '../components/HowItWorks.vue'
import Hero from '../components/Hero.vue'
import becomeTeacherImage from '../assets/become-teacher.png'
import BecomeTutor from '../components/BecomeTutor.vue' // Consider renaming to BecomeTutor.vue

const activeSubject = ref(null)
const selectedTutor = ref(null)
const subjects = ref([])
const router = useRouter()

</script>

<style scoped>
/* Reuse existing styles but rename classes accordingly */
.tutoring-page {
  font-family: 'Inter', sans-serif;
  background-color: #f5f7fa;
  min-height: 100vh;
  padding: 1rem 0;
}


</style>
