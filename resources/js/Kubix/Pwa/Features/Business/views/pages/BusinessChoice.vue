<template>
  <div class="business-choice-container" :style="{ '--primary': context.cityBranding.primary }">
    <header class="choice-header">
      <h1>Meus Negócios</h1>
      <p v-if="businesses.length">Selecione uma unidade para gerenciar</p>
      <p v-else>Você ainda não possui unidades registradas</p>
    </header>

    <div v-if="businesses.length" class="business-grid">
      <div 
        v-for="biz in businesses" 
        :key="biz.id" 
        class="business-card"
        @click="selectBusiness(biz.id)"
      >
        <div class="card-image">
          <img v-if="biz.branding?.logo" :src="biz.branding.logo" :alt="biz.label">
          <div v-else class="img-fallback">{{ biz.label.charAt(0) }}</div>
          
          <div class="card-overlay">
            <span>Acessar Painel</span>
          </div>
        </div>

        <div class="card-info">
          <h3>{{ biz.label }}</h3>
          <div class="location">
            <i class="ph-map-pin"></i>
            <span>{{ biz.sub || 'Unidade Ativa' }}</span>
          </div>
          
          <div class="solutions-badges">
            <span v-for="sol in biz.solutions?.slice(0, 2)" :key="sol.slug" class="badge">
              {{ sol.name }}
            </span>
            <span v-if="biz.solutions?.length > 2" class="badge plus">
              +{{ biz.solutions.length - 2 }}
            </span>
          </div>
        </div>
      </div>

      <button class="add-business-card">
        <div class="add-icon">+</div>
        <span>Registrar Novo Negócio</span>
      </button>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">🏢</div>
      <h2>Comece sua jornada Business</h2>
      <p>Transforme seu negócio local em uma experiência digital no KUBIX.</p>
      <button class="btn-primary">Registrar meu primeiro negócio</button>
    </div>

    <button class="btn-back" @click="workspace.backToSocial()">
      Voltar ao Mural
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/Kubix/core/stores/auth'
import { useWorkspaceStore } from '@/Kubix/core/stores/workspace'
import { useContextStore } from '@/Kubix/core/stores/context'

const auth = useAuthStore()
const workspace = useWorkspaceStore()
const context = useContextStore()

// Obtenemos solo los contextos que son negocios/branches
const businesses = computed(() => auth.myBusinesses)

const selectBusiness = (id) => {
  // Disparamos el switch que ya configuramos (hace el splash y cambia el modo)
  workspace.switchBranch(id)
}
</script>

<style scoped>
.business-choice-container {
  min-height: 100vh;
  padding: 2rem;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.choice-header {
  text-align: center;
  margin-bottom: 3rem;
}

.choice-header h1 { font-size: 2rem; color: #1e293b; margin-bottom: 0.5rem; }
.choice-header p { color: #64748b; }

/* Grid de Cards */
.business-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  width: 100%;
  max-width: 1000px;
}

.business-card {
  background: white;
  border-radius: 1.25rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.business-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
  border-color: var(--primary);
}

.card-image {
  height: 160px;
  background: #f1f5f9;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-image img { width: 100%; height: 100%; object-fit: cover; }

.img-fallback {
  width: 60px;
  height: 60px;
  background: var(--primary);
  color: white;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
}

.business-card:hover .card-overlay { opacity: 1; }
.card-overlay span { color: white; font-weight: 600; border: 2px solid white; padding: 0.5rem 1rem; border-radius: 2rem; }

.card-info { padding: 1.5rem; }
.card-info h3 { margin: 0; font-size: 1.25rem; color: #1e293b; }

.location { display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.875rem; margin-top: 0.5rem; }

.solutions-badges { display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap; }
.badge { background: #f1f5f9; color: #475569; font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 0.5rem; text-transform: uppercase; font-weight: 600; }
.badge.plus { background: var(--primary); color: white; }

/* Add Business Card */
.add-business-card {
  border: 2px dashed #cbd5e1;
  background: transparent;
  border-radius: 1.25rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  min-height: 280px;
  cursor: pointer;
  color: #64748b;
  transition: all 0.3s;
}

.add-business-card:hover { border-color: var(--primary); color: var(--primary); background: #f8fafc; }
.add-icon { font-size: 3rem; font-weight: 200; }

.btn-back { margin-top: 4rem; background: none; border: none; color: #64748b; text-decoration: underline; cursor: pointer; }
</style>