<template>
  <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Comment Moderation</h1>
        <p class="text-sm text-gray-500 mt-1">Approve or reject pending comments before they appear publicly</p>
      </div>
      <span v-if="pending.length" class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
        {{ pending.length }} pending
      </span>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl w-fit">
      <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
        :class="['px-4 py-2 rounded-lg text-sm font-medium transition', activeTab===tab.key ? 'bg-white shadow text-indigo-700' : 'text-gray-500 hover:text-gray-800']">
        {{ tab.label }}
        <span v-if="tab.key==='pending' && pending.length" class="ml-1 px-1.5 py-0.5 bg-red-500 text-white rounded-full text-xs">{{ pending.length }}</span>
      </button>
    </div>

    <div v-if="loading" class="text-center py-16 text-gray-400">Loading…</div>

    <div v-else-if="!currentList.length" class="text-center py-16">
      <div class="text-5xl mb-3">✅</div>
      <p class="text-gray-400">No {{ activeTab }} comments.</p>
    </div>

    <div v-else class="space-y-3">
      <div v-for="c in currentList" :key="c.id"
        class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-start gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
              <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-xs">
                {{ (c.author_name||c.author||'A').charAt(0).toUpperCase() }}
              </div>
              <span class="font-semibold text-gray-800 text-sm">{{ c.author_name || c.author }}</span>
              <span class="text-xs text-gray-400">on</span>
              <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-medium">{{ c.post_title || 'Post #'+c.post_id }}</span>
              <span class="text-xs text-gray-400">{{ formatTime(c.created_at) }}</span>
              <span :class="badge(c.status)" class="text-xs px-2 py-0.5 rounded-full font-medium">{{ c.status }}</span>
            </div>
            <p class="text-gray-700 text-sm pl-10 leading-relaxed">{{ c.content }}</p>
          </div>
          <div class="flex gap-2 flex-shrink-0">
            <button v-if="c.status!=='active'"   @click="approve(c)" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-semibold hover:bg-green-200 transition">✓ Approve</button>
            <button v-if="c.status!=='rejected'" @click="reject(c)"  class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold hover:bg-amber-200 transition">✕ Reject</button>
            <button @click="remove(c)" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-semibold hover:bg-red-200 transition">🗑</button>
          </div>
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
      pending: [],
      active: [],
      rejected: [],
      loading: true,
      activeTab: 'pending',
      tabs: [
        { key: 'pending', label: 'Pending' },
        { key: 'active', label: 'Approved' },
        { key: 'rejected', label: 'Rejected' }
      ]
    };
  },

  computed: {
    currentList() {
      return this[this.activeTab] || [];
    }
  },

  async created() {
    await this.load();
  },

  methods: {
    async load() {
      this.loading = true;

      try {
        const res = await this.$axios.get('/admin/comments');

        this.pending  = res.data.pending  || [];
        this.active   = res.data.active   || [];
        this.rejected = res.data.rejected || [];

      } catch (e) {
        toast.error('Failed to load comments');
      } finally {
        this.loading = false;
      }
    },

    async approve(c) {
      try {
        await this.$axios.patch(`/admin/comments/${c.id}/approve`);

        this.pending  = this.pending.filter(x => x.id !== c.id);
        this.rejected = this.rejected.filter(x => x.id !== c.id);

        this.active.unshift({ ...c, status: 'active' });

        toast.success('Approved');

      } catch {
        toast.error('Failed');
      }
    },

    async reject(c) {
      try {
        await this.$axios.patch(`/admin/comments/${c.id}/reject`);

        this.pending = this.pending.filter(x => x.id !== c.id);
        this.active  = this.active.filter(x => x.id !== c.id);

        this.rejected.unshift({ ...c, status: 'rejected' });

        toast.success('Rejected');

      } catch {
        toast.error('Failed');
      }
    },

    async remove(c) {
      try {
        await this.$axios.delete(`/admin/comments/${c.id}`);

        ['pending', 'active', 'rejected'].forEach(k => {
          this[k] = this[k].filter(x => x.id !== c.id);
        });

        toast.success('Deleted');

      } catch {
        toast.error('Failed');
      }
    },

    badge(status) {
      return {
        pending: 'bg-amber-100 text-amber-700',
        active: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700'
      }[status] || 'bg-gray-100 text-gray-600';
    },

    formatTime(ts) {
      return ts ? new Date(ts.replace(' ', 'T')).toLocaleString() : '';
    }
  }
};
</script>