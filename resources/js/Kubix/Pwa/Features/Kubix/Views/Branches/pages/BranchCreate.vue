<template>
    <div class="w-full mx-auto py-10 z-0 mt-10">
        <WizardBase
            title="Criar Nova Filial"
            subtitle="Complete os passos para inicializar a nova infraestrutura operacional."
            :steps="steps"
            @finish="handleFinish"
        />
    </div>
</template>

<script setup>
import { markRaw, watch, computed, getCurrentInstance } from "vue";
import { useRouter } from "vue-router";
import WizardBase from "@/Kubix/common/Ui/wizards/WizardBase.vue";



// Composable de gestión
import { useBranchWizard } from "../composables/useBranchWizard.js";

// Componentes de los pasos
import BranchForm from "@/Kubix/Pwa/shared/branch/components/BranchForm.vue";
import AddressForm from "@/Kubix/Pwa/shared/address/components/AddressForm.vue";
import Userform from "@/Kubix/Pwa/shared/user/components/UserForm.vue";
import SolutionsForm from "@/Kubix/Pwa/shared/solutions/components/SolutionsForm.vue";


const { proxy } = getCurrentInstance();

const {
    state,
    reset,
    setBranchData,
    setAddressData,
    setManagerData,
    setManagerAddress,
    setSolutions,
} = useBranchWizard();

const router = useRouter();

/**
 * ORQUESTADOR DE PASOS DINÁMICOS
 */
const steps = computed(() => {
    // 1. PASOS BASE: FILIAL
    const workflow = [
        {
            label: "Dados da Filial",
            component: markRaw(BranchForm),
            props: {
                initialData: state.branchData,
                onCompleted: (data) => setBranchData(data),
            },
        },
        {
            label: "Endereço da Filial",
            component: markRaw(AddressForm),
            props: {
                initialData: state.addressData,
                branchType: state.branchData?.type || "physical",
                title: "Localização da Filial",
                showQuestion: true,
                questionTitle: "Responsável",
                questionSubtitle: "Cadastrar gestor agora?",
                questionIcon: "fa-solid fa-user-plus",
                onCompleted: (data) => setAddressData(data),
            },
        },
    ];

    // 2. PASOS DINÁMICOS: MANAGER
    if (state.hasManager) {
        workflow.push({
            label: "Dados do Responsável",
            component: markRaw(Userform),
            props: {
                initialData: state.manager,
                showQuestion: true,
                questionTitle: "Endereço do Gestor?",
                onCompleted: (data) => {
                    const { has_manager_address, ...userData } = data;
                    setManagerData(userData);
                    state.hasManagerAddress = has_manager_address;
                },
            },
        });

        if (state.hasManagerAddress) {
            workflow.push({
                label: "Endereço Pessoal",
                component: markRaw(AddressForm),
                props: {
                    initialData: state.managerAddress,
                    branchType: "physical",
                    title: "Endereço Pessoal do Manager",
                    showQuestion: false,
                    onCompleted: (data) => setManagerAddress(data),
                },
            });
        }
    }

    // 3. PASO FINAL: SOLUCIONES (Siempre al final del array)
    workflow.push({
        label: "Configuração de Soluções",
        component: markRaw(SolutionsForm),
        props: {
            initialData: state.solutions,
            onCompleted: (data) => {
                // Capturamos el array de IDs enviado por el componente
                setSolutions(data.solution_ids);
            },
        },
    });

    return workflow;
});

/**
 * ENVÍO FINAL AL BACKEND
 */
const handleFinish = async () => {
    const formData = new FormData();
    
    // 1. Clonamos los datos para no ensuciar el estado de la UI
    const payload = JSON.parse(JSON.stringify(state));

    // 2. EXTRACCIÓN DE BINARIOS (Aquí es donde ocurre la magia)
    // Buscamos los archivos reales que están en el state original (no en el clonado)
    if (state.branchData.branding.logo instanceof File) {
        formData.append('branding_logo', state.branchData.branding.logo);
        // Limpiamos el JSON para que no pese innecesariamente
        payload.branchData.branding.logo = null;
    }

    if (state.branchData.branding.watermark instanceof File) {
        formData.append('branding_watermark', state.branchData.branding.watermark);
        payload.branchData.branding.watermark = null;
    }

    // 3. AGREGAR EL RESTO DE LOS DATOS
    // Enviamos todo el árbol (branch, address, manager, solutions) como un solo campo JSON
    formData.append('data', JSON.stringify(payload));

    // 4. LOG DE VERIFICACIÓN STOICO
    console.group("🚀 Verificación de Envío");
    console.log("📄 Datos Estructurados:", payload);
    console.log("🖼️ Archivo Logo:", formData.get('branding_logo'));
    console.log("🖼️ Archivo Watermark:", formData.get('branding_watermark'));
    console.groupEnd();

    // Ahora sí, enviamos el formData al Service
    // await branchService.create(formData);
};


</script>