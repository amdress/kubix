<template>
  <div class="fixed inset-0 z-[200] bg-slate-950 flex flex-col items-center justify-between p-6">

    <!-- 🔹 HEADER -->
    <header class="w-full pt-10 text-center">
      <h2 class="text-white text-lg font-black uppercase tracking-tighter">
        Scanner KUBIX
      </h2>
      <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest mt-1">
        Aproxime o Código 
      </p>
    </header>

    <!-- 🔹 SCANNER FRAME -->
    <div class="relative w-full max-w-[300px] aspect-square group">

      <div class="absolute inset-0 rounded-[42px] border-2 border-white/5 bg-white/[0.02] backdrop-blur-sm"></div>

      <div class="absolute inset-2 rounded-[36px] overflow-hidden bg-black">
        <div id="kubix-qr-viewport" class="w-full h-full"></div>
      </div>

      <!-- corners -->
      <div class="absolute -top-1 -left-1 w-10 h-10 border-t-4 border-l-4 border-blue-600 rounded-tl-2xl"></div>
      <div class="absolute -top-1 -right-1 w-10 h-10 border-t-4 border-r-4 border-blue-600 rounded-tr-2xl"></div>
      <div class="absolute -bottom-1 -left-1 w-10 h-10 border-b-4 border-l-4 border-blue-600 rounded-bl-2xl"></div>
      <div class="absolute -bottom-1 -right-1 w-10 h-10 border-b-4 border-r-4 border-blue-600 rounded-br-2xl"></div>

      <!-- scan line -->
      <div class="absolute inset-x-8 top-1/2 h-[2px] bg-blue-500/50 shadow-[0_0_15px_rgba(59,130,246,0.8)] animate-scan-line"></div>

    </div>

    <!-- 🔹 FOOTER -->
    <footer class="w-full pb-10 flex flex-col items-center gap-6">

      <!-- error -->
      <p
        v-if="scannerError"
        class="text-red-400 text-[10px] font-black uppercase bg-red-500/10 px-4 py-2 rounded-lg"
      >
        {{ scannerError }}
      </p>

      <!-- close -->
      <button
        @click="closeAndStop"
        class="w-16 h-16 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center text-white active:scale-90 transition-all duration-300 hover:bg-white/10"
      >
        <PhX :size="28" weight="bold" />
      </button>

    </footer>

  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import { PhX } from "@phosphor-icons/vue"
import { useScanner } from "@/Kubix/core/utils/device/scanner"

const emit = defineEmits(['close', 'result'])

/**
 * 📷 Scanner Engine (CORE)
 */
const {
  start,
  stop,
  error: scannerError
} = useScanner()

/**
 * 🚀 INIT CAMERA
 */
onMounted(() => {
  start('kubix-qr-viewport', (decodedText) => {
    emit('result', decodedText)
    closeAndStop()
  })
})

/**
 * 🧹 CLEAN STOP
 */
const closeAndStop = async () => {
  await stop()
  emit('close')
}

/**
 * 🛑 SAFETY CLEANUP
 */
onUnmounted(() => {
  stop()
})
</script>

<style>
#kubix-qr-viewport video {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
  transform: scale(1.2);
}

@keyframes scan-line {
  0% { transform: translateY(-100px); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(100px); opacity: 0; }
}

.animate-scan-line {
  animation: scan-line 2.5s ease-in-out infinite;
}
</style>