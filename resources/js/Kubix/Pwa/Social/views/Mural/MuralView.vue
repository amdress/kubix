<template>
  <div class="min-h-screen bg-white">
    
    <div class="pt-6 border-b border-slate-50">
      <div class="px-6 flex items-center justify-between mb-3">
        <h2 class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em]">
          Negócios Ativos em Batel
        </h2>
      </div>

      <BusinessStatus 
        :brands="fakePartners" 
        @select="toGo" 
        class="pb-4"
      />
    </div>

    <main class="py-4">
      <div class="flex flex-col">
        <MuralCard 
          v-for="post in fakeMuralData" 
          :key="post.id" 
          :data="post"
          @open-business="toGo"
          @select="handleDetail"
        />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from "vue-router";
import BusinessStatus from "@/Kubix/Pwa/shared/Ui/status/BusinessStatus.vue";
import MuralCard from "../../components/cards/MuralCard.vue";

const router = useRouter();

// REDIRECCIÓN AL SPLASH (Lo que me pediste)
const toGo = (business) => {
  const id = typeof business === 'string' ? business : business.id;
  router.push({ name: 'workspace.public.businessSplash', params: { id } });
};

const handleDetail = (post) => console.log("Abriendo detalle de:", post.title);

// --- FAKE DATA: NEGOCIOS (Para BusinessStatus) ---
const fakePartners = ref([
  { id: "jp-1", name: "Juan Pinchos", logo: "https://images.unsplash.com/photo-1594179047519-f347310d3322?w=100", status: 1 },
  { id: "kb-1", name: "Kubix Café", logo: "https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=100", status: 1 },
  { id: "gym-1", name: "Iron Batel", logo: "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=100", status: 2 },
  { id: "pet-1", name: "Pet Shop", logo: "https://images.unsplash.com/photo-1516733725897-1aa73b87c8e8?w=100", status: 3 },
  { id: "pet-1", name: "Pet Shop", logo: "https://images.unsplash.com/photo-1516733725897-1aa73b87c8e8?w=100", status: 3 },
  { id: "pet-1", name: "Pet Shop", logo: "https://images.unsplash.com/photo-1516733725897-1aa73b87c8e8?w=100", status: 3 },
  { id: "pet-1", name: "Pet Shop", logo: "https://images.unsplash.com/photo-1516733725897-1aa73b87c8e8?w=100", status: 3 },
]);

// --- FAKE DATA: MURAL (Polimorfismo puro) ---
const fakeMuralData = ref([
  {
    id: 101,
    type: 'PROMO', // Forma con imagen
    businessId: 'jp-1',
    businessName: 'Juan Pinchos',
    avatar: 'https://images.unsplash.com/photo-1594179047519-f347310d3322?w=100',
    isOpen: true,
    timeAgo: '12 min',
    distance: '300m',
    title: 'Noite da Picanha: 3x2',
    description: 'Somente hoje na unidade Batel. Apresente o código no balcão e aproveite o festival de espetinhos.',
    image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600',
    badge: 'Oferta Relâmpago'
  },
  {
    id: 102,
    type: 'ALERTA', // Forma sólida, pesada, informativa
    businessId: 'admin-1',
    businessName: 'Segurança Batel',
    avatar: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=100',
    isOpen: true,
    timeAgo: '45 min',
    distance: '1.5km',
    title: 'Rua Interditada para Obras',
    description: 'Manutenção na rede elétrica na Visconde de Guarapuava. Previsão de retorno: 19:00h.',
    image: null,
    badge: 'Urgente'
  },
  {
    id: 103,
    type: 'EVENTO', // Forma con layout de calendario
    businessId: 'kb-1',
    businessName: 'Kubix Café',
    avatar: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=100',
    isOpen: true,
    timeAgo: '2h',
    distance: '600m',
    title: 'Workshop: Latte Art para Iniciantes',
    description: 'Venha aprender a fazer desenhos no seu café com nosso barista premiado. Vagas limitadas.',
    image: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600',
    eventDay: '25',
    eventMonth: 'ABR',
    eventTime: '15:00h',
    badge: 'Inscrições Abertas'
  },
  {
    id: 104,
    type: 'INFO', // Forma tipo "Tweet" / Status rápido
    businessId: 'gym-1',
    businessName: 'Iron Batel',
    avatar: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=100',
    isOpen: true,
    timeAgo: '5h',
    distance: '900m',
    title: 'Nova área de Crossfit finalizada!',
    description: 'Equipamentos novos e área ampliada. Venha conferir o novo espaço a partir de amanhã.',
    image: null,
    badge: 'Novidade'
  }
]);
</script>