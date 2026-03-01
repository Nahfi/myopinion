import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';
import PostsView from '../views/PostsView.vue';
import PostDetailView from '../views/PostDetailView.vue';
import UserListView from '../views/UserListView.vue';
 
const routes = [
  { path: '/', component: PostsView },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/posts/:id', component: PostDetailView },
  { path: '/users', component: UserListView},
  {
    path: '/admin/create-post',
    component: () => import('../views/CreatePost.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
