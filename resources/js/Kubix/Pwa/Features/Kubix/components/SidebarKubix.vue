<template>
    <div class="space-y-8">
        <div>
            <p v-if="!collapsed" class="section-title">Infraestructura</p>

            <router-link
                :to="{ name: 'kubix.branches.create' }"
                class="nav-link"
                active-class="active-link"
            >
                <i class="fas fa-plus-circle w-4 text-center"></i>
                <span v-if="!collapsed">Crear Branch</span>
            </router-link>

            <router-link
                to="/kubix/branches"
                class="nav-link"
                active-class="active-link"
            >
                <i class="fas fa-city w-4 text-center"></i>
                <span v-if="!collapsed">Ver Todas las Branches</span>
            </router-link>
        </div>

        <div>
            <p v-if="!collapsed" class="section-title">SaaS Global</p>

            <router-link
                to="/kubix/solutions"
                class="nav-link"
                active-class="active-link"
            >
                <i class="fas fa-cubes w-4 text-center"></i>
                <span v-if="!collapsed">Soluciones Master</span>
            </router-link>
        </div>
    </div>
</template>

<script setup>
defineProps({
    collapsed: Boolean,
});
</script>

<style scoped>
/* Los estilos se heredan del padre o se definen aquí para los links */
.section-title {
    @apply text-[9px] uppercase tracking-[0.3em] font-black text-white/40 mb-4 px-2;
}
.nav-link {
    @apply flex items-center gap-3 px-3 py-3 rounded-xl font-black text-[10px] uppercase tracking-wide text-white/80 hover:bg-white/10 transition;
}
.active-link {
    @apply bg-white/20 text-white border-l-4 border-white rounded-l-none;
}
</style>
