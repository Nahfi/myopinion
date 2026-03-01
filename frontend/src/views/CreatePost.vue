<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-xl bg-white rounded-xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
        {{ isEdit ? 'Edit Post' : 'Create New Post' }}
      </h2>
      <div class="space-y-4">
        <input
          v-model="post.title"
          type="text"
          placeholder="Post Title"
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />

        <textarea
          v-model="post.content"
          rows="6"
          placeholder="Write your content here..."
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
        ></textarea>

        <input
          type="file"
          @change="handleImageUpload"
          class="w-full"
        />

        <button
          @click="handleSubmit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-md shadow-md transition"
        >
          {{ isEdit ? 'Update Post' : 'Publish Post' }}
        </button>
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
      post: {
        title: '',
        content: '',
        image: null,
      },
      isEdit: false,
      postId: null,
    };
  },
  created() {
    const id = this.$route.query.id;
    if (id) {
      this.isEdit = true;
      this.postId = id;
      this.fetchPost(id);
    }
  },
  methods: {
    handleImageUpload(event) {
      this.post.image = event.target.files[0];
    },
    async fetchPost(id) {
      try {
        const res = await this.$axios.get(`/posts/${id}`);
        this.post.title = res.data.title;
        this.post.content = res.data.content;
         
      } catch (err) {
        toast.error('Failed to load post');
      }
    },
    async handleSubmit() {
      if (!this.post.title || !this.post.content) {
        toast.error('Please fill in all required fields.');
        return;
      }

      const formData = new FormData();
      formData.append('title', this.post.title);
      formData.append('content', this.post.content);
      if (this.post.image) {
        formData.append('image', this.post.image);
      }

      try {
        if (this.isEdit) {
          await this.$axios.post(`/posts/${this.postId}?_method=PUT`, formData);
          toast.success('Post updated successfully!');
        } else {
          await this.$axios.post('/posts', formData);
          toast.success('Post created successfully!');
        }
        this.$router.push('/');
      } catch (err) { 
        toast.error('Failed to submit post');
      }
    },
  },
};
</script>

<style scoped>
textarea::placeholder,
input::placeholder {
  color: #999;
}
</style>