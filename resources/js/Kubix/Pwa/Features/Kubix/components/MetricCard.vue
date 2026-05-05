<template>
  <div
    class="relative rounded-2xl p-6 shadow-lg text-white
           overflow-hidden transition hover:scale-[1.03]"
    :class="variantClass"
  >
    <div class="flex justify-between items-start mb-4">
      <span class="text-xs font-bold uppercase opacity-80">
        {{ title }}
      </span>
      <i :class="`fa-solid ${icon} text-xl`" />
    </div>

    <div class="text-3xl font-black">
      {{ value }}
    </div>

    <div class="text-xs opacity-80 mt-1">
      {{ subtitle }}
    </div>

    <!-- PROGRESS -->
    <div v-if="progress !== null" class="mt-4 h-1 bg-white/30 rounded">
      <div
        class="h-1 bg-white rounded"
        :style="{ width: progress + '%' }"
      />
    </div>

    <!-- PULSE -->
    <span
      v-if="pulse"
      class="absolute inset-0 rounded-2xl animate-pulse bg-white/5"
    />
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  title: String,
  value: [String, Number],
  subtitle: String,
  icon: String,
  variant: String,
  progress: {
    type: Number,
    default: null,
  },
  pulse: Boolean,
});

const variantClass = computed(() => {
  switch (props.variant) {
    case "primary":
      return "bg-gradient-to-br from-blue-600 to-blue-800";
    case "success":
      return "bg-gradient-to-br from-green-600 to-green-800";
    case "warning":
      return "bg-gradient-to-br from-yellow-500 to-orange-600";
    case "danger":
      return "bg-gradient-to-br from-red-600 to-red-800";
    default:
      return "bg-gradient-to-br from-zinc-700 to-zinc-900";
  }
});
</script>
