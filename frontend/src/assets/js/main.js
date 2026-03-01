

import axios from 'axios';
import { createApp } from 'vue';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import App from '../../App.vue';
import router from '../../router';
import '../css/main.css';


axios.defaults.baseURL = 'http://localhost:8081';
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}


const app = createApp(App);

app.config.globalProperties.$axios = axios;

app.use(Toast, {
    position: 'top-right',
    timeout: 3000,
})
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');

    if (to.meta.requiresAuth && !token) {
        return next('/login');
    }

    if (to.meta.requiresAdmin && role !== 'admin') {
        return next('/');
    }

    next();
});

app.use(router);
app.mount('#app');
