<template>
  <div class="max-w-6xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">All Users</h1>

    <div v-if="loading" class="text-gray-500">Loading users...</div>
    <div v-if="error" class="text-red-600">{{ error }}</div>

    <table v-if="users.length" class="w-full table-auto border border-gray-300 rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2 text-left">#</th>
          <th class="border px-4 py-2 text-left">Name</th>
          <th class="border px-4 py-2 text-left">Username</th>
          <th class="border px-4 py-2 text-left">Email</th>
          <th class="border px-4 py-2 text-left">Role</th>
          <th class="border px-4 py-2 text-left">Joined</th>
        </tr>
      </thead>
      <tbody>
        <UserCard
          v-for="(user, index) in users"
          :key="user.id"
          :user="user"
          :index="index"
        />
      </tbody>
    </table>
  </div>
</template>

<script>
import UserCard from '../components/UserCard.vue';



export default {
  components: { UserCard },
  data() {
    return {
      users: [],
      loading: true,
      error: ''
    };
  },
  async mounted() {
    try {
      const token = localStorage.getItem('token');
      const res = await this.$axios.get('/admin/users', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      this.users = res.data.users;
    } catch (err) {
      this.error = 'Failed to load users';
    } finally {
      this.loading = false;
    }
  }
};
</script>
