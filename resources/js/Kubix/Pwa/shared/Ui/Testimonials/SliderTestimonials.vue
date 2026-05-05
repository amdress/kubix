<template>
  <section class="mt-5 overflow-hidden">
    <div class="px-6 flex justify-between items-end mb-2">
      <div>
        <p class="text-[5px] font-black uppercase tracking-[0.2em] opacity-40">
          {{ label }}
        </p>
        <h3 class="text-lg font-black tracking-tight">
          {{ title }}
        </h3>
      </div>
      
      <div class="flex gap-2">
        <button 
          @click="prev" 
          class="p-1 opacity-50 hover:opacity-100 transition-opacity"
        >
          <PhCaretLeft size="18" weight="bold" />
        </button>
        <button 
          @click="next" 
          class="p-1 opacity-50 hover:opacity-100 transition-opacity"
        >
          <PhCaretRight size="18" weight="bold" />
        </button>
      </div>
    </div>

    <div class="relative px-6">
      <div 
        ref="slider"
        class="flex gap-4 overflow-x-auto scroll-smooth scrollbar-hide snap-x snap-mandatory"
      >
        <div
          v-for="(t, i) in items"
          :key="i"
          class="min-w-[85%] md:min-w-[40%] lg:min-w-[30%] snap-center"
        >
          <div
            class="rounded-2xl p-6 border shadow-sm transition-all duration-300 h-full"
            :class="isDark 
              ? 'bg-slate-900 border-slate-800 text-white' 
              : 'bg-white border-slate-200 text-slate-900'"
          >
            <div class="flex items-center gap-3 mb-4">
              <img
                :src="t.avatar"
                class="h-12 w-12 rounded-full object-cover shadow-sm"
                :alt="t.name"
              />
              <div>
                <p class="text-sm font-bold leading-tight">{{ t.name }}</p>
                <div class="flex gap-0.5 mt-1">
                  <PhStar
                    v-for="n in 5"
                    :key="n"
                    size="10"
                    :weight="n <= t.rating ? 'fill' : 'thin'"
                    :class="n <= t.rating ? 'text-amber-400' : 'text-slate-300'"
                  />
                </div>
              </div>
            </div>

            <p class="text-sm opacity-80 leading-relaxed italic line-clamp-4">
              "{{ t.comment }}"
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { PhStar, PhCaretLeft, PhCaretRight } from "@phosphor-icons/vue";

const props = defineProps({
  items: { type: Array, required: true },
  title: { type: String, default: 'O que dizem' },
  label: { type: String, default: 'Reputação' },
  isDark: { type: Boolean, default: false },
  autoPlay: { type: Boolean, default: true },
  interval: { type: Number, default: 5000 }
});

const slider = ref(null);
let playInterval = null;

const next = () => {
  if (!slider.value) return;
  const { scrollLeft, clientWidth, scrollWidth } = slider.value;
  // Si está al final, vuelve al inicio
  if (scrollLeft + clientWidth >= scrollWidth - 10) {
    slider.value.scrollTo({ left: 0, behavior: 'smooth' });
  } else {
    slider.value.scrollBy({ left: clientWidth, behavior: 'smooth' });
  }
};

const prev = () => {
  if (!slider.value) return;
  slider.value.scrollBy({ left: -slider.value.clientWidth, behavior: 'smooth' });
};

onMounted(() => {
  if (props.autoPlay) {
    playInterval = setInterval(next, props.interval);
  }
});

onBeforeUnmount(() => {
  if (playInterval) clearInterval(playInterval);
});
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>