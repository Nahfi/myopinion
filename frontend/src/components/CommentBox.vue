<template>
  <div class="mt-4">
    <h3 class="font-semibold text-lg mb-2">Add a Comment</h3>
    <textarea v-model="comment" class="w-full border rounded p-2" rows="3" placeholder="Write your comment..."></textarea>
    <button @click="submitComment" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Submit</button>
  </div>
</template>

<script>
export default {
  props: {
    postId: Number
  },
  data() {
    return {
      comment: ''
    };
  },
  methods: {
    async submitComment() {
      try {
        const token = localStorage.getItem('token');
        const response = await this.$axios.post(`/posts/${this.postId}/comments`, {
          content: this.comment
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        this.comment = '';
        this.$emit('comment-added', response.data);
        window.reload
      } catch (error) {
        alert('Failed to post comment');
      }
    }
  }
}
</script>

<style scoped></style>
