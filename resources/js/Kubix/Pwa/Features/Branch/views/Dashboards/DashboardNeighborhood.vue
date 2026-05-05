<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 p-8 font-sans">
    <!-- Background Effects -->
    <!-- <div class="fixed inset-0 pointer-events-none overflow-hidden">
      <div class="absolute top-0 right-0 w-[1000px] h-[800px] bg-gradient-to-br from-cyan-500/10 via-transparent to-transparent blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-gradient-to-tr from-violet-500/10 via-transparent to-transparent blur-3xl"></div>
      <div class="absolute inset-0 bg-[linear-gradient(rgba(6,182,212,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(6,182,212,0.03)_1px,transparent_1px)] bg-[size:64px_64px]"></div>
    </div> -->

    <div class="relative max-w-[2500px] mx-auto space-y-8">
      
      <!-- Header Section -->
      <!-- <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-8 border-b border-zinc-800/50">
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-400 to-violet-500 rounded-lg flex items-center justify-center shadow-lg shadow-cyan-500/20">
              <svg class="w-7 h-7 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
              </svg>
            </div>
            <div>
              <h1 class="text-4xl font-black tracking-tighter bg-gradient-to-r from-cyan-400 via-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                KubyxSyS
              </h1>
              <p class="text-xs font-mono text-zinc-500 tracking-wider mt-0.5">SUPERADMIN / CONTROL_PLANE</p>
            </div>
          </div>
          <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-2">
              <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
              </span>
              <span class="font-mono text-zinc-400">SYSTEM_ONLINE</span>
            </div>
            <span class="text-zinc-700">•</span>
            <span class="font-mono text-zinc-500">Engine v4.8.2</span>
            <span class="text-zinc-700">•</span>
            <span class="font-mono text-zinc-500">{{ currentTime }}</span>
          </div>
        </div>
        
        <div class="flex items-center gap-3">
          <button class="px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 rounded-lg text-sm font-bold transition-all duration-200 hover:border-zinc-600">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Export Data
            </span>
          </button>
          <button class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-violet-500 hover:from-cyan-400 hover:to-violet-400 rounded-lg text-sm font-black text-zinc-950 transition-all duration-200 shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
              </svg>
              New Company
            </span>
          </button>
        </div>
      </header> -->

      <!-- KPI Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div 
          v-for="(kpi, index) in kpiMetrics" 
          :key="index"
          class="group relative bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-2xl p-6 hover:border-zinc-700 transition-all duration-300 overflow-hidden"
          :style="{ animationDelay: `${index * 100}ms` }"
        >
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500 blur-2xl" :class="kpi.gradientColor"></div>
          
          <div class="relative">
            <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" :class="kpi.iconBg">
                <span class="text-2xl">{{ kpi.icon }}</span>
              </div>
              <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold" :class="kpi.trendBg">
                <svg v-if="kpi.trendDirection === 'up'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                <span>{{ kpi.trend }}</span>
              </div>
            </div>
            
            <div class="space-y-2">
              <p class="text-xs font-mono text-zinc-500 uppercase tracking-widest">{{ kpi.label }}</p>
              <p class="text-3xl font-black tracking-tight">{{ kpi.value }}</p>
              <p class="text-xs text-zinc-500">{{ kpi.subtitle }}</p>
            </div>

            <div class="mt-4 h-1.5 bg-zinc-800 rounded-full overflow-hidden">
              <div 
                class="h-full rounded-full transition-all duration-1000" 
                :class="kpi.progressBg"
                :style="{ width: kpi.progress + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Companies Table -->
        <section class="lg:col-span-8 bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-2xl overflow-hidden">
          <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
            <div>
              <h2 class="text-lg font-black tracking-tight">Active Companies</h2>
              <p class="text-xs text-zinc-500 mt-1 font-mono">{{ companies.length }} registered tenants</p>
            </div>
            <div class="flex items-center gap-2">
              <button class="p-2 hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </button>
              <button class="p-2 hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-zinc-950/50 text-zinc-400">
                <tr class="text-xs font-mono uppercase tracking-wider">
                  <th class="px-6 py-4 text-left font-black">Company</th>
                  <th class="px-6 py-4 text-center font-black">Branches</th>
                  <th class="px-6 py-4 text-center font-black">Users</th>
                  <th class="px-6 py-4 text-left font-black">Solutions</th>
                  <th class="px-6 py-4 text-center font-black">Status</th>
                  <th class="px-6 py-4 text-right font-black">MRR</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-zinc-800">
                <tr 
                  v-for="company in companies" 
                  :key="company.id"
                  class="hover:bg-zinc-800/30 transition-colors group"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div 
                        class="w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm shadow-lg"
                        :style="{ background: company.gradient }"
                      >
                        {{ company.initials }}
                      </div>
                      <div>
                        <p class="font-bold text-zinc-100 group-hover:text-cyan-400 transition-colors">{{ company.name }}</p>
                        <p class="text-xs font-mono text-zinc-500">{{ company.domain }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-800 text-sm font-bold">
                      {{ company.branches }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-800 text-sm font-bold">
                      {{ company.users }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex gap-1.5 flex-wrap">
                      <span 
                        v-for="solution in company.solutions" 
                        :key="solution"
                        class="px-2.5 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-md text-xs font-bold text-cyan-400 font-mono"
                      >
                        {{ solution }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span 
                      class="inline-flex px-3 py-1 rounded-full text-xs font-bold font-mono"
                      :class="company.statusClass"
                    >
                      {{ company.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span class="text-base font-black text-zinc-100">${{ company.mrr.toLocaleString() }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="p-4 border-t border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
            <span class="font-mono">Showing 1-8 of {{ companies.length }}</span>
            <div class="flex gap-2">
              <button class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 rounded-lg font-bold transition-colors">Previous</button>
              <button class="px-3 py-1.5 bg-cyan-500 text-zinc-950 rounded-lg font-bold">1</button>
              <button class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 rounded-lg font-bold transition-colors">2</button>
              <button class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 rounded-lg font-bold transition-colors">Next</button>
            </div>
          </div>
        </section>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-6">
          
          <!-- Solutions Registry -->
          <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-black tracking-tight">Solutions Registry</h3>
              <span class="px-2.5 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-full text-xs font-bold text-cyan-400 font-mono">
                {{ solutions.length }} active
              </span>
            </div>
            
            <div class="space-y-4">
              <div 
                v-for="solution in solutions" 
                :key="solution.id"
                class="group p-4 bg-zinc-950/50 hover:bg-zinc-800/30 border border-zinc-800 hover:border-zinc-700 rounded-xl transition-all duration-200 cursor-pointer"
              >
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center shadow-lg">
                      <span class="text-zinc-950 font-black">{{ solution.name[0] }}</span>
                    </div>
                    <div>
                      <p class="font-bold text-zinc-100 group-hover:text-cyan-400 transition-colors">{{ solution.name }}</p>
                      <p class="text-xs font-mono text-zinc-500">{{ solution.version }}</p>
                    </div>
                  </div>
                  <div class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                  </div>
                </div>
                
                <div class="flex flex-wrap gap-1.5 mb-3">
                  <span 
                    v-for="component in solution.components" 
                    :key="component"
                    class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 rounded text-[10px] font-mono text-zinc-400 uppercase"
                  >
                    {{ component }}
                  </span>
                </div>
                
                <div class="flex items-center justify-between text-xs">
                  <span class="font-mono text-zinc-500">{{ solution.deployments }} deployments</span>
                  <button class="text-cyan-400 hover:text-cyan-300 font-bold">
                    View →
                  </button>
                </div>
              </div>
            </div>

            <button class="w-full mt-4 py-3 border-2 border-dashed border-zinc-800 hover:border-cyan-500/30 hover:bg-cyan-500/5 rounded-xl text-zinc-500 hover:text-cyan-400 text-sm font-bold transition-all duration-200">
              <span class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Deploy New Solution
              </span>
            </button>
          </div>

          <!-- System Health -->
          <div class="bg-gradient-to-br from-cyan-500/10 via-violet-500/10 to-fuchsia-500/10 backdrop-blur border border-cyan-500/20 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-lg font-black tracking-tight">System Health</h3>
            </div>
            
            <div class="space-y-4">
              <div v-for="metric in systemMetrics" :key="metric.label">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-xs font-mono text-zinc-400 uppercase">{{ metric.label }}</span>
                  <span class="text-sm font-black text-zinc-100">{{ metric.value }}</span>
                </div>
                <div class="h-2 bg-zinc-900/50 rounded-full overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all duration-1000"
                    :class="metric.barColor"
                    :style="{ width: metric.percentage + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-black tracking-tight mb-6">Recent Activity</h3>
            
            <div class="space-y-4">
              <div 
                v-for="activity in recentActivities" 
                :key="activity.id"
                class="flex gap-3 pb-4 border-b border-zinc-800 last:border-0 last:pb-0"
              >
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :class="activity.iconBg">
                  <span class="text-lg">{{ activity.icon }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-bold text-zinc-100 mb-1">{{ activity.title }}</p>
                  <p class="text-xs text-zinc-500 mb-2">{{ activity.description }}</p>
                  <span class="text-[10px] font-mono text-zinc-600">{{ activity.time }}</span>
                </div>
              </div>
            </div>
          </div>

        </aside>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// Current Time
const currentTime = ref('');
const updateTime = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  updateTime();
  const interval = setInterval(updateTime, 1000);
  onUnmounted(() => clearInterval(interval));
});

// KPI Metrics
const kpiMetrics = ref([
  {
    icon: '🏢',
    label: 'Active Companies',
    value: '847',
    subtitle: '+68 this month',
    trend: '+12.5%',
    trendDirection: 'up',
    trendBg: 'bg-green-500/10 text-green-400',
    iconBg: 'bg-green-500/10',
    gradientColor: 'from-green-500 to-emerald-500',
    progress: 75,
    progressBg: 'bg-gradient-to-r from-green-500 to-emerald-500'
  },
  {
    icon: '🏪',
    label: 'Total neighborhoods',
    value: '28/52',
    subtitle: 'Across all companies',
    trend: '+8.3%',
    trendDirection: 'up',
    trendBg: 'bg-cyan-500/10 text-cyan-400',
    iconBg: 'bg-cyan-500/10',
    gradientColor: 'from-cyan-500 to-blue-500',
    progress: 82,
    progressBg: 'bg-gradient-to-r from-cyan-500 to-blue-500'
  },
  {
    icon: '👥',
    label: 'Total Users',
    value: '15,847',
    subtitle: '+156 this week',
    trend: '+5.2%',
    trendDirection: 'up',
    trendBg: 'bg-violet-500/10 text-violet-400',
    iconBg: 'bg-violet-500/10',
    gradientColor: 'from-violet-500 to-purple-500',
    progress: 68,
    progressBg: 'bg-gradient-to-r from-violet-500 to-purple-500'
  },
  {
    icon: '💰',
    label: 'Monthly Revenue',
    value: '$328.5k',
    subtitle: 'MRR tracking',
    trend: '+24.7%',
    trendDirection: 'up',
    trendBg: 'bg-fuchsia-500/10 text-fuchsia-400',
    iconBg: 'bg-fuchsia-500/10',
    gradientColor: 'from-fuchsia-500 to-pink-500',
    progress: 91,
    progressBg: 'bg-gradient-to-r from-fuchsia-500 to-pink-500'
  }
]);

// Companies Data
const companies = ref([
  {
    id: 1,
    name: 'TechnoLegal Abogados',
    domain: 'technolegal.com',
    initials: 'TL',
    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    branches: 12,
    users: 156,
    solutions: ['LiberaJuros', 'Contratos'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 5800
  },
  {
    id: 2,
    name: 'PropiedadesRíos',
    domain: 'propiedadesrios.com',
    initials: 'PR',
    gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    branches: 8,
    users: 94,
    solutions: ['AlugApp', 'Pagos'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 4200
  },
  {
    id: 3,
    name: 'Clínica Salud Plus',
    domain: 'saludplus.med',
    initials: 'SP',
    gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    branches: 15,
    users: 342,
    solutions: ['AgendaPro', 'Pacientes'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 8900
  },
  {
    id: 4,
    name: 'Inversiones GlobalFin',
    domain: 'globalfin.io',
    initials: 'GF',
    gradient: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
    branches: 6,
    users: 78,
    solutions: ['LiberaJuros'],
    status: 'TRIAL',
    statusClass: 'bg-amber-500/10 border border-amber-500/20 text-amber-400',
    mrr: 0
  },
  {
    id: 5,
    name: 'ConsultaLex Jurídico',
    domain: 'consultalex.legal',
    initials: 'CL',
    gradient: 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
    branches: 4,
    users: 45,
    solutions: ['Contratos', 'Clientes'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 2100
  },
  {
    id: 6,
    name: 'UrbanSpace Coworking',
    domain: 'urbanspace.io',
    initials: 'US',
    gradient: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
    branches: 7,
    users: 112,
    solutions: ['AlugApp', 'AgendaPro'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 3400
  },
  {
    id: 7,
    name: 'AutoFinance Pro',
    domain: 'autofinance.pro',
    initials: 'AF',
    gradient: 'linear-gradient(135deg, #ff9a56 0%, #ff6a88 100%)',
    branches: 9,
    users: 203,
    solutions: ['LiberaJuros', 'Pagos'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 6700
  },
  {
    id: 8,
    name: 'MediCare Network',
    domain: 'medicare.health',
    initials: 'MN',
    gradient: 'linear-gradient(135deg, #6a11cb 0%, #2575fc 100%)',
    branches: 23,
    users: 567,
    solutions: ['AgendaPro', 'Pacientes', 'Farmacia'],
    status: 'ACTIVE',
    statusClass: 'bg-green-500/10 border border-green-500/20 text-green-400',
    mrr: 12500
  }
]);

// Solutions Registry
const solutions = ref([
  {
    id: 1,
    name: 'LiberaJuros',
    version: 'v2.4.0',
    deployments: 124,
    components: ['User', 'Payment', 'Calculator', 'Contracts']
  },
  {
    id: 2,
    name: 'AlugApp',
    version: 'v1.8.3',
    deployments: 89,
    components: ['User', 'Property', 'Payment', 'Address']
  },
  {
    id: 3,
    name: 'AgendaPro',
    version: 'v3.1.0',
    deployments: 156,
    components: ['User', 'Calendar', 'Notification', 'Booking']
  },
  {
    id: 4,
    name: 'VetPro',
    version: 'v0.8.5',
    deployments: 12,
    components: ['User', 'Pet', 'Health-Records', 'Agenda']
  }
]);

// System Metrics
const systemMetrics = ref([
  { label: 'CPU Usage', value: '42%', percentage: 42, barColor: 'bg-gradient-to-r from-cyan-500 to-blue-500' },
  { label: 'Memory', value: '68%', percentage: 68, barColor: 'bg-gradient-to-r from-violet-500 to-purple-500' },
  { label: 'API Throughput', value: '8.4k RPS', percentage: 84, barColor: 'bg-gradient-to-r from-green-500 to-emerald-500' },
  { label: 'Uptime', value: '99.998%', percentage: 99, barColor: 'bg-gradient-to-r from-fuchsia-500 to-pink-500' }
]);

// Recent Activities
const recentActivities = ref([
  {
    id: 1,
    icon: '🏢',
    title: 'New Company Registered',
    description: 'TechnoLegal Abogados joined the platform',
    time: '5 min ago',
    iconBg: 'bg-green-500/10'
  },
  {
    id: 2,
    icon: '⚡',
    title: 'Solution Activated',
    description: 'PropiedadesRíos enabled AlugApp on 3 branches',
    time: '23 min ago',
    iconBg: 'bg-cyan-500/10'
  },
  {
    id: 3,
    icon: '💰',
    title: 'Payment Processed',
    description: 'Clínica Salud Plus renewed annual subscription',
    time: '1 hour ago',
    iconBg: 'bg-violet-500/10'
  },
  {
    id: 4,
    icon: '👥',
    title: 'Bulk User Import',
    description: '24 users registered in the last 2 hours',
    time: '2 hours ago',
    iconBg: 'bg-fuchsia-500/10'
  }
]);
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(24, 24, 27, 0.5);
}

::-webkit-scrollbar-thumb {
  background: rgba(63, 63, 70, 0.8);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(6, 182, 212, 0.5);
}

/* Smooth animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group {
  animation: slideIn 0.5s ease-out backwards;
}
</style>