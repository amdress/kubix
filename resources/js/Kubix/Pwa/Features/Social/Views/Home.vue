<template>
  <div :class="['min-h-full flex flex-col transition-colors duration-300', ui.isDark ? 'bg-slate-950' : 'bg-slate-100']">
    
    <BusinessStatus :items="stories" @select="goToProfile" />

    <div class="flex-1 w-full max-w-[950px] mx-auto py-6 px-4 md:px-0 grid grid-cols-1 lg:grid-cols-12 gap-10">
      
      <main class="lg:col-span-6 space-y-10 space-x-10">
        <template v-for="(item, index) in mixedFeed" :key="index">
          
          <article 
            v-if="item.type === 'reel'" 
            class="relative h-[650px] md:h-[700px] rounded-[2.5rem] md:rounded-[3rem] overflow-hidden shadow-2xl border border-white/10 group"
          >
            <video 
              :src="item.media" 
              autoplay muted loop playsinline 
              class="w-full h-full object-cover brightness-[0.85]"
            ></video>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-black/20 p-6 flex flex-col justify-end">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-full border-2 border-white/20 overflow-hidden shadow-lg shadow-black/50">
                  <img :src="item.userLogo" class="w-full h-full object-cover" />
                </div>
                <div class="leading-tight">
                  <span class="text-white font-black uppercase text-[11px] tracking-widest block">{{ item.userName }}</span>
                  <span class="text-blue-400 text-[9px] font-black uppercase tracking-tighter">Agora no Bairro</span>
                </div>
              </div>

              <p class="text-white text-sm md:text-base font-bold leading-tight drop-shadow-md line-clamp-2 mb-6">
                {{ item.caption }}
              </p>
              
              <div class="flex gap-3">
                 <button class="flex-1 h-12 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 text-white font-black text-[10px] uppercase tracking-widest transition-all">
                   Interagir
                 </button>
                 <button class="w-12 h-12 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 text-white flex items-center justify-center transition-all">
                   <PhHeart :size="22" weight="fill" class="text-white" />
                 </button>
              </div>
            </div>
          </article>

          <article 
            v-else-if="item.type === 'ad'" 
            @click="goToProfile(item.companyId)"
            class="p-[1.5px] rounded-[2.5rem] md:rounded-[3rem] bg-gradient-to-br from-white/20 to-transparent shadow-xl cursor-pointer active:scale-[0.98] transition-all"
          >
            <div :class="['rounded-[2.4rem] md:rounded-[2.9rem] overflow-hidden', ui.isDark ? 'bg-slate-900' : 'bg-white shadow-inner']">
              <div class="relative h-64">
                <img :src="item.image" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t" :class="ui.isDark ? 'from-slate-900' : 'from-white'"></div>
                <div class="absolute bottom-4 left-6">
                  <div class="bg-blue-600 px-3 py-1.5 rounded-xl shadow-lg shadow-blue-500/30">
                     <span class="text-white font-black text-[9px] uppercase tracking-widest">{{ item.tag }}</span>
                  </div>
                </div>
              </div>
              <div class="p-7 flex justify-between items-center">
                <div class="min-w-0 pr-4">
                  <h3 :class="['font-black uppercase text-lg tracking-tight leading-none mb-1 truncate', ui.isDark ? 'text-white' : 'text-slate-900']">
                    {{ item.title }}
                  </h3>
                  <p class="text-slate-500 text-[11px] font-bold leading-none">{{ item.subtitle }}</p>
                </div>
                <button class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                  <PhArrowRight :size="24" weight="bold" />
                </button>
              </div>
            </div>
          </article>

        </template>
        <div class="h-20"></div>
      </main>

      <aside class="hidden lg:block lg:col-span-5 relative">
        <div class="sticky top-24 space-y-6">

            <h3>Sugestoes para voce </h3>
          
          <div class="flex items-center gap-4 mb-8">
             <div class="w-14 h-14 rounded-full p-[2px] bg-gradient-to-tr from-blue-500 to-cyan-400">
                <img src="https://ui-avatars.com/api/?name=Amdress+Stark&background=0f172a&color=fff" class="w-full h-full rounded-full object-cover border-2 border-white" />
             </div>
             <div>
                <h4 :class="['font-black text-sm uppercase', ui.isDark ? 'text-white' : 'text-slate-900']">Amdress Stark</h4>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">Premium Developer</p>
             </div>
          </div>

          <div :class="['p-6 rounded-[2rem]', ui.isDark ? 'bg-slate-900/50' : 'bg-white']">
            <h3 :class="['font-black text-xs uppercase tracking-widest mb-4', ui.isDark ? 'text-blue-400' : 'text-blue-600']">
              Notícias do Batel
            </h3>
            <div class="space-y-5">
              <div v-for="n in 3" :key="n" class="group cursor-pointer">
                <p :class="['text-[11px] font-bold leading-snug group-hover:text-blue-500 transition-colors', ui.isDark ? 'text-slate-300' : 'text-slate-700']">
                  Obra na Avenida Batel deve durar até o final da semana. Planeje sua rota.
                </p>
                <span class="text-[9px] text-slate-500 font-bold uppercase mt-1 block">Há 2 horas</span>
              </div>
            </div>
          </div>

          <div class="px-6 text-[9px] text-slate-500 font-bold uppercase tracking-widest flex flex-wrap gap-x-4 gap-y-2 opacity-50">
            <span>Sobre</span>
            <span>Ajuda</span>
            <span>Privacidade</span>
            <span>Kubix © 2026</span>
          </div>
        </div>
      </aside>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/useUIStore";
import { PhArrowRight, PhHeart } from "@phosphor-icons/vue"
import BusinessStatus from '../components/BusinessStatus.vue'

const ui = useUIStore()
const router = useRouter()
const stories = ref([])

// Feed mixto (Reels + Anuncios)
const mixedFeed = ref([
  {
    type: 'reel',
    userName: 'BRUNO DJ',
    userLogo: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=200',
    media: 'https://assets.mixkit.co/videos/preview/mixkit-hand-holding-a-smartphone-at-a-party-night-42410-large.mp4',
    caption: 'O baile no setor 4 já começou! Vem que tá lotado! 🔥🚀 #KUBIX #Batel'
  },
  {
    type: 'ad',
    companyId: 1,
    tag: 'OFERTA DO DIA 🍕',
    title: 'FORNO DI NAPOLI',
    subtitle: 'Pizza grande por R$ 45,00 retirando no local.',
    image: 'https://images.unsplash.com/photo-1593504049359-7b7d92c7385f?q=80&w=800'
  },
  {
    type: 'reel',
    userName: 'ANA CLARA',
    userLogo: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200',
    media: 'https://assets.mixkit.co/videos/preview/mixkit-fresh-vegetables-and-fruits-at-a-market-11554-large.mp4',
    caption: 'Feirinha de quarta-feira no Batel. Tudo fresquinho! 🍎🥬'
  }
])

onMounted(() => {
  // Simulación de datos de la DB
  stories.value = [
    { id: 1, name: 'Pizza', logo: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=100', isActive: true, hasUpdate: true },
    { id: 2, name: 'Burger', logo: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=100', isActive: false, hasUpdate: false },
    { id: 3, name: 'Barber', logo: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=100', isActive: false, hasUpdate: true }
  ]
})

const goToProfile = (id) => {
  router.push({ name: 'app.radar.profile', params: { id: id.toString() } })
}
</script>

<style scoped>
/* Evitar scroll horizontal accidental */
.page-mural {
  overflow-x: hidden;
}

/* Soporte para pantallas táctiles */
article {
  -webkit-tap-highlight-color: transparent;
}
</style>