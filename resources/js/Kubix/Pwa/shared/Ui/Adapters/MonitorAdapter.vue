<template>
  <component 
    :is="config.component"
    v-bind="{ ...config.props, ...entity }"
    :icon="config.icon ? markRaw(config.icon) : null"
    :label="entity.label || config.label"
  />
</template>

<script setup>
import { computed, markRaw } from 'vue';

// 1. IMPORTACIÓN DE COMPONENTES ÚNICOS
import CounterCard from '../Counters/CounterCard.vue';
import VitalityFlipper from './VitalityFlipper.vue'

// 2. ICONOS
import { 
  PhBuildings, 
  PhLightning, 
  PhFire, 
  PhUsers 
} from '@phosphor-icons/vue';

const props = defineProps({
  entity: {
    type: Object,
    required: true
  }
});

/**
 * REGISTRO DE UI: El cerebro del polimorfismo.
 * Aquí definimos qué componente usar y qué "traje" (props) ponerle.
 */
const uiRegistry = {
  // --- GRUPO COUNTERS (Variante Trend) ---
  companies: {
    component: CounterCard,
    icon: PhBuildings,
    label: 'Total Companies',
    props: {
      variant: 'trend',
      gradientColor: 'from-cyan-500 to-blue-500',
      iconBg: 'bg-cyan-500/10',
      progressBg: 'bg-cyan-500'
    }
  },
  ads: {
    component: CounterCard,
    icon: PhLightning,
    label: 'Active Ads',
    props: {
      variant: 'trend',
      gradientColor: 'from-violet-500 to-purple-500',
      iconBg: 'bg-violet-500/10',
      progressBg: 'bg-violet-500'
    }
  },
  events: {
    component: CounterCard,
    icon: PhFire,
    label: 'Live Events',
    props: {
      variant: 'trend',
      gradientColor: 'from-fuchsia-500 to-pink-500',
      iconBg: 'bg-fuchsia-500/10',
      progressBg: 'bg-fuchsia-500'
    }
  },

  // --- GRUPO COUNTERS (Variante Pulse/Online) ---
  nomads: {
    component: CounterCard,
    icon: PhUsers,
    label: 'Nomads Online',
    props: {
      variant: 'pulse',
      gradientColor: 'from-emerald-500 to-teal-500',
      iconBg: 'bg-emerald-500/10',
      progressBg: 'bg-emerald-500',
      maxCapacity: 1000 // Valor por defecto para nómadas
    }
  },

  // --- GRUPO VITALITY (Componente Especial) ---
  vitality: {
    component: VitalityFlipper,
    props: {} 
  }
};

// Selector dinámico de configuración
const config = computed(() => {
  return uiRegistry[props.entity.type] || uiRegistry['companies'];
});
</script>