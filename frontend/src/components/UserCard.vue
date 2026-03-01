<template>
  <tr class="hover:bg-gray-50">
    <td class="border px-4 py-2">{{ index + 1 }}</td>
    <td class="border px-4 py-2">{{ user.name }}</td>
    <td class="border px-4 py-2">{{ user.username }}</td>
    <td class="border px-4 py-2">{{ user.email }}</td>
    <td class="border px-4 py-2">{{ user.role }}</td>
    <td class="border px-4 py-2">{{ formatDate(user.created_at) }}</td>
  </tr>
</template>

<script>
export default {
  props: ['user', 'index'],
  methods: {
    formatDate(dateStr) {
      const date = new Date(dateStr.replace(' ', 'T'));
      return date.toLocaleDateString();
    }
  }
};
</script>
