<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="$emit('cancel')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-2">{{ title }}</h3>
      <p class="text-gray-500 text-sm mb-6">{{ message }}</p>
      <div class="flex gap-3 justify-end">
        <button @click="$emit('cancel')" class="px-4 py-2 text-gray-600 border border-gray-200 rounded-xl text-sm hover:bg-gray-50 transition">Cancel</button>
        <button @click="$emit('confirm')" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition">Delete</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: { title: String, message: String },
  emits: ['confirm', 'cancel']
};
</script>
