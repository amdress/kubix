<template>
  <footer
    class="border-t border-white/10 bg-slate-950 text-slate-400 py-6 px-4 transition-all duration-300"
  >
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-center items-center gap-3 mb-6">
        <img
          src="@/assets/images/shared/logo_transparent.png"
          class="w-6 h-6 object-contain opacity-60"
          alt="Kubix"
        />
        <span class="font-black uppercase tracking-widest text-xs text-white">
          {{ t('footer.brand') }}
        </span>
      </div>

      <div class="text-center text-[11px] tracking-wide opacity-70 mb-6">
        <i18n-t keypath="footer.copyright" tag="p">
          <template #year>
            {{ currentYear }}
          </template>
          <template #company>
            <span class="font-black text-blue-400 mx-1">DRUNK-CODE</span>
          </template>
        </i18n-t>
      </div>

      <div class="flex justify-center gap-4 opacity-60 hover:opacity-80 transition-opacity mb-6 flex-wrap">
        <a href="#" class="hover:text-white transition-colors text-[10px] uppercase tracking-widest">
          {{ t('footer.links.terms') }}
        </a>
        <span class="text-white/20">·</span>
        <a href="#" class="hover:text-white transition-colors text-[10px] uppercase tracking-widest">
          {{ t('footer.links.privacy') }}
        </a>
        <span class="text-white/20">·</span>
        <a href="#" class="hover:text-white transition-colors text-[10px] uppercase tracking-widest">
          {{ t('footer.links.support') }}
        </a>
      </div>

      <div class="flex justify-center gap-3">
        <a
          v-for="social in socials"
          :key="social.name"
          :href="social.url"
          target="_blank"
          rel="noopener noreferrer"
          class="p-2 rounded-lg border border-white/10 hover:border-white/30 hover:text-white transition-all duration-200 hover:scale-110"
          :title="social.name"
        >
          <component :is="social.icon" :size="16" weight="bold" />
        </a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { markRaw } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  PhTwitterLogo,
  PhInstagramLogo,
  PhLinkedinLogo,
} from '@phosphor-icons/vue'

const { t } = useI18n()
const currentYear = new Date().getFullYear()

const socials = [
  {
    name: 'Twitter',
    url: 'https://twitter.com',
    icon: markRaw(PhTwitterLogo),
  },
  {
    name: 'Instagram',
    url: 'https://instagram.com',
    icon: markRaw(PhInstagramLogo),
  },
  {
    name: 'LinkedIn',
    url: 'https://linkedin.com',
    icon: markRaw(PhLinkedinLogo),
  },
]
</script>

<style scoped>
/* Mobile First */
footer {
  font-size: 0.875rem;
}

/* Tablet */
@media (min-width: 768px) {
  footer {
    padding: 2rem 1.5rem;
  }
}

/* Desktop */
@media (min-width: 1024px) {
  footer {
    padding: 2rem;
  }
}
</style>