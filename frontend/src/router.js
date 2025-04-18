import { createRouter, createWebHistory } from 'vue-router';
import DefaultLayout from './components/DefaultLayout.vue';
import Login from './pages/Login.vue';
import Signup from './pages/Signup.vue';
import Courses from './components/Courses.vue';


const routes = [
    {
        path: '/',
        component: DefaultLayout,
        
    },
    { path: '/courses', component: Courses },
    { path: '/login', component: Login },
    { path: '/signup', component: Signup },
   
    /*{ path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },*/
];

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router;