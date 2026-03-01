<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-100 flex items-center justify-center p-4">
    <div class="flex flex-col lg:flex-row bg-white shadow-2xl rounded-2xl overflow-hidden max-w-5xl w-full">
      <!-- Left Side -->
      <div class="flex-1 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white flex flex-col justify-center p-10">
        <h1 class="text-5xl font-extrabold mb-4">Opinion</h1>
        <p class="text-lg opacity-90">Share your thoughts. React. Discuss. All in one wave.</p>
      </div>

      <!-- Right Side: Login Form -->
      <div class="flex-1 p-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Sign In to Continue</h2>

        <div class="space-y-4">
          <input
            type="text"
            v-model="form.identifier"
            placeholder="Email or Username"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />

          <input
            type="password"
            v-model="form.password"
            placeholder="Password"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />

          <button
            @click="login"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-md shadow-md transition"
          >
            Log In
          </button>
 
          <div class="my-4 border-t border-gray-300"></div>

         <router-link to="/register" custom v-slot="{ navigate }">
            <button
              @click="navigate"
              class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-md shadow-md transition"
            >
              Create New Account
            </button>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification';
const toast = useToast();
export default {
  data() {
    return {
      form: {
        identifier: '',
        password: ''
      }
    };
  },
  methods: {
    async login() {
      try {
        const res = await this.$axios.post('/login', {
          email: this.form.identifier,
          password: this.form.password
        });
        localStorage.setItem('token', res.data.token);
        localStorage.setItem('role', res.data.user.role);
        this.$axios.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`;
        toast.success('Login successful!');
        this.$router.push('/');
      } catch (err) { 
        toast.error('Invalid login credentials');
      }
    },
  }
};
</script>

<style scoped>
input::placeholder {
  color: #999;
}
a {
  font-size: 0.95rem;
}
</style>