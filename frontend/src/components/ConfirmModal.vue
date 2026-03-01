<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-lg w-full max-w-sm">
      <h2 class="text-lg font-semibold mb-4">{{ title }}</h2>
      <p class="text-sm text-gray-600 mb-6">{{ message }}</p>
      <div class="flex justify-end space-x-3">
        <button @click="$emit('cancel')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
          Cancel
        </button>
        <button @click="$emit('confirm')" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
          Confirm
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: { type: String, default: 'Confirm Action' },
    message: { type: String, default: 'Are you sure you want to proceed?' }
  }
};
</script>
