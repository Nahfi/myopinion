<template>
  <div class="group bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl border border-white/20 overflow-hidden transition-all duration-500 hover:-translate-y-2 mb-8">
    <!-- Post Header -->
    <div class="relative p-6 pb-4">
      <!-- Gradient Background Overlay -->
      <div :class="['absolute inset-0 opacity-10 rounded-t-2xl', bgColor]"></div>
      
      <div class="relative flex items-start justify-between">
        <div class="flex items-center space-x-4">
          <!-- Author Avatar -->
          <div :class="['w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg', bgColor]">
            {{ getAuthorInitial() }}
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ post.title }}</h3>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>{{ getTimeAgo(post.created_at) }}</span>
            </div>
          </div>
        </div>
        
        <!-- Admin Actions -->
        <div v-if="isAdmin" class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
          <button 
            @click="goToEdit" 
            class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 hover:bg-blue-200 hover:scale-110 transition-all duration-200 flex items-center justify-center group/edit"
            title="Edit Post"
          >
            <svg class="w-5 h-5 group-hover/edit:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
          </button>
          <button 
            @click="deletePost" 
            class="w-10 h-10 rounded-xl bg-red-100 text-red-600 hover:bg-red-200 hover:scale-110 transition-all duration-200 flex items-center justify-center group/delete"
            title="Delete Post"
          >
            <svg class="w-5 h-5 group-hover/delete:rotate-12 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Post Content -->
    <div class="px-6 pb-4">
      <p class="mt-4 text-gray-700 leading-relaxed whitespace-pre-line mb-4 text-base">{{ post.content }}</p>
      
      <!-- Post Image -->
      <div v-if="post.image_url" class="relative rounded-xl overflow-hidden mb-6 group/image">
        <img
          :src="getFullImageUrl(post.image_url)"
          :alt="post.title"
          class="w-full max-h-80 object-cover transition-transform duration-700 group-hover/image:scale-105"
          @error="handleImageError"
          @load="handleImageLoad"
        />
        
        <!-- Image Loading State -->
        <div v-if="imageLoading" class="absolute inset-0 bg-gray-200 flex items-center justify-center">
          <svg class="w-8 h-8 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <!-- Image Error State -->
        <div v-if="imageError" class="absolute inset-0 bg-gray-100 flex flex-col items-center justify-center text-gray-500">
          <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span class="text-sm">Image not found</span>
        </div>

        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover/image:opacity-100 transition-opacity duration-300"></div>
        
        <!-- Image Overlay Actions -->
        <div class="absolute top-4 right-4 opacity-0 group-hover/image:opacity-100 transition-opacity duration-300">
          <button 
            @click="openImageModal"
            class="w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm text-gray-700 hover:bg-white transition-all duration-200 flex items-center justify-center"
            title="View full image"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Post Actions -->
    <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <!-- Reaction Button -->
          <button 
            @click="toggleReaction"
            :class="[
              'flex items-center space-x-2 px-4 py-2.5 rounded-xl font-medium transition-all duration-300 hover:scale-105',
              userReacted 
                ? 'bg-pink-100 text-pink-600 shadow-lg shadow-pink-200/50' 
                : 'bg-white text-gray-600 hover:bg-pink-50 hover:text-pink-600 shadow-md'
            ]"

            
          >
            <svg 
              :class="['w-5 h-5 transition-all duration-300', userReacted ? 'scale-110' : '']" 
              :fill="userReacted ? 'currentColor' : 'none'" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span>{{ reactionCount }}</span>
          </button>

          <!-- Comment Button -->
          <button 
            @click="toggleCommentBox"
            :class="[
              'flex items-center space-x-2 px-4 py-2.5 rounded-xl font-medium transition-all duration-300 hover:scale-105',
              showCommentBox 
                ? 'bg-blue-100 text-blue-600 shadow-lg shadow-blue-200/50' 
                : 'bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 shadow-md'
            ]"
            :disabled="loadingComments"
          >
            <!-- Loading Spinner -->
            <svg v-if="loadingComments" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span>{{ post.total_comments }}</span>
          </button>
        </div>

        <!-- Share Button -->
        <button 
          @click="sharePost"
          class="flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-white text-gray-600 hover:bg-green-50 hover:text-green-600 font-medium transition-all duration-300 hover:scale-105 shadow-md"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
          </svg>
          <span>Share</span>
        </button>
      </div>
    </div>

    <!-- Comments Section -->
    <div v-if="showCommentBox" class="border-t border-gray-100 bg-gradient-to-b from-gray-50/50 to-white">
      <div class="p-6">
        <!-- Comment Input -->
        <CommentBox :postId="post.id" @comment-added="addComment" />
        
        <!-- Loading State -->
        <div v-if="loadingComments" class="flex items-center justify-center py-8">
          <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-600">Loading comments...</span>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="commentsError" class="text-center py-8">
          <svg class="w-12 h-12 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-red-600 text-sm mb-4">Failed to load comments</p>
          <button 
            @click="loadComments" 
            class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors duration-200"
          >
            Try Again
          </button>
        </div>
        
        <!-- Comments List -->
        <div v-else-if="comments.length" class="mt-6 space-y-4">
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-semibold text-gray-900">Comments ({{ comments.length }})</h4>
            <div class="flex items-center space-x-2">
              <button 
                @click="refreshComments"
                class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200 flex items-center space-x-1"
                :disabled="loadingComments"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refresh</span>
              </button>
            </div>
          </div>
          
          <div 
            v-for="(comment, index) in comments" 
            :key="comment.id || index"
            class="flex space-x-3 group/comment"
            :style="{ animationDelay: `${index * 100}ms` }"
          >
            <!-- Comment Avatar -->
            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
              {{ getCommentAuthorInitial(comment) }}
            </div>
            
            <!-- Comment Content -->
            <div class="flex-1 min-w-0">
              <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 group-hover/comment:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between mb-2">
                  <p class="font-medium text-gray-900 text-sm">{{ getCommentAuthor(comment) }}</p>
                  <span class="text-xs text-gray-500">{{ getCommentTime(comment) }}</span>
                </div>
                <p class="text-gray-700 text-sm leading-relaxed">{{ comment.content }}</p>
              </div> 
            </div>
          </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
          <p class="text-gray-500 text-sm">No comments yet. Be the first to comment!</p>
        </div>
      </div>
    </div>

    <!-- Image Modal (Optional) -->
    <div v-if="showImageModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50" @click="closeImageModal">
      <div class="relative max-w-4xl max-h-full p-4">
        <img 
          :src="getFullImageUrl(post.image_url)" 
          :alt="post.title"
          class="max-w-full max-h-full object-contain rounded-lg"
        />
        <button 
          @click="closeImageModal"
          class="absolute top-2 right-2 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <ConfirmModal
    v-if="showDeleteConfirm"
    title="Delete Post"
    message="Are you sure you want to delete this post?"
    @confirm="confirmDelete"
    @cancel="showDeleteConfirm = false"
  />

</template>

<script>
import ConfirmModal from './ConfirmModal.vue';
import CommentBox from './CommentBox.vue';
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  props: ['post'],
  components: { CommentBox,ConfirmModal },
  data() {
    return {
      showCommentBox: false,
      comments: [],
      loadingComments: false,
      commentsError: false,
      commentsLoaded: false,
      userReacted: false,
      reactionCount: 0,
      imageLoading: true,
      imageError: false,
      showImageModal: false,
      showDeleteConfirm: false,
      commentCount: 0,
      colors: [
        'bg-gradient-to-r from-blue-500 to-blue-600',
        'bg-gradient-to-r from-green-500 to-green-600',
        'bg-gradient-to-r from-purple-500 to-purple-600',
        'bg-gradient-to-r from-pink-500 to-pink-600',
        'bg-gradient-to-r from-amber-500 to-amber-600',
        'bg-gradient-to-r from-indigo-500 to-indigo-600',
        'bg-gradient-to-r from-red-500 to-red-600',
        'bg-gradient-to-r from-teal-500 to-teal-600'
      ]
    };
  },
  computed: {
    isAdmin() {
      return localStorage.getItem('role') === 'admin';
    },
    bgColor() {
      const index = this.post.id % this.colors.length;
      return this.colors[index];
    }
  },
  mounted() {
    this.reactionCount = this.post.total_reactions || 0;
    this.userReacted = !!this.post.user_reacted;
  },
  methods: {
   
    getFullImageUrl(imageUrl) {
      if (!imageUrl) return '';
       
      if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
        return imageUrl;
      }
       
      const cleanImageUrl = imageUrl.startsWith('/') ? imageUrl.substring(1) : imageUrl;
       
      const baseURL = this.$axios?.defaults?.baseURL || 'http://opinion.local';
       
      const cleanBaseURL = baseURL.endsWith('/') ? baseURL.slice(0, -1) : baseURL;
      const cleanBaseURLs = cleanBaseURL+'/public'; 
      return `${cleanBaseURLs}/${cleanImageUrl}`;
    },
 
    handleImageLoad() {
      this.imageLoading = false;
      this.imageError = false;
    },

    
    handleImageError() {
      this.imageLoading = false;
      this.imageError = true;
      console.error('Failed to load image:', this.getFullImageUrl(this.post.image_url));
    },
 
    openImageModal() {
      this.showImageModal = true;
      document.body.style.overflow = 'hidden'; 
    },
 
    closeImageModal() {
      this.showImageModal = false;
      document.body.style.overflow = 'auto'; 
    },

    async toggleCommentBox() {
      const token = localStorage.getItem('token');
      if (!token) {
        this.$router.push({ path: '/login', query: { redirect: this.$route.fullPath } });
        return;
      }

      this.showCommentBox = !this.showCommentBox;
      
      if (this.showCommentBox && !this.commentsLoaded) {
        await this.loadComments();
      }
    },

    async loadComments() {
      this.loadingComments = true;
      this.commentsError = false;

      try {
        const response = await this.$axios.get(`/posts/${this.post.id}/comments`);
        this.comments = response.data.comments || [];
        this.commentCount = response.data.count || this.comments.length;
        this.commentsLoaded = true;
        
        if (this.comments.length > 0) {
          console.log(`Loaded ${this.comments.length} comments`);
        }
        
      } catch (error) {
        console.error('Error loading comments:', error);
        this.commentsError = true;
        
        if (toast) {
          toast.error('Failed to load comments');
        }
        
        this.comments = [];
      } finally {
        this.loadingComments = false;
      }
    },

    async refreshComments() {
      this.commentsLoaded = false;
      await this.loadComments();
    },

    async toggleReaction() {
      const token = localStorage.getItem('token'); 
 

      try {
        const res = await this.$axios.post(`/reaction/${this.post.id}`,{
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        if (this.userReacted) {
          this.reactionCount--;
          this.userReacted = false;
          toast.info('Reaction removed');
        } else {
          this.reactionCount++;
          this.userReacted = true;
          toast.success('Reaction added');
        }
      } catch (error) {
        toast.error('Failed to react to post');
      }
    }
,

    sharePost() {
      if (navigator.share) {
        navigator.share({
          title: this.post.title,
          text: this.post.content,
          url: window.location.href
        });
      } else {
        navigator.clipboard.writeText(window.location.href);
        if (toast) {
          toast.success('Link copied to clipboard!');
        }
      }
    },

    goToEdit() {
      this.$router.push(`/admin/create-post?id=${this.post.id}`);
    },

    deletePost() { 
      this.showDeleteConfirm = true;
    },

    async confirmDelete() {
      this.showDeleteConfirm = false;

      try {
        await this.$axios.delete(`/posts/${this.post.id}`);
        toast.success('Post deleted successfully!');
        this.$emit('remove', this.post.id);
      } catch (err) {
        toast.error('Failed to delete post');
      }
    },

    addComment(newComment) {
      this.comments.unshift(newComment);
      this.$forceUpdate();
      
      if (this.$toast) {
        this.$toast.success('Comment added successfully!');
      }
    },

    getAuthorInitial() {
      return this.post.author?.charAt(0)?.toUpperCase() || this.post.title?.charAt(0)?.toUpperCase() || 'A';
    },

    getCommentAuthor(comment) {
      return comment.author || comment.user?.name || comment.username || 'Anonymous';
    },

    getCommentAuthorInitial(comment) {
      const author = this.getCommentAuthor(comment);
      return author.charAt(0).toUpperCase();
    },

    getTimeAgo(postTime) {
      if (!postTime) return 'Just now'; 
      const isoTime = postTime.replace(' ', 'T');
      const postDate = new Date(isoTime);
      const now = new Date();

      if (isNaN(postDate.getTime())) return 'Invalid Date';

      const diffInMs = now - postDate;
      const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
      const diffInHours = Math.floor(diffInMinutes / 60);

      if (diffInMinutes < 1) return 'Just now';
      if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
      if (diffInHours < 24) return `${diffInHours}h ago`;
      if (diffInHours < 48) return 'Yesterday';

      return postDate.toLocaleDateString();
    },

    getCommentTime(comment) {
      if (!comment.created_at) return 'Just now';
      
      const now = new Date();
      const commentDate = new Date(comment.created_at);
      const diffInMinutes = Math.floor((now - commentDate) / (1000 * 60));
      
      if (diffInMinutes < 1) return 'Just now';
      if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
      
      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) return `${diffInHours}h ago`;
      
      return commentDate.toLocaleDateString();
    }
  }
};
</script>

<style scoped>
/* Animation for comments */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group\/comment {
  animation: slideInUp 0.3s ease-out forwards;
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Custom scrollbar for comments */
.space-y-4::-webkit-scrollbar {
  width: 4px;
}

.space-y-4::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.space-y-4::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Glass morphism effect */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

/* Hover animations */
.hover\:scale-105:hover {
  transform: scale(1.05);
}

.hover\:scale-110:hover {
  transform: scale(1.1);
}

.hover\:-translate-y-2:hover {
  transform: translateY(-8px);
}

/* Image hover effect */
.group\/image:hover img {
  transform: scale(1.05);
}

/* Button press effect */
button:active {
  transform: scale(0.95);
}

/* Disabled button styles */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

button:disabled:hover {
  transform: none;
}

/* Modal styles */
.fixed {
  position: fixed;
}

.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

.z-50 {
  z-index: 50;
}
</style>