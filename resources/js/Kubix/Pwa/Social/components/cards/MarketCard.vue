<template>
  <div class="group rounded-2xl overflow-hidden bg-white border border-slate-200 cursor-pointer active:scale-[0.97] transition">

    <div class="relative aspect-[4/5]">
      <img 
        :src="item.image" 
        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
      />

      <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

      <!-- PRECIO -->
      <div class="absolute top-2 left-2 bg-black/70 text-white text-[10px] font-black px-2 py-1 rounded-lg">
        R$ {{ item.price }}
      </div>

      <!-- BARRIO -->
      <div class="absolute bottom-2 left-2 text-white text-[9px] font-bold uppercase opacity-80">
        {{ item.neighborhood }}
      </div>
    </div>

    <div class="p-2">
      <h3 class="text-[11px] font-black truncate">
        {{ item.title }}
      </h3>
    </div>

  </div>
</template>

<script setup>
defineProps({ item: Object })
</script>