<template>
  <!-- <div class="min-h-screen bg-[#080a11] flex flex-col items-center justify-center p-6 font-sans">
    
    <div class="mb-8 relative">
      <div class="absolute inset-0 bg-yellow-500/20 blur-3xl rounded-full"></div>
      <div class="relative inline-flex items-center justify-center p-8 bg-yellow-500/10 border border-yellow-500/20 rounded-full shadow-2xl">
        <i class="fa-solid fa-compass text-6xl text-yellow-500"></i>
      </div>
    </div>

    <div class="text-center max-w-md">
      <h1 class="text-6xl font-black text-white tracking-tighter mb-2">404</h1>
      <h2 class="text-xl font-bold text-white uppercase tracking-[0.3em] mb-4">Página não encontrada</h2>
      <p class="text-gray-500 text-sm leading-relaxed mb-8">
        O recurso solicitado não pôde ser localizado no sistema.
        Verifique o endereço ou volte para o painel principal do <span class="text-indigo-500 font-bold">KUBIX SYS</span>.
      </p>
    </div>

    <div class="flex flex-col sm:flex-row gap-4">
      <button 
        @click="goBack" 
        class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white text-xs font-black uppercase tracking-widest rounded-xl border border-white/10 transition-all active:scale-95"
      >
        <i class="fa-solid fa-arrow-left mr-2"></i> Voltar
      </button>

      <button 
        @click="goHome" 
        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/20 transition-all active:scale-95"
      >
        <i class="fa-solid fa-house mr-2"></i> Ir para o Dashboard
      </button>
    </div>

    <div class="mt-16 opacity-20">
      <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.5em]">Navigation Protocol Active</h3>
    </div>
  </div> -->
</template>

<script setup>
// import { useRouter } from 'vue-router';
// import { useAuthStore } from "@/core/store/auth";

// const router = useRouter();
// const authStore = useAuthStore();

// const goBack = () => router.back();

// const goHome = () => {
//   const role = authStore.role;
//   if (authStore.isSuperAdmin) {
//     router.push({ name: 'kubix.dashboard' });
//   } else if (['branch_manager', 'branch_staff'].includes(role)) {
//     router.push({ name: 'branch.dashboard' });
//   } else {
//     router.push({ name: 'customer.dashboard' });
//   }
// };
</script>

<style scoped>
@keyframes pulse-yellow {
  0% { opacity: 0.5; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.1); }
  100% { opacity: 0.5; transform: scale(1); }
}
.bg-yellow-500\/20 {
  animation: pulse-yellow 4s infinite ease-in-out;
}
</style>
