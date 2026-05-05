<template>
  <article :class="['mb-8 rounded-xl overflow-hidden border', ui.isDark ? 'bg-slate-900 border-white/5' : 'bg-white border-slate-200 shadow-sm']">
    <div class="p-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-blue-600 overflow-hidden">
          <img src="https://i.pravatar.cc/100?u=9" class="w-full h-full object-cover" />
        </div>
        <div class="flex flex-col">
          <span class="text-xs font-bold leading-none">camidev_vibe</span>
          <span class="text-[10px] opacity-60">Curitiba, PR • 10h</span>
        </div>
      </div>
      <PhDotsThree :size="24" weight="bold" class="cursor-pointer" />
    </div>

    <div class="aspect-square bg-slate-200 dark:bg-slate-800 overflow-hidden">
      <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" />
    </div>

    <div class="p-3">
      <div class="flex items-center gap-4 mb-2">
        <PhHeart :size="26" class="hover:text-red-500 cursor-pointer transition-colors" />
        <PhChatCircle :size="26" class="cursor-pointer" />
        <PhPaperPlaneTilt :size="26" class="cursor-pointer" />
      </div>
      <p class="text-xs font-bold mb-1">1,234 curtidas</p>
      <p class="text-xs">
        <span class="font-bold mr-2">camidev_vibe</span>
        Explorando novos ângulos no bairro hoje... ✨ #KUBIX #Vibe
      </p>
    </div>
  </article>
</template>

<script setup>
import { useUIStore } from '@/Kubix/Pwa/Layouts/store/uiStore'
import { PhDotsThree, PhHeart, PhChatCircle, PhPaperPlaneTilt } from "@phosphor-icons/vue";
const ui = useUIStore();
</script>