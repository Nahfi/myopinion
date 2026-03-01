<template>
  <div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Reaction Types</h1>
    <p class="text-sm text-gray-500 mb-6">Manage the emoji reactions users can add to posts.</p>

    <!-- Add form -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm mb-6">
      <h2 class="font-semibold text-gray-800 mb-4">Add New Reaction</h2>
      <div class="flex gap-3 flex-wrap">
        <input v-model="form.emoji" placeholder="Emoji" maxlength="4"
          class="w-20 border border-gray-200 rounded-xl px-3 py-2 text-center text-2xl focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        <input v-model="form.name" placeholder="Name (e.g. Love)"
          class="flex-1 min-w-32 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        <input v-model.number="form.sort_order" type="number" placeholder="Order"
          class="w-20 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
        <button @click="addType" :disabled="!form.emoji||!form.name"
          class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 disabled:opacity-40 transition">Add</button>
      </div>
    </div>

    <div v-if="loading" class="text-center text-gray-400 py-8">Loading…</div>
    <div v-else class="space-y-2">
      <div v-for="type in types" :key="type.id"
        class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition">
        <span class="text-3xl">{{ type.emoji }}</span>
        <div class="flex-1">
          <p class="font-semibold text-gray-800">{{ type.name }}</p>
          <p class="text-xs text-gray-400">Order: {{ type.sort_order }} · {{ type.is_active ? 'Active' : 'Hidden' }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="toggleActive(type)"
            :class="['px-3 py-1.5 rounded-lg text-xs font-semibold transition', type.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
            {{ type.is_active ? '✓ Active' : '○ Hidden' }}
          </button>
          <button @click="remove(type)" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-semibold hover:bg-red-200 transition">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification';
const toast = useToast();
export default {
  data() { return { types: [], loading: true, form: { emoji:'', name:'', sort_order:0 } }; },
  async created() { await this.load(); },
  methods: {
    async load() {
      this.loading = true;
      try {
        const res = await this.$axios.get('/admin/reaction-types');
        this.types = res.data.types || [];
      } catch { toast.error('Failed to load'); }
      finally { this.loading = false; }
    },
    async addType() {
      try {
        const res = await this.$axios.post('/admin/reaction-types', { ...this.form, is_active: 1 });
        // Reload to get fresh data
        await this.load();
        this.form = { emoji:'', name:'', sort_order:0 };
        toast.success('Reaction type added');
      } catch { toast.error('Failed to add'); }
    },
    async toggleActive(type) {
      try {
        await this.$axios.put(`/admin/reaction-types/${type.id}`, { is_active: type.is_active ? 0 : 1 });
        await this.load();
        toast.success('Updated');
      } catch { toast.error('Failed'); }
    },
    async remove(type) {
      try {
        await this.$axios.delete(`/admin/reaction-types/${type.id}`);
        this.types = this.types.filter(t => t.id !== type.id);
        toast.success('Deleted');
      } catch { toast.error('Failed to delete'); }
    }
  }
};
</script>
