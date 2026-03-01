import { createRouter, createWebHistory } from 'vue-router';
import PostsView    from '../views/PostsView.vue';
import LoginView    from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';

const routes = [
  { path: '/', component: PostsView },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/admin/create-post', component: () => import('../views/CreatePost.vue'),        meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/users',       component: () => import('../views/UserListView.vue'),       meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/comments',    component: () => import('../views/AdminCommentsView.vue'),  meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/reactions',   component: () => import('../views/AdminReactionsView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const role  = localStorage.getItem('role');
  if (to.meta.requiresAuth && !token) return next({ path: '/login', query: { redirect: to.fullPath } });
  if (to.meta.requiresAdmin && role !== 'admin') return next('/');
  next();
});

export default router;
