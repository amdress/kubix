<template>
  <div class="bg-[#05070a] text-slate-100 overflow-x-hidden">

    <!-- Fondo global -->
    <div class="fixed inset-0 -z-20 pointer-events-none">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(59,130,246,0.07)_0%,rgba(5,7,10,1)_70%)]" />
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808005_1px,transparent_1px),linear-gradient(to_bottom,#80808005_1px,transparent_1px)] bg-[size:60px_60px]" />
    </div>

    <HeroSection />
    <StatsSection />
    <MobileSection />
    <TestimonialsSection />

  </div>
</template>

<script setup>
import HeroSection         from './components/HeroSection.vue'
import StatsSection        from './components/StatsSection.vue'
import MobileSection       from './components/MobileSection.vue'
import TestimonialsSection from './components/TestimonialsSection.vue'
</script>