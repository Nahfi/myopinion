<template>
  <nav class="bg-indigo-700 text-white shadow-lg sticky top-0 z-40">
    <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
      <router-link to="/" class="text-xl font-extrabold tracking-tight hover:text-indigo-200 transition flex items-center gap-2">
        💬 Opinion
      </router-link>

      <div class="flex items-center gap-1">
        <router-link to="/" class="px-3 py-1.5 rounded-lg hover:bg-indigo-600 text-sm font-medium transition">Posts</router-link>

        <template v-if="!isAuthenticated">
          <router-link to="/login"    class="px-3 py-1.5 rounded-lg hover:bg-indigo-600 text-sm font-medium transition">Login</router-link>
          <router-link to="/register" class="px-3 py-1.5 rounded-lg bg-white text-indigo-700 font-semibold text-sm hover:bg-indigo-100 transition ml-1">Register</router-link>
        </template>

        <template v-else>
          <template v-if="isAdmin">
            <router-link to="/admin/create-post" class="px-3 py-1.5 rounded-lg hover:bg-indigo-600 text-sm font-medium transition">New Post</router-link>
            <router-link to="/admin/users"       class="px-3 py-1.5 rounded-lg hover:bg-indigo-600 text-sm font-medium transition">Users</router-link>
            <router-link to="/admin/comments"    class="relative px-3 py-1.5 rounded-lg hover:bg-indigo-600 text-sm font-medium transition">
              Comments
              <span v-if="pendingCount > 0" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs flex items-center justify-center font-bold">
                {{ pendingCount > 9 ? '9+' : pendingCount }}
              </span>
            </router-link>
            <router-link to="/admin/reactions"   class="px-3 py-1.5 rounded-lg hover:bg-indigo-600 text-sm font-medium transition">Reactions</router-link>
          </template>

          <div class="flex items-center gap-2 ml-2 pl-2 border-l border-indigo-500">
            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-sm">{{ userInitial }}</div>
            <button @click="logout" class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-sm font-medium transition">Logout</button>
          </div>
        </template>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  data() { return { token: null, role: null, userName: '', pendingCount: 0 }; },
  computed: {
    isAuthenticated() { return !!this.token; },
    isAdmin()         { return this.role === 'admin'; },
    userInitial()     { return (this.userName || 'U').charAt(0).toUpperCase(); },
  },
  methods: {
    syncAuth() {
      this.token    = localStorage.getItem('token');
      this.role     = localStorage.getItem('role');
      this.userName = localStorage.getItem('userName') || '';
      if (this.isAdmin) this.fetchPending();
    },
    logout() {
      this.$axios.post('/logout').catch(() => {});
      ['token','role','userName'].forEach(k => localStorage.removeItem(k));
      delete this.$axios.defaults.headers.common['Authorization'];
      this.token = null; this.role = null;
      this.$router.push('/login');
    },
    async fetchPending() {
      try {
        const res = await this.$axios.get('/admin/comments/pending');
        this.pendingCount = res.data.comments?.length || 0;
      } catch {}
    }
  },
  created() { this.syncAuth(); },
  watch: { $route() { this.syncAuth(); } }
};
</script>
