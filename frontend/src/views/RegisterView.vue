<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow rounded border border-indigo-200">
    <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">Create a New Account</h2>
    <input v-model="form.name" type="text" placeholder="Name" class="w-full mb-4 border border-gray-300 p-3 rounded focus:outline-none focus:ring focus:border-indigo-500">
    <input v-model="form.email" type="email" placeholder="Email" class="w-full mb-4 border border-gray-300 p-3 rounded focus:outline-none focus:ring focus:border-indigo-500">
    <input v-model="form.username" type="text" placeholder="Username" class="w-full mb-4 border border-gray-300 p-3 rounded focus:outline-none focus:ring focus:border-indigo-500">
    <input v-model="form.password" type="password" placeholder="Password" class="w-full mb-4 border border-gray-300 p-3 rounded focus:outline-none focus:ring focus:border-indigo-500">
    <button @click="register" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">Register</button>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification';
const toast = useToast();
export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        username: '',
        password: ''
      }
    }
  },
  methods: {
    async register() {
      try {
        const res = await this.$axios.post('/register', {
          name: this.form.name.trim(),
          email: this.form.email,
          username: this.form.username,
          password: this.form.password
        });
        toast.success('Account created. Please login.');
        this.$router.push('/login');
      } catch (err) {
        toast.error('Registration failed. Please try again.');
      }
    }
  }
}
</script>

<style scoped></style>