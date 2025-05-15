import { createRouter, createWebHistory } from 'vue-router';
import DefaultLayout from './components/DefaultLayout.vue';
import Login from './pages/Login.vue';
import Signup from './pages/Signup.vue';
import Dashboard from './pages/Dashboard.vue';
import CinVerif from './components/CinVerif.vue';
import ClientDashboard from './pages/ClientDashboard.vue'; // Import the component
import Services from './pages/Services.vue';
import NearbyProvidersMap from './components/NearbyProvidersMap.vue';
import CinVerificationPanel from './components/CINVerificationPanel.vue';
import AdminPanel from './components/AdminPanel.vue';
import ProviderForm from './components/ProviderForm.vue';
const routes = [
    {
        path: '/',
        component: DefaultLayout,
    },
    { path: '/login', component: Login },
    { path: '/signup', component: Signup },
    { path: '/dashboard', component: Dashboard },
    {
        path: '/clientdashboard',
        component: ClientDashboard,  // Use the imported component
    },
    { path: '/services', component: Services },
    { path: '/cinverif', component: CinVerif },
    { path: '/cinverificationpanel', component: CinVerificationPanel },
    { path: '/adminpanel', component: AdminPanel },
    { path: '/nearbyprovidersmap', component: NearbyProvidersMap },
    { path: '/providerform', component: ProviderForm },
    // { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }, 
];

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router;