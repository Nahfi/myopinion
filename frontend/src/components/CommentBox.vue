<template>
  <div class="mt-4">
    <div class="flex gap-3">
      <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
        {{ userInitial }}
      </div>
      <div class="flex-1">
        <textarea
          v-model="comment"
          :disabled="submitting"
          rows="2"
          placeholder="Write a comment… (reviewed before publishing)"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none bg-gray-50 focus:bg-white transition"
        ></textarea>
        <div class="flex items-center justify-between mt-2">
          <span class="text-xs text-amber-600 flex items-center gap-1">
            ⚠️ Comments need admin approval before appearing
          </span>
          <button
            @click="submit"
            :disabled="submitting || !comment.trim()"
            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-1"
          >
            <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ submitting ? 'Posting…' : 'Post' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="submitted" class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 flex items-center gap-2">
      ✅ Comment submitted! It will appear after admin approval.
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification';
const toast = useToast();
export default {
  props: { postId: Number },
  emits: ['comment-added'],
  data() { return { comment: '', submitting: false, submitted: false }; },
  computed: {
    userInitial() { return (localStorage.getItem('userName') || 'U').charAt(0).toUpperCase(); }
  },
  methods: {
    async submit() {
      if (!this.comment.trim()) return;
      this.submitting = true;
      try {
        await this.$axios.post(`/posts/${this.postId}/comments`, { content: this.comment });
        this.comment = '';
        this.submitted = true;
        this.$emit('comment-added');
        setTimeout(() => { this.submitted = false; }, 5000);
      } catch (e) {
        toast.error(e.response?.data?.message || 'Failed to post comment');
      } finally {
        this.submitting = false;
      }
    }
  }
};
</script>
