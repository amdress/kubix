<template>
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-zinc-950/50 text-zinc-400">
        <tr class="text-xs font-mono uppercase tracking-wider">
          <th class="px-6 py-4 text-left font-black">Company</th>
          <th class="px-6 py-4 text-left font-black">Solutions</th>
          <th class="px-6 py-4 text-center font-black">Status</th>
          <th class="px-6 py-4 text-right font-black">MRR</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-zinc-800">
        <tr v-for="company in rows" :key="company.id" class="hover:bg-zinc-800/30 transition-colors group">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-lg"
                   :style="{ background: company.gradient }">
                {{ company.initials }}
              </div>
              <div>
                <p class="font-bold text-zinc-100 group-hover:text-cyan-400 transition-colors">{{ company.name }}</p>
                <p class="text-xs font-mono text-zinc-500">{{ company.domain }}</p>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex gap-1.5 flex-wrap">
              <span v-for="sol in company.solutions" :key="sol" 
                    class="px-2.5 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-md text-xs font-bold text-cyan-400 font-mono">
                {{ sol }}
              </span>
            </div>
          </td>
          <td class="px-6 py-4 text-center">
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold font-mono" :class="company.statusClass">
              {{ company.status }}
            </span>
          </td>
          <td class="px-6 py-4 text-right">
            <span class="text-base font-black text-zinc-100">${{ company.mrr }}</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  rows: { type: Array, required: true }
});
</script>