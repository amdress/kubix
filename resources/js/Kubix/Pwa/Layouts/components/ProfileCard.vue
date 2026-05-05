<template>
  <div class="profile-card" :class="{ 'profile-card--collapsed': collapsed }">
    <!-- HERO BACKGROUND -->
    <div class="profile-card__hero">
      <img 
        v-if="hero" 
        :src="hero" 
        :alt="user.name"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full bg-gradient-to-br from-cyan-500/30 via-slate-900 to-violet-500/30"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 to-transparent"></div>
    </div>

    <!-- CONTENT -->
    <div class="profile-card__content">
      <!-- AVATAR -->
      <div class="profile-card__avatar-wrapper">
        <div class="profile-card__avatar">
          <img 
            :src="user.avatar" 
            :alt="user.name"
            class="w-full h-full object-cover"
          />
        </div>
        <div v-if="showStatus" class="profile-card__status">
          <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
          </span>
        </div>
      </div>

      <!-- INFO (se oculta cuando está colapsado) -->
      <div v-if="!collapsed" class="profile-card__info">
        <h3 class="profile-card__name">{{ user.name }}</h3>
        <p class="profile-card__role">{{ user.role }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  collapsed: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    required: true,
    default: () => ({
      name: 'Usuario',
      role: 'Rol',
      avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop'
    })
  },
  hero: {
    type: String,
    default: null
  },
  showStatus: {
    type: Boolean,
    default: true
  }
});
</script>

<style scoped>
.profile-card {
  @apply relative w-full h-32 overflow-hidden rounded-xl border border-slate-800 bg-slate-950 transition-all duration-300;
}

.profile-card--collapsed {
  @apply h-20;
}

.profile-card__hero {
  @apply absolute inset-0 z-0;
}

.profile-card__content {
  @apply relative z-10 h-full flex items-center justify-center gap-3 p-3;
}

.profile-card--collapsed .profile-card__content {
  @apply justify-center;
}

.profile-card__avatar-wrapper {
  @apply relative flex-shrink-0;
}

.profile-card__avatar {
  @apply w-14 h-14 rounded-full overflow-hidden border-2 border-slate-950 ring-2 ring-cyan-400/50 shadow-lg transition-all;
}

.profile-card--collapsed .profile-card__avatar {
  @apply w-12 h-12;
}

.profile-card__status {
  @apply absolute bottom-0 right-0;
}

.profile-card__info {
  @apply flex-1 min-w-0;
}

.profile-card__name {
  @apply text-sm font-bold text-white truncate leading-tight;
}

.profile-card__role {
  @apply text-xs text-slate-300 truncate mt-0.5 font-mono uppercase tracking-wider;
}
</style>