<template>
  <a
    :href="whatsappLink"
    target="_blank"
    class="fixed bottom-6 right-6 z-[90] w-14 h-14 flex items-center justify-center bg-[#25D366] text-white rounded-full shadow-[0_10px_25px_rgba(37,211,102,0.4)] active:scale-90 transition-all duration-300 hover:brightness-110"
    @click="$emit('click')"
  >
    <PhWhatsappLogo size="32" weight="fill" />

    <span 
      v-if="badge" 
      class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full"
    ></span>
  </a>
</template>

<script setup>
import { computed } from 'vue';
import { PhWhatsappLogo } from "@phosphor-icons/vue";

const props = defineProps({
  phone: { type: String, required: true },
  message: { type: String, default: 'Olá! Gostaria de mais informações.' },
  badge: { type: Boolean, default: false }
});

defineEmits(['click']);

const whatsappLink = computed(() => {
  const encodedMessage = encodeURIComponent(props.message);
  // Limpiamos el teléfono de espacios o caracteres especiales por si acaso
  const cleanPhone = props.phone.replace(/\D/g, '');
  return `https://wa.me/${cleanPhone}?text=${encodedMessage}`;
});
</script>

<style scoped>
/* Animación de pulso suave para que llame la atención sin molestar */
@keyframes pulse-green {
  0% { shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
  70% { shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
  100% { shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
}

a {
  /* Opcional: añadir un pulso si quieres que sea muy llamativo */
  animation: pulse-green 2s infinite;
}
</style>