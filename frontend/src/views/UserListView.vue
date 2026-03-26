<template>
  <div class="max-w-6xl mx-auto mt-10 px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">All Users</h1>
      <button @click="openCreate"
        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
        + Add User
      </button>
    </div>

    <div v-if="loading" class="text-gray-400 text-sm">Loading users...</div>
    <div v-if="error" class="text-red-500 text-sm mb-4">{{ error }}</div>

     <div v-if="!loading" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Username</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="!users.length">
              <td colspan="8" class="text-center py-10 text-gray-400">No users found</td>
            </tr>
            <tr v-for="(user, index) in users" :key="user.id" class="hover:bg-gray-50 transition">
              <td class="px-4 py-3 text-gray-400">{{ index + 1 }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                    {{ user.name?.charAt(0).toUpperCase() }}
                  </div>
                  <span class="font-medium text-gray-800">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-gray-500">@{{ user.username || '—' }}</td>
              <td class="px-4 py-3 text-gray-500">{{ user.email }}</td>
              <td class="px-4 py-3">
                <span :class="user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600'"
                  class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-4 py-3">
                <!-- no toggle for admin -->
                <span v-if="user.role === 'admin'"
                  class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 text-purple-400 capitalize">
                  admin
                </span>
                <button v-else @click="toggleStatus(user)"
                  :disabled="togglingId === user.id"
                  :class="user.status === 'active'
                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                    : 'bg-red-100 text-red-600 hover:bg-red-200'"
                  class="text-xs font-semibold px-2.5 py-1 rounded-full transition capitalize disabled:opacity-50">
                  {{ togglingId === user.id ? '…' : user.status }}
                </button>
              </td>
              <td class="px-4 py-3 text-gray-400 text-xs">{{ formatDate(user.created_at) }}</td>
              <td class="px-4 py-3">
                <!-- no actions for admin -->
                <span v-if="user.role === 'admin'" class="text-xs text-gray-300 italic">—</span>
                <div v-else class="flex items-center gap-2">
                  <button @click="openEdit(user)"
                    class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-medium px-3 py-1.5 rounded-lg transition">
                    Edit
                  </button>
                  <button @click="confirmDelete(user)"
                    class="text-xs bg-red-50 text-red-500 hover:bg-red-100 font-medium px-3 py-1.5 rounded-lg transition">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <!-- Add / Edit Modal -->
    <div v-if="modal.show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-bold text-gray-900">{{ modal.isEdit ? 'Edit User' : 'Add User' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" placeholder="John Doe"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input v-model="form.username" type="text" placeholder="johndoe"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input v-model="form.email" type="email" placeholder="john@example.com"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <hr class="border-gray-100" />
          <p class="text-xs text-gray-400">
            {{ modal.isEdit ? 'Leave password blank to keep current password.' : 'Password is required.' }}
          </p>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Password <span v-if="!modal.isEdit" class="text-red-500">*</span>
            </label>
            <input v-model="form.password" type="password" placeholder="Min. 6 characters"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <!-- confirm only shown when password is filled -->
          <div v-if="form.password">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Confirm Password <span class="text-red-500">*</span>
            </label>
            <input v-model="form.password_confirm" type="password" placeholder="Repeat password"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>
        </div>

        <div v-if="modal.error" class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
          {{ modal.error }}
        </div>

        <div class="flex gap-3 pt-1">
          <button @click="closeModal"
            class="flex-1 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
            Cancel
          </button>
          <button @click="submitForm" :disabled="modal.saving"
            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-sm transition disabled:opacity-60 flex items-center justify-center gap-2">
            <svg v-if="modal.saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ modal.saving ? 'Saving…' : (modal.isEdit ? 'Save Changes' : 'Create User') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete confirm -->
    <div v-if="deleteModal.show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center space-y-4">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto text-2xl">🗑️</div>
        <h2 class="text-lg font-bold text-gray-900">Delete User?</h2>
        <p class="text-sm text-gray-500">
          Are you sure you want to delete <strong>{{ deleteModal.user?.name }}</strong>? This cannot be undone.
        </p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false"
            class="flex-1 border border-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
            Cancel
          </button>
          <button @click="deleteUser" :disabled="deleteModal.loading"
            class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 rounded-xl text-sm transition disabled:opacity-60">
            {{ deleteModal.loading ? 'Deleting…' : 'Yes, Delete' }}
          </button>
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
      users: [],
      loading: true,
      error: '',
      togglingId: null,
      form: { name: '', username: '', email: '', password: '', password_confirm: '' },
      modal: { show: false, isEdit: false, editId: null, saving: false, error: '' },
      deleteModal: { show: false, user: null, loading: false },
    };
  },

  async mounted() {
    await this.fetchUsers();
  },

  methods: {
    async fetchUsers() {
      this.loading = true;
      try {
        const res = await this.$axios.get('/admin/users');
        this.users = res.data.users;
      } catch {
        this.error = 'Failed to load users';
      } finally {
        this.loading = false;
      }
    },

    formatDate(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    },

    openCreate() {
      this.form = { name: '', username: '', email: '', password: '', password_confirm: '' };
      this.modal = { show: true, isEdit: false, editId: null, saving: false, error: '' };
    },

    openEdit(user) {
      this.form = { name: user.name, username: user.username || '', email: user.email, password: '', password_confirm: '' };
      this.modal = { show: true, isEdit: true, editId: user.id, saving: false, error: '' };
    },

    closeModal() {
      this.modal.show = false;
    },

    async submitForm() {
      this.modal.error = '';

      // frontend confirm check before hitting API
      if (this.form.password && this.form.password !== this.form.password_confirm) {
        this.modal.error = 'Passwords do not match';
        return;
      }

      this.modal.saving = true;
      try {
        if (this.modal.isEdit) {
          await this.$axios.put(`/admin/users/${this.modal.editId}`, this.form);
          toast.success('User updated!');
        } else {
          await this.$axios.post('/admin/users', this.form);
          toast.success('User created!');
        }
        this.closeModal();
        await this.fetchUsers();
      } catch (e) {
        this.modal.error = e.response?.data?.message || 'Something went wrong';
      } finally {
        this.modal.saving = false;
      }
    },

    confirmDelete(user) {
      this.deleteModal = { show: true, user, loading: false };
    },

    async deleteUser() {
      this.deleteModal.loading = true;
      try {
        await this.$axios.delete(`/admin/users/${this.deleteModal.user.id}`);
        toast.success('User deleted');
        this.deleteModal.show = false;
        await this.fetchUsers();
      } catch {
        toast.error('Failed to delete user');
      } finally {
        this.deleteModal.loading = false;
      }
    },

    async toggleStatus(user) {
      this.togglingId = user.id;
      try {
        const res = await this.$axios.patch(`/admin/users/${user.id}/status`);
        user.status = res.data.status;
        toast.success(`User ${res.data.status}`);
      } catch {
        toast.error('Failed to update status');
      } finally {
        this.togglingId = null;
      }
    },
  },
};
</script>