<template>
    <div
        class="w-full h-full flex flex-col justify-center mt-10 items-center animate-in fade-in duration-500 min-h-0"
    >
        <form
            @submit.prevent="handleSubmit"
            class="flex flex-col w-full max-w-4xl h-full lg:h-auto space-y-6"
        >
            <div class="grid grid-cols-1 gap-6 flex-grow min-h-0">
                <div
                    class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl flex flex-col overflow-y-auto custom-scrollbar"
                >
                    <div class="space-y-6">
                        <div
                            class="border-b border-slate-50 pb-4 flex justify-between items-end"
                        >
                            <div>
                                <h3
                                    class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-800 italic"
                                >
                                    {{ title }}
                                </h3>
                                <p
                                    class="text-[9px] text-slate-400 mt-1 uppercase font-bold"
                                >
                                    Operação:
                                    <span
                                        :class="
                                            isPhysical
                                                ? 'text-orange-500'
                                                : 'text-blue-500'
                                        "
                                        >{{
                                            isPhysical ? "Física" : "Digital"
                                        }}</span
                                    >
                                </p>
                            </div>
                            <div
                                class="text-[10px] font-black text-slate-300 uppercase italic"
                            >
                                Brasil
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <BaseInput
                                v-model="form.country"
                                label="País"
                                disabled
                                class="opacity-60 italic"
                            />
                            <BaseInput
                                v-model="form.state"
                                type="select"
                                label="UF (Estado)"
                                :options="states"
                                @update:modelValue="handleStateChange"
                                required
                            />
                            <BaseInput
                                v-model="form.city"
                                type="select"
                                label="Cidade"
                                :options="cities"
                                :disabled="!form.state || loadingCities"
                                placeholder="Selecione a cidade"
                                required
                            />
                        </div>

                        <template v-if="isPhysical">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative">
                                    <BaseInput
                                        v-model="form.zip_code"
                                        label="CEP"
                                        mask="#####-###"
                                        :disabled="loadingCep"
                                        required
                                    />
                                    <div
                                        v-if="loadingCep"
                                        class="absolute right-3 top-10"
                                    >
                                        <div
                                            class="w-4 h-4 border-2 border-lime-400/20 border-t-lime-400 rounded-full animate-spin"
                                        ></div>
                                    </div>
                                </div>
                                <BaseInput
                                    v-model="form.street"
                                    label="Logradouro / Rua"
                                    class="md:col-span-2"
                                    required
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <BaseInput
                                    v-model="form.number"
                                    label="Número"
                                    required
                                />
                                <BaseInput
                                    v-model="form.complement"
                                    label="Complemento"
                                />
                                <BaseInput
                                    v-model="form.neighborhood"
                                    label="Bairro"
                                    class="md:col-span-2"
                                    required
                                />
                            </div>
                        </template>

                        <div
                            v-if="showQuestion"
                            class="mt-4 p-6 bg-slate-50 rounded-[32px] border border-dashed border-slate-200 flex items-center justify-between group transition-all hover:border-lime-400/50"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-sm text-slate-400 group-hover:text-lime-500 transition-colors"
                                >
                                    <i :class="questionIcon"></i>
                                </div>
                                <div>
                                    <h4
                                        class="text-[10px] font-black uppercase text-slate-800 tracking-wider"
                                    >
                                        {{ questionTitle }}
                                    </h4>
                                    <p
                                        class="text-[9px] text-slate-400 font-bold uppercase"
                                    >
                                        {{ questionSubtitle }}
                                    </p>
                                </div>
                            </div>
                            <label
                                class="relative inline-flex items-center cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    v-model="extraAction"
                                    class="sr-only peer"
                                />
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-lime-500"
                                ></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="shrink-0 flex items-center gap-6 bg-slate-50/50 p-4 rounded-[24px]"
            >
                <button
                    type="button"
                    @click="$emit('prev')"
                    class="px-6 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors"
                >
                    [ Voltar ]
                </button>

                <button
                    type="submit"
                    :disabled="!isValid"
                    class="w-full font-black uppercase text-[10px] tracking-[0.4em] py-5 rounded-2xl transition-all flex items-center justify-center gap-3"
                    :class="
                        isValid
                            ? 'bg-lime-400 text-black shadow-xl hover:bg-lime-500'
                            : 'bg-slate-200 text-slate-400 cursor-not-allowed'
                    "
                >
                    {{
                        isValid
                            ? "Prosseguir ➔"
                            : loadingCep
                              ? "Buscando..."
                              : "Aguardando Dados"
                    }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import BaseInput from "@/Kubix/common/Ui/BaseInput.vue";

const props = defineProps({
    branchType: { type: String, default: "physical" },
    initialData: { type: Object, default: () => ({}) },
    title: { type: String, default: "Localização Nacional" },
    showQuestion: { type: Boolean, default: false },
    questionTitle: { type: String, default: "Responsável" },
    questionSubtitle: { type: String, default: "Cadastrar gestor ahora?" },
    questionIcon: { type: String, default: "fa-solid fa-user-plus" },
});

const emit = defineEmits(["prev", "completed"]);

const loadingCep = ref(false);
const loadingCities = ref(false);
const states = ref([]);
const cities = ref([]);
const extraAction = ref(false);

const isPhysical = computed(() => props.branchType === "physical");

// Formulario reactivo
const form = ref({
    zip_code: props.initialData.zip_code || "",
    street: props.initialData.street || "",
    number: props.initialData.number || "",
    complement: props.initialData.complement || "",
    neighborhood: props.initialData.neighborhood || "",
    city: props.initialData.city || "",
    state: props.initialData.state || "",
    country: "Brasil",
});

// Helper para CEP limpio
const cleanZip = computed(() => {
    return (form.value.zip_code || "").replace(/\D/g, "");
});

// VALIDACIÓN SIMPLIFICADA Y CLARA
const isValid = computed(() => {
    const f = form.value;

    // 1. Validación geográfica básica (SIEMPRE requerida)
    const hasState = !!f.state && f.state.trim().length >= 2;
    const hasCity = !!f.city && f.city.trim().length >= 2;

    // Si NO es física, solo necesita estado y ciudad
    if (!isPhysical.value) {
        const valid = hasState && hasCity;
        console.log("📍 [DIGITAL] Validación:", { hasState, hasCity, valid });
        return valid;
    }

    // Si ES física, necesita TODA la dirección
    const hasZip = cleanZip.value.length === 8;
    const hasStreet = !!f.street && f.street.trim().length >= 3;
    const hasNumber = !!f.number && String(f.number).trim().length >= 1;
    const hasNeighborhood =
        !!f.neighborhood && f.neighborhood.trim().length >= 2;

    const valid =
        hasState &&
        hasCity &&
        hasZip &&
        hasStreet &&
        hasNumber &&
        hasNeighborhood;

    console.log("🏢 [FÍSICA] Validación:", {
        hasState,
        hasCity,
        hasZip: `${hasZip} (${cleanZip.value.length}/8)`,
        hasStreet: `${hasStreet} (${(f.street || "").length}/3+)`,
        hasNumber,
        hasNeighborhood: `${hasNeighborhood} (${(f.neighborhood || "").length}/2+)`,
        RESULTADO: valid,
    });

    return valid;
});

// Watch para CEP
watch(
    () => form.value.zip_code,
    (val) => {
        const clean = (val || "").replace(/\D/g, "");
        console.log("👀 CEP changed:", val, "→ clean:", clean);
        if (isPhysical.value && clean.length === 8) {
            fetchAddress(clean);
        }
    },
);

// Carga inicial de estados
onMounted(async () => {
    console.log("🚀 AddressForm mounted with:", props);
    try {
        const res = await fetch(
            "https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome",
        );
        const data = await res.json();
        states.value = data.map((s) => ({ value: s.sigla, label: s.nome }));
        console.log("✅ Estados cargados:", states.value.length);

        // Si tiene estado inicial, cargar ciudades
        if (form.value.state) {
            console.log(
                "🔄 Cargando ciudades para estado inicial:",
                form.value.state,
            );
            await handleStateChange();
        }
    } catch (e) {
        console.error("❌ Error cargando estados IBGE:", e);
    }
});

const handleStateChange = async () => {
    const selectedState = form.value.state;
    console.log("🗺️ Estado cambiado a:", selectedState);

    if (!selectedState) {
        cities.value = [];
        form.value.city = "";
        return;
    }

    loadingCities.value = true;
    try {
        const res = await fetch(
            `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${selectedState}/municipios?orderBy=nome`,
        );
        const data = await res.json();
        cities.value = data.map((c) => ({ value: c.nome, label: c.nome }));
        console.log("✅ Ciudades cargadas:", cities.value.length);
    } catch (e) {
        console.error("❌ Error cargando ciudades:", e);
    } finally {
        loadingCities.value = false;
    }
};

const fetchAddress = async (cleanCep) => {
    console.log("🔍 Buscando dirección para CEP:", cleanCep);
    loadingCep.value = true;
    try {
        const res = await fetch(`https://viacep.com.br/ws/${cleanCep}/json/`);
        const data = await res.json();
        console.log("📬 Respuesta ViaCEP:", data);

        if (!data.erro) {
            form.value.state = data.uf;
            await handleStateChange(); // Cargar ciudades primero
            form.value.city = data.localidade;
            form.value.street = data.logradouro;
            form.value.neighborhood = data.bairro;
            console.log("✅ Dirección autocompletada");
        } else {
            console.warn("⚠️ CEP no encontrado");
        }
    } catch (e) {
        console.error("❌ Error buscando CEP:", e);
    } finally {
        loadingCep.value = false;
    }
};

const handleSubmit = () => {
    console.log("🚀 handleSubmit llamado. isValid:", isValid.value);

    if (!isValid.value) {
        console.warn("⚠️ Formulario inválido, submit bloqueado");
        return;
    }

    const payload = {
        ...form.value,
        has_manager: extraAction.value,
    };

    console.log('✅ Emitiendo "completed" con payload:', payload);
    emit("completed", payload);
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(163, 230, 53, 0.2);
    border-radius: 10px;
}
</style>
