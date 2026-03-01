<template>
  <div class="max-w-3xl mx-auto mt-8">
    <div v-if="loading" class="text-gray-500">Loading post...</div>
    <div v-if="error" class="text-red-600">{{ error }}</div>

    <div v-if="post">
      <h1 class="text-2xl font-bold mb-4">{{ post.title }}</h1>
      <p class="text-gray-800 mb-6">{{ post.content }}</p>

      <h2 class="text-xl font-semibold mb-2">Comments</h2>
      <ul class="mb-4">
        <li v-for="comment in comments" :key="comment.id" class="border-b py-2">
          {{ comment.content }}
        </li>
      </ul>

      <CommentBox :postId="post.id" @comment-added="loadComments" />
    </div>
  </div>
</template>

<script>
import CommentBox from '../components/CommentBox.vue';

export default {
  components: {
    CommentBox
  },
  data() {
    return {
      post: null,
      comments: [],
      loading: true,
      error: ''
    };
  },
  async created() {
    await this.loadPost();
    await this.loadComments();
  },
  methods: {
    async loadPost() {
      try {
        const res = await this.$axios.get(`/posts/${this.$route.params.id}`);
        this.post = res.data;
      } catch (e) {
        this.error = 'Failed to load post';
      } finally {
        this.loading = false;
      }
    },
    async loadComments() {
      try {
        const res = await this.$axios.get(`/posts/${this.$route.params.id}/comments`);
        this.comments = res.data;
      } catch (e) {
        this.error = 'Failed to load comments';
      }
    }
  }
}
</script>

<style scoped></style>