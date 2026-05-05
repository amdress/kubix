<template>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Filiais Cadastradas
        </h1>
        <div class="flex items-center space-x-4">
            <input
                type="text"
                placeholder="Filtrar clientes..."
                class="px-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            <a @click="goToCreateBranch"
                class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                Nova Sucursal
            </a>
        </div>
    </div>
</template>
<script setup>
// 1. Importe useRouter do vue-router
import { useRouter } from 'vue-router';

// 2. Inicialize o router
const router = useRouter();

function goToCreateBranch() {
    router.push({ name: 'branch.create' }); 
    
}

</script>