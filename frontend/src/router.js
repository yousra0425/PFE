import { createRouter, createWebHistory } from 'vue-router';
import DefaultLayout from './components/DefaultLayout.vue';
import Login from './pages/Login.vue';
import Signup from './pages/Signup.vue';
import TutorSignup from './pages/TutorSignup.vue';
import Dashboard from './pages/Dashboard.vue';
import CinVerif from './components/CinVerif.vue';
import ClientDashboard from './pages/ClientDashboard.vue';
import Services from './pages/Services.vue';

import CinVerificationPanel from './components/CINVerificationPanel.vue';
import AdminDashboard from './pages/AdminDashboard.vue';
import ProviderForm from './components/BecomeTutor.vue';
import OnlineTutors from './pages/OnlineTutors.vue';
import FindTutors from './pages/FindTutors.vue';
import TutorDashboard from './pages/TutorDashboard.vue';
import BecomeTutor from './pages/BecomeTutor.vue';
import ClientsMap from './pages/ClientsMap.vue';

const routes = [
  {
    path: '/',
    component: DefaultLayout,
  },
  { path: '/login', component: Login },
  { path: '/signup', component: Signup },
  { path: '/tutor-signup', component: TutorSignup },
  { path: '/becometutor', component: BecomeTutor },
  { path: '/dashboard', component: Dashboard },
  { path: '/clientdashboard', component: ClientDashboard },  
  { path: '/tutordashboard', component: TutorDashboard },
  { path: '/admindashboard', name: 'AdminDashboard', component: AdminDashboard },
  { path: '/services', component: Services },
  { path: '/cinverif', component: CinVerif },
  { path: '/cinverificationpanel', component: CinVerificationPanel },

  { path: '/providerform', component: ProviderForm },
  {
    path: '/findtutors',
    name: 'FindTutors',
    component: FindTutors,
    props: route => ({
      category: route.query.category,
      mode: route.query.mode,
      lat: route.query.lat,
      lng: route.query.lng,
      location: route.query.location,
    }),
  },
  { path: '/onlinetutors', name: 'OnlineTutors', component: OnlineTutors },

  { path: '/clientsmap', name: 'ClientsMap', component: ClientsMap },

  // Optional: catch-all route for 404s
  // { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
