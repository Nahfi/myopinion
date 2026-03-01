<template>
  <div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Latest Posts</h1>
      <router-link v-if="isAdmin" to="/admin/create-post"
        class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
        + New Post
      </router-link>
    </div>

    <!-- Skeleton loader -->
    <div v-if="loading" class="space-y-5">
      <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-6 border border-gray-100 animate-pulse">
        <div class="flex gap-3 mb-4">
          <div class="w-11 h-11 rounded-full bg-gray-200"></div>
          <div class="flex-1 space-y-2 pt-1">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/3"></div>
          </div>
        </div>
        <div class="space-y-2">
          <div class="h-3 bg-gray-100 rounded"></div>
          <div class="h-3 bg-gray-100 rounded w-4/5"></div>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <div class="text-5xl mb-4">😕</div>
      <p class="text-gray-500 mb-4">{{ error }}</p>
      <button @click="load" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm">Retry</button>
    </div>

    <div v-else-if="!posts.length" class="text-center py-16">
      <div class="text-5xl mb-3">📭</div>
      <p class="text-gray-400">No posts yet.</p>
    </div>

    <PostCard v-else
      v-for="post in posts" :key="post.id"
      :post="post" :reaction-types="reactionTypes"
      @remove="id => posts = posts.filter(p => p.id !== id)"
    />
  </div>
</template>

<script>
import PostCard from '../components/PostCard.vue';
export default {
  components: { PostCard },
  data() { return { posts: [], reactionTypes: [], loading: true, error: '' }; },
  computed: { isAdmin() { return localStorage.getItem('role') === 'admin'; } },
  async created() { await this.load(); },
  methods: {
    async load() {
      this.loading = true; this.error = '';
      try {
        const [postsRes, typesRes] = await Promise.all([
          this.$axios.get('/posts'),
          this.$axios.get('/reaction-types'),
        ]);
        this.posts         = postsRes.data.posts || [];
        this.reactionTypes = typesRes.data.types || [];
      } catch { this.error = 'Failed to load posts'; }
      finally { this.loading = false; }
    }
  }
};
</script>
