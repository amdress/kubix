<template>
  <aside class="w-[320px] hidden xl:block sticky top-20 h-fit">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-full bg-slate-300 overflow-hidden">
           <img src="https://i.pravatar.cc/150?u=me" class="w-full h-full object-cover" />
        </div>
        <div class="flex flex-col">
          <span class="text-sm font-bold">seu_usuario</span>
          <span class="text-xs opacity-60">Nome de Exibição</span>
        </div>
      </div>
      <button class="text-xs font-bold text-blue-500 hover:text-white transition-colors">Mudar</button>
    </div>

    <div class="flex justify-between items-center mb-4">
      <span class="text-sm font-bold opacity-50 uppercase tracking-tighter">Sugestões para você</span>
      <button class="text-xs font-bold">Ver tudo</button>
    </div>

    <div class="space-y-4">
      <div v-for="i in 5" :key="i" class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden">
            <img :src="`https://i.pravatar.cc/100?u=sug${i}`" class="w-full h-full object-cover" />
          </div>
          <div class="flex flex-col">
            <span class="text-[13px] font-bold">vizinho_pro_{{ i }}</span>
            <span class="text-[10px] opacity-40">Seguido por joao_dev</span>
          </div>
        </div>
        <button class="text-xs font-bold text-blue-500">Seguir</button>
      </div>
    </div>

    <footer class="mt-8 text-[10px] opacity-30 uppercase tracking-widest leading-loose">
      Sobre • Ajuda • Imprensa • API • Carreiras • Privacidade • Termos
      <div class="mt-4">© 2026 KUBIX FROM PARANÁ</div>
    </footer>
  </aside>
</template>