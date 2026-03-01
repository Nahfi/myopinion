<template>
  <div class="group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-0.5 mb-5">

    <!-- Header -->
    <div class="p-5 pb-3 flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div :class="['w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm', bgColor]">
          {{ authorInitial }}
        </div>
        <div>
          <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ post.title }}</h3>
          <div class="text-xs text-gray-400 mt-0.5">
            <span class="font-medium text-gray-500">{{ post.author_name || post.author }}</span>
            · <span>{{ timeAgo }}</span>
          </div>
        </div>
      </div>

      <!-- Admin actions -->
      <div v-if="isAdmin" class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
        <button @click="$router.push('/admin/create-post?id=' + post.id)"
          class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center text-sm transition">✏️</button>
        <button @click="showDeleteConfirm = true"
          class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center text-sm transition">🗑</button>
      </div>
    </div>

    <!-- Content -->
    <div class="px-5 pb-4">
      <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ post.content }}</p>
      <div v-if="post.image_url && !imageError" class="mt-4 rounded-xl overflow-hidden">
        <img :src="fullImageUrl" :alt="post.title"
          class="w-full max-h-72 object-cover cursor-zoom-in hover:opacity-95 transition"
          @error="imageError = true" @click="showImg = true" />
      </div>
    </div>

    <!-- Reaction summary -->
    <div v-if="totalReactions > 0" class="px-5 pb-2 flex items-center gap-1 flex-wrap">
      <span v-for="t in reactionSummary" :key="t.id"
        class="flex items-center gap-1 text-xs bg-gray-100 rounded-full px-2 py-0.5">
        {{ t.emoji }} <span class="font-medium text-gray-500">{{ t.count }}</span>
      </span>
      <span class="text-xs text-gray-400 ml-1">{{ totalReactions }} reaction{{ totalReactions !== 1 ? 's' : '' }}</span>
    </div>

    <!-- Action bar -->
    <div class="px-5 py-3 border-t border-gray-50 flex items-center gap-2">

      <!-- Reaction picker -->
      <div class="relative">
        <button @click="togglePicker"
          :class="['flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition hover:scale-105',
            userReactionType ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
          <span class="text-base">{{ userReactionType ? userReactionType.emoji : '👍' }}</span>
          <span>{{ userReactionType ? userReactionType.name : 'React' }}</span>
        </button>

        <!-- Emoji popup -->
        <div v-if="showPicker"
          class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 rounded-2xl shadow-xl p-2 flex gap-1 z-50"
          style="animation: pop 0.12s ease-out;">
          <button v-for="type in reactionTypes" :key="type.id" @click="react(type)"
            :class="['p-2 rounded-xl hover:bg-indigo-50 text-xl transition hover:scale-125',
              userReactionType?.id === type.id ? 'bg-indigo-100 ring-2 ring-indigo-400' : '']"
            :title="type.name">
            {{ type.emoji }}
          </button>
        </div>
      </div>

      <!-- Comments button -->
      <button @click="toggleComments"
        :class="['flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition hover:scale-105',
          showComments ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']">
        💬 {{ post.total_comments }} Comment{{ post.total_comments != 1 ? 's' : '' }}
      </button>

      <!-- Share -->
      <button @click="share"
        class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-green-50 hover:text-green-700 text-sm font-medium transition hover:scale-105 ml-auto">
        🔗 Share
      </button>
    </div>

    <!-- Comments section -->
    <div v-if="showComments" class="border-t border-gray-50 p-5">
      <CommentBox :postId="post.id" @comment-added="onCommentAdded" />

      <div v-if="loadingComments" class="text-center py-6 text-gray-400 text-sm">Loading comments…</div>

      <div v-else-if="comments.length" class="mt-4 space-y-3">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
          {{ comments.length }} approved comment{{ comments.length !== 1 ? 's' : '' }}
        </p>
        <div v-for="c in comments" :key="c.id" class="flex gap-3">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
            {{ (c.author_name || c.author || 'A').charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 bg-gray-50 rounded-2xl px-4 py-3">
            <div class="flex items-center justify-between mb-1">
              <span class="font-semibold text-gray-800 text-sm">{{ c.author_name || c.author }}</span>
              <span class="text-xs text-gray-400">{{ formatTime(c.created_at) }}</span>
            </div>
            <p class="text-gray-700 text-sm leading-relaxed">{{ c.content }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-8">
        <div class="text-4xl mb-2">💬</div>
        <p class="text-gray-400 text-sm">No approved comments yet. Be the first!</p>
      </div>
    </div>

    <!-- Image full-screen -->
    <div v-if="showImg" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50" @click="showImg=false">
      <img :src="fullImageUrl" :alt="post.title" class="max-w-4xl max-h-screen p-4 object-contain rounded-xl" />
    </div>

    <ConfirmModal v-if="showDeleteConfirm" title="Delete Post"
      message="Are you sure? This cannot be undone."
      @confirm="confirmDelete" @cancel="showDeleteConfirm=false" />
  </div>
</template>

<script>
import CommentBox from './CommentBox.vue';
import ConfirmModal from './ConfirmModal.vue';
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  components: { CommentBox, ConfirmModal },
  props: ['post', 'reactionTypes'],
  emits: ['remove'],
  data() {
    return {
      showComments: false,
      comments: [],
      loadingComments: false,
      commentsLoaded: false,
      userReactionType: null,
      showPicker: false,
      imageError: false,
      showImg: false,
      showDeleteConfirm: false,
      colors: [
        'bg-gradient-to-br from-indigo-500 to-blue-600',
        'bg-gradient-to-br from-violet-500 to-purple-600',
        'bg-gradient-to-br from-pink-500 to-rose-600',
        'bg-gradient-to-br from-teal-500 to-emerald-600',
        'bg-gradient-to-br from-amber-500 to-orange-600',
        'bg-gradient-to-br from-cyan-500 to-sky-600',
      ]
    };
  },
  computed: {
    isAdmin() { return localStorage.getItem('role') === 'admin'; },
    bgColor() { return this.colors[this.post.id % this.colors.length]; },
    authorInitial() { return (this.post.author_name || this.post.author || 'A').charAt(0).toUpperCase(); },
    fullImageUrl() {
      const url = this.post.image_url;
      if (!url) return '';
      if (url.startsWith('http')) return url;
      const base = (this.$axios?.defaults?.baseURL || '').replace(/\/$/, '');
      return `${base}/public/${url.replace(/^\//, '')}`;
    },
    timeAgo() {
      if (!this.post.created_at) return '';
      const diff = (Date.now() - new Date(this.post.created_at.replace(' ','T'))) / 1000;
      if (diff < 60) return 'just now';
      if (diff < 3600) return Math.floor(diff/60) + 'm ago';
      if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
      return Math.floor(diff/86400) + 'd ago';
    },
    reactionSummary() {
      return (this.post.reaction_summary || []).map(t => ({
        ...t,
        count: parseInt(t.count || 0)
      })).filter(t => t.count > 0);
    },
    totalReactions() {
      return this.reactionSummary.reduce((sum, r) => sum + r.count, 0);
    }
  },
  mounted() {
    if (this.post.user_reaction_type_id && this.reactionTypes) {
      this.userReactionType = this.reactionTypes.find(t => t.id == this.post.user_reaction_type_id) || null;
    }
  },
  methods: {
    togglePicker() {
      if (!localStorage.getItem('token')) { this.$router.push('/login'); return; }
      this.showPicker = !this.showPicker;
    },
    async react(type) {
      this.showPicker = false;
      if (!localStorage.getItem('token')) { this.$router.push('/login'); return; }
      try {
        const res = await this.$axios.post(`/reaction/${this.post.id}`, { reaction_type_id: type.id });
        // update the post reaction summary instantly
        this.post.reaction_summary = res.data.types || [];
        if (res.data.action === 'removed') {
          this.userReactionType = null;
          toast.info('Reaction removed');
        } else {
          this.userReactionType = type;
          toast.success('Reacted with ' + type.emoji);
        }
      } catch {
        toast.error('Failed to react');
      }
    },
    async toggleComments() {
      if (!localStorage.getItem('token')) { this.$router.push('/login'); return; }
      this.showComments = !this.showComments;
      if (this.showComments && !this.commentsLoaded) await this.loadComments();
    },
    async loadComments() {
      this.loadingComments = true;
      try {
        const res = await this.$axios.get(`/posts/${this.post.id}/comments`);
        this.comments = res.data.comments || [];
        this.commentsLoaded = true;
      } catch { toast.error('Failed to load comments'); }
      finally { this.loadingComments = false; }
    },
    onCommentAdded() { /* pending — don't add to list */ },
    share() {
      if (navigator.share) navigator.share({ title: this.post.title, url: window.location.href });
      else { navigator.clipboard.writeText(window.location.href); toast.success('Link copied!'); }
    },
    async confirmDelete() {
      this.showDeleteConfirm = false;
      try {
        await this.$axios.delete(`/admin/posts/${this.post.id}`);
        toast.success('Post deleted');
        this.$emit('remove', this.post.id);
      } catch { toast.error('Failed to delete post'); }
    },
    formatTime(ts) {
      if (!ts) return '';
      const d = new Date(ts.replace(' ','T'));
      const diff = (Date.now() - d) / 1000;
      if (diff < 3600) return Math.floor(diff/60) + 'm ago';
      if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
      return d.toLocaleDateString();
    }
  }
};
</script>

<style scoped>
@keyframes pop {
  from { opacity:0; transform:scale(0.85) translateY(6px); }
  to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>