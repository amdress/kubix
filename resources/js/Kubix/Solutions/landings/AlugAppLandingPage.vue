<template>
  <section class="py-32 px-6 bg-gradient-to-b from-[#080a11] to-[#0d111c] relative overflow-hidden">
    
    <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
      <pre class="text-[10px] text-emerald-500 font-mono">
        {
          "module": "AlugApp",
          "status": "active",
          "automation": true,
          "sync_branch": "enabled"
        }
      </pre>
    </div>

    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-2 gap-16 items-center">
        
        <div class="relative group">
          <div class="absolute -inset-4 bg-emerald-500/10 rounded-[3rem] blur-2xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>
          
          <div class="relative bg-[#0b0e18] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl overflow-hidden">
            <div class="flex justify-between items-center mb-8">
              <div class="flex gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500/20"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500/20"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/20"></div>
              </div>
              <span class="text-[9px] font-black text-emerald-500 bg-emerald-500/10 px-3 py-1 rounded-full uppercase tracking-widest">Live Terminal: AlugApp</span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
              <div class="bg-black/40 p-4 rounded-2xl border border-white/5">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Inadimplência</p>
                <p class="text-2xl font-black text-white">1.2%</p>
                <div class="w-full bg-white/5 h-1 mt-2 rounded-full overflow-hidden">
                  <div class="bg-emerald-500 h-full w-[15%]"></div>
                </div>
              </div>
              <div class="bg-black/40 p-4 rounded-2xl border border-white/5">
                <p class="text-[10px] text-slate-500 uppercase font-bold">Ocupação</p>
                <p class="text-2xl font-black text-white">98%</p>
                <div class="w-full bg-white/5 h-1 mt-2 rounded-full overflow-hidden">
                  <div class="bg-indigo-500 h-full w-[98%]"></div>
                </div>
              </div>
            </div>

            <div class="space-y-3">
              <div v-for="n in 3" :key="n" class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-file-contract text-emerald-500 text-xs"></i>
                  </div>
                  <div>
                    <p class="text-[11px] font-bold text-white">Contrato #882{{n}}</p>
                    <p class="text-[9px] text-slate-500">Sincronizado via Nexus</p>
                  </div>
                </div>
                <span class="text-[9px] font-bold text-emerald-400">ATIVO</span>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-8">
          <div>
            <h2 class="text-emerald-500 text-[11px] font-black uppercase tracking-[0.5em] mb-4 flex items-center gap-3">
              <span class="w-8 h-[1px] bg-emerald-500"></span> Proptech Excellence
            </h2>
            <h3 class="text-5xl md:text-6xl font-black text-white leading-tight">
              ALUGAPP: <br/>
              <span class="font-light italic text-slate-400">O FIM DO ALUGUEL</span> <br/>
              COMPLICADO.
            </h3>
          </div>

          <p class="text-slate-400 text-lg leading-relaxed">
            Esqueça as planilhas de cobrança e a dor de cabeça com vistorias. O <strong>AlugApp</strong> dentro do ecossistema <strong>NexusOne</strong> automatiza o ciclo completo do seu imóvel.
          </p>

          <div class="grid grid-cols-1 gap-6">
            <div class="flex gap-4 group">
              <div class="w-12 h-12 shrink-0 bg-white/5 rounded-2xl flex items-center justify-center border border-white/10 group-hover:border-emerald-500/50 transition-colors">
                <i class="fa-solid fa-wand-magic-sparkles text-emerald-500"></i>
              </div>
              <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-1">Onboarding Instantâneo</h4>
                <p class="text-slate-500 text-sm">Cadastre novos inquilinos e contratos em segundos com validação automática.</p>
              </div>
            </div>

            <div class="flex gap-4 group">
              <div class="w-12 h-12 shrink-0 bg-white/5 rounded-2xl flex items-center justify-center border border-white/10 group-hover:border-emerald-500/50 transition-colors">
                <i class="fa-solid fa-shield-halved text-emerald-500"></i>
              </div>
              <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-1">Garantia e Segurança</h4>
                <p class="text-slate-500 text-sm">Análise de crédito integrada e custódia de documentos criptografada.</p>
              </div>
            </div>
          </div>

          <button class="mt-4 px-8 py-4 bg-emerald-600/10 border border-emerald-500/30 text-emerald-400 font-black rounded-2xl hover:bg-emerald-600 hover:text-white transition-all duration-300 uppercase tracking-[0.2em] text-[10px]">
            Explorar AlugApp
          </button>
        </div>

      </div>
    </div>
  </section>
</template>