<template>
  <nav class="navbar">
    <div class="navbar-left">
      <router-link to="/" class="logo">MyApp</router-link>
    </div>
    <div class="navbar-right">
      <router-link to="/" class="nav-link">Posts</router-link>

      <template v-if="!isAuthenticated">
        <router-link to="/login" class="nav-link">Login</router-link>
        <router-link to="/register" class="nav-link">Register</router-link>
      </template>

      <template v-else>
        <router-link v-if="isAdmin" to="/users" class="nav-link">User List</router-link>
        <router-link v-if="isAdmin" to="/admin/create-post" class="nav-link">Create Post</router-link>
        <button @click="logout" class="nav-link logout-btn">Logout</button>
      </template>
    </div>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      token: null,
      role: null
    };
  },
  computed: {
    isAuthenticated() {
      return !!this.token;
    },
    isAdmin() {
      return this.role === 'admin';
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('role');
      this.token = null;
      this.role = null;
      this.$router.push('/login');
    },
    syncAuth() {
      this.token = localStorage.getItem('token');
      this.role = localStorage.getItem('role');
    }
  },
  created() {
    this.syncAuth();
  },
  watch: {
    $route() {
      this.syncAuth();
    }
  }
};
</script>


<style scoped>
.navbar {
  background-color: #4caf50;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 20px;
  flex-wrap: wrap;
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: white;
  text-decoration: none;
}

.navbar-right {
  display: flex;
  gap: 15px;
  align-items: center;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 4px;
  transition: background 0.2s;
}

.nav-link:hover {
  background-color: #45a049;
}

.logout-btn {
  background-color: transparent;
  border: none;
  color: white;
  font-weight: 500;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 4px;
}

.logout-btn:hover {
  background-color: #c0392b;
}
</style>
