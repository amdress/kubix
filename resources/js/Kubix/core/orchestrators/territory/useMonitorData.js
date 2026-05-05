import { markRaw, ref } from 'vue';
import { 
  PhStorefront, 
  PhUserPlus, 
  PhWarningCircle 
} from "@phosphor-icons/vue";

export function useMonitorData() {
  // MÉTRICAS SUPERIORES: 
  // Las primeras 4 son simples, la 5ta es el objeto complejo para el Flipper.
  const metrics = markRaw([
    { 
      id: 'm1', 
      type: 'companies', 
      value: 128, 
      trend: '12%', 
      trendDirection: 'up', 
      subtitle: 'vs last month'
    },
    { 
      id: 'm2', 
      type: 'ads', 
      value: 450, 
      trend: '5%', 
      trendDirection: 'up', 
      subtitle: 'Active campaigns'
    },
    { 
      id: 'm3', 
      type: 'events', 
      value: 12, 
      trend: '2%', 
      trendDirection: 'down', 
      subtitle: 'Next 24h'
    },
    { 
      id: 'm4', 
      type: 'nomads', 
      value: 342, 
      subtitle: 'Verified in zone',
      maxCapacity: 500 
    },
    { 
      id: 'm5', 
      type: 'vitality', 
      label: 'Territory Vitality',
      // Datos anidados para que el Adapter los pase al Flipper
      gaugeData: { value: 68 },
      revenueData: { 
        value: 15420, 
        trend: 14.5, 
        series: [12000, 13500, 12800, 14000, 14500, 15000, 15420] 
      }
    }
  ]);

  // DATOS DEL SIDEBAR
  const branchHealth = ref([
    { label: 'Infrastructure', value: 'Excellent', percentage: 95, barColor: 'bg-cyan-500' },
    { label: 'Security', value: 'Stable', percentage: 82, barColor: 'bg-emerald-500' },
    { label: 'Connectivity', value: 'High Load', percentage: 45, barColor: 'bg-amber-500' }
  ]);

  const recentActivities = markRaw([
    { id: 1, title: 'New Nomad Registered', time: '2 mins ago', icon: PhUserPlus, iconColor: 'text-emerald-400' },
    { id: 2, title: 'Market Node Update', time: '15 mins ago', icon: PhStorefront, iconColor: 'text-cyan-400' },
    { id: 3, title: 'Network Latency Alert', time: '1h ago', icon: PhWarningCircle, iconColor: 'text-amber-400' }
  ]);

  const mapData = ref([
    { id: 'n1', lat: -25.443, lng: -49.286, title: 'Node Alpha' }
  ]);

  return { metrics, branchHealth, recentActivities, mapData };
}