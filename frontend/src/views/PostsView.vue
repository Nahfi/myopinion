<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto py-8 px-4">
      <div class="flex gap-8 items-start">

        <!-- ── Main Feed ───────────────────────────────────────── -->
        <div class="flex-1 min-w-0">

          <!-- Header -->
          <div class="flex items-center justify-between mb-5">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Latest Posts</h1>
              <p v-if="!loading" class="text-sm text-gray-400 mt-0.5">
                {{ posts.length }} post{{ posts.length !== 1 ? 's' : '' }}
              </p>
            </div>
            <router-link v-if="isAdmin" to="/admin/create-post"
              class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
              + New Post
            </router-link>
          </div>

          <!-- Skeleton -->
          <div v-if="loading" class="space-y-3">
            <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm animate-pulse">
              <div class="flex gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0"></div>
                <div class="flex-1 space-y-2 pt-1">
                  <div class="h-4 bg-gray-200 rounded-lg w-2/3"></div>
                  <div class="h-3 bg-gray-100 rounded-lg w-1/4"></div>
                </div>
              </div>
              <div class="space-y-2">
                <div class="h-3 bg-gray-100 rounded-lg w-full"></div>
                <div class="h-3 bg-gray-100 rounded-lg w-4/5"></div>
              </div>
              <div class="flex gap-2 mt-4 pt-4 border-t border-gray-50">
                <div class="h-8 bg-gray-100 rounded-xl w-20"></div>
                <div class="h-8 bg-gray-100 rounded-xl w-24"></div>
                <div class="h-8 bg-gray-100 rounded-xl w-16 ml-auto"></div>
              </div>
            </div>
          </div>

          <!-- Error -->
          <div v-else-if="error" class="bg-white rounded-2xl border border-red-100 p-12 text-center shadow-sm">
            <div class="text-5xl mb-3">😕</div>
            <p class="text-gray-600 font-medium mb-1">Something went wrong</p>
            <p class="text-gray-400 text-sm mb-5">{{ error }}</p>
            <button @click="load" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
              Try Again
            </button>
          </div>

          <!-- Empty -->
          <div v-else-if="!posts.length" class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="text-5xl mb-3">📭</div>
            <p class="text-gray-600 font-medium">No posts yet</p>
            <p class="text-gray-400 text-sm mt-1">Be the first to share something!</p>
          </div>

          <!-- ✅ Posts — wrapped in div with space-y-3 so each card has consistent gap -->
          <div v-else class="space-y-3">
            <PostCard
              v-for="post in posts"
              :key="post.id"
              :post="post"
              :reaction-types="reactionTypes"
              @remove="id => posts = posts.filter(p => p.id !== id)"
            />
          </div>

        </div>

        <!-- ── Sidebar ─────────────────────────────────────────── -->
        <aside v-if="isLoggedIn" class="w-72 flex-shrink-0 sticky top-20 space-y-4">

          <!-- Join the Conversation widget -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <!-- Header -->
            <div class="px-4 pt-4 pb-3 border-b border-gray-50">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0 text-base">💬</div>
                <div>
                  <h2 class="font-bold text-gray-900 text-sm leading-tight">Join the Conversation</h2>
                  <p class="text-xs text-gray-400 mt-0.5">Posts awaiting your comment</p>
                </div>
              </div>
            </div>

            <!-- Skeleton -->
            <div v-if="sidebarLoading" class="p-3 space-y-1">
              <div v-for="i in 4" :key="i" class="animate-pulse flex items-center gap-3 p-2 rounded-xl">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0"></div>
                <div class="flex-1 space-y-1.5">
                  <div class="h-3 bg-gray-200 rounded-lg w-4/5"></div>
                  <div class="h-2.5 bg-gray-100 rounded-lg w-1/2"></div>
                </div>
              </div>
            </div>

            <!-- All caught up -->
            <div v-else-if="!suggestedPosts.length" class="px-4 py-8 text-center">
              <div class="text-3xl mb-2">🎉</div>
              <p class="text-sm font-medium text-gray-700">All caught up!</p>
              <p class="text-xs text-gray-400 mt-1">You've commented on every post.</p>
            </div>

            <!-- List -->
            <div v-else class="p-2">
              <a
                v-for="post in suggestedPosts"
                :key="post.id"
                :href="'#post-' + post.id"
                @click.prevent="scrollToPost(post.id)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 active:bg-indigo-100 transition-colors cursor-pointer group"
              >
                <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow-sm', avatarColor(post.id)]">
                  {{ (post.author_name || 'A').charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-800 truncate group-hover:text-indigo-700 transition-colors leading-snug">
                    {{ post.title }}
                  </p>
                  <p class="text-xs text-gray-400 mt-0.5 truncate">
                    {{ post.author_name }} · {{ timeAgo(post.created_at) }}
                  </p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-500 group-hover:translate-x-0.5 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </a>
            </div>

            <!-- Footer -->
            <div v-if="suggestedPosts.length" class="mx-3 mb-3 px-3 py-2 bg-indigo-50 rounded-xl">
              <p class="text-xs text-indigo-500 text-center font-medium">
                {{ suggestedPosts.length }} post{{ suggestedPosts.length !== 1 ? 's' : '' }} waiting for your thoughts
              </p>
            </div>

          </div>

          <!-- Activity Stats widget -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Your Activity</h3>
            <div class="grid grid-cols-2 gap-2">
              <div class="bg-indigo-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-indigo-600">{{ posts.length }}</p>
                <p class="text-xs text-indigo-400 mt-0.5">Total Posts</p>
              </div>
              <div class="bg-green-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-green-600">{{ (posts.length - suggestedPosts.length < 0 ? 0 : (posts.length - suggestedPosts.length)) }}</p>
                <p class="text-xs text-green-400 mt-0.5">Commented</p>
              </div>
            </div>
          </div>

        </aside>

      </div>
    </div>
  </div>
</template>

<script>
import PostCard from '../components/PostCard.vue';

export default {
  components: { PostCard },
  data() {
    return {
      posts:          [],
      reactionTypes:  [],
      suggestedPosts: [],
      loading:        true,
      sidebarLoading: false,
      error:          '',
    };
  },
  computed: {
    isAdmin()    { return localStorage.getItem('role') === 'admin'; },
    isLoggedIn() { return !!localStorage.getItem('token'); },
  },
  async created() {
    await this.load();
    if (this.isLoggedIn) await this.loadSidebar();
  },
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
      } catch {
        this.error = 'Failed to load posts';
      } finally {
        this.loading = false;
      }
    },

    async loadSidebar() {
      this.sidebarLoading = true;
      try {
        const res = await this.$axios.get('/posts/with/not-commented');
        this.suggestedPosts = res.data.posts || [];
      } catch {
        this.suggestedPosts = [];
      } finally {
        this.sidebarLoading = false;
      }
    },

    scrollToPost(id) {
      const el = document.getElementById('post-' + id);
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.classList.add('ring-2', 'ring-indigo-400', 'ring-offset-2', 'transition-all');
        setTimeout(() => el.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-2'), 1800);
      }
    },

    avatarColor(id) {
      const colors = [
        'bg-gradient-to-br from-indigo-500 to-blue-600',
        'bg-gradient-to-br from-violet-500 to-purple-600',
        'bg-gradient-to-br from-pink-500 to-rose-600',
        'bg-gradient-to-br from-teal-500 to-emerald-600',
        'bg-gradient-to-br from-amber-500 to-orange-600',
        'bg-gradient-to-br from-cyan-500 to-sky-600',
      ];
      return colors[id % colors.length];
    },

    timeAgo(ts) {
      if (!ts) return '';
      const diff = (Date.now() - new Date(ts.replace(' ', 'T'))) / 1000;
      if (diff < 60)    return 'just now';
      if (diff < 3600)  return Math.floor(diff / 60)   + 'm ago';
      if (diff < 86400) return Math.floor(diff / 3600)  + 'h ago';
      return Math.floor(diff / 86400) + 'd ago';
    },
  }
};
</script>