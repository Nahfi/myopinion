<template>
  <div class="max-w-3xl mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-6">All Posts</h1>
    <div v-if="loading" class="text-gray-500">Loading posts...</div>
    <div v-if="error" class="text-red-600">{{ error }}</div>
    <PostCard v-for="post in posts" :key="post.id" :post="post" />
  </div>
</template>

<script>
import PostCard from '../components/PostCard.vue';

export default {
  components: {
    PostCard
  },
  data() {
    return {
      posts: [],
      loading: true,
      error: ''
    };
  },
  async created() {
    try {
      const res = await this.$axios.get('/posts');
      this.posts = res.data.posts;
    } catch (e) {
      this.error = 'Failed to load posts';
    } finally {
      this.loading = false;
    }
  }
}
</script>

<style scoped></style>