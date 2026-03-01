<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-200 text-3xl">💬</div>
        <h1 class="text-3xl font-extrabold text-gray-900">Join Opinion</h1>
        <p class="text-gray-500 mt-1 text-sm">Create your account and start sharing</p>
      </div>
      <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Create Account</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
            <input v-model="form.name" type="text" placeholder="John Doe"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
            <input v-model="form.username" type="text" placeholder="johndoe"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input v-model="form.email" type="email" placeholder="you@example.com"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <input v-model="form.password" type="password" placeholder="Min. 6 characters"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
          </div>
          <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ error }}</div>
          <button @click="register" :disabled="loading"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-60">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ loading ? 'Creating…' : 'Create Account' }}
          </button>
        </div>
        <p class="mt-6 text-center text-sm text-gray-500">
          Already have an account?
          <router-link to="/login" class="text-indigo-600 font-semibold hover:underline ml-1">Sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification';
const toast = useToast();
export default {
  data() { return { form: { name:'', username:'', email:'', password:'' }, loading: false, error: '' }; },
  methods: {
    async register() {
      this.error = '';
      const { name,username,email,password } = this.form;
      if (!name||!username||!email||!password) { this.error = 'All fields required.'; return; }
      this.loading = true;
      try {
        const res = await this.$axios.post('/register', this.form);
        localStorage.setItem('token',    res.data.token);
        localStorage.setItem('role',     res.data.user.role);
        localStorage.setItem('userName', res.data.user.name);
        this.$axios.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`;
        toast.success(`Welcome, ${res.data.user.name}!`);
        this.$router.push('/');
      } catch (e) {
        this.error = e.response?.data?.message || 'Registration failed';
      } finally { this.loading = false; }
    }
  }
};
</script>
