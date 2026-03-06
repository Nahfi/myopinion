<template>
  <div class="max-w-lg mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Profile</h1>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
      <!-- Avatar -->
      <div class="flex items-center gap-4 mb-2">
        <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
          {{ initial }}
        </div>
        <div>
          <p class="font-bold text-gray-900 text-lg">{{ form.name }}</p>
          <p class="text-sm text-gray-400">@{{ form.username }}</p>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
        <input v-model="form.name" type="text"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
        <input v-model="form.username" type="text"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
        <input v-model="form.email" type="email"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
      </div>

      <hr class="border-gray-100" />
      <p class="text-sm font-semibold text-gray-500">Change Password <span class="font-normal">(leave blank to keep current)</span></p>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
        <input v-model="form.password" type="password" placeholder="Min. 6 characters"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
        <input v-model="form.password_confirm" type="password" placeholder="Repeat new password"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
      </div>

      <div v-if="error"   class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ error }}</div>
      <div v-if="success" class="p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ success }}</div>

      <button @click="save" :disabled="saving"
        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition disabled:opacity-60 flex items-center justify-center gap-2">
        <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        {{ saving ? 'Saving…' : 'Save Changes' }}
      </button>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  data() {
    return {
      form: { name: '', username: '', email: '', password: '', password_confirm: '' },
      saving: false,
      error: '',
      success: '',
    };
  },
  computed: {
    initial() { return (this.form.name || 'U').charAt(0).toUpperCase(); }
  },
  async created() {
    try {
      const res = await this.$axios.get('/profile');
      const u = res.data;
      this.form.name     = u.name;
      this.form.username = u.username;
      this.form.email    = u.email;
    } catch {
      this.error = 'Failed to load profile';
    }
  },
  methods: {
    async save() {
      this.error = ''; this.success = '';
      if (this.form.password && this.form.password !== this.form.password_confirm) {
        this.error = 'Passwords do not match'; return;
      }
      if (this.form.password && this.form.password.length < 6) {
        this.error = 'Password must be at least 6 characters'; return;
      }

      this.saving = true;
      try {
        const payload = {
          name:     this.form.name,
          username: this.form.username,
          email:    this.form.email,
        };
        if (this.form.password) payload.password = this.form.password;

        const res = await this.$axios.put('/profile', payload);
        // Update localStorage name
        localStorage.setItem('userName', this.form.name);
        this.success = 'Profile updated successfully!';
        this.form.password = '';
        this.form.password_confirm = '';
        toast.success('Profile updated!');
      } catch (e) {
        this.error = e.response?.data?.message || 'Update failed';
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>