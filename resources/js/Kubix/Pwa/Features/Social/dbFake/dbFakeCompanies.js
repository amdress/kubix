// @/Kubix/core/data/dbFakeCompanies.js

export const dbFakeCompanies = [
  // --- COMIDA ---
  { 
    id: 1, name: "Bella Massa", category: 'comida', categoryLabel: 'Pizzaria Gourmet',
    primaryColor: "#e11d48", // Rosa/Rojo
    description: "Autêntica pizza italiana com massa de fermentação natural e ingredientes importados.",
    address: 'Rua Itupava, 120', distance: 150, rating: 4.8, likes: 124, isLiked: false, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Bella',
    gallery: ["https://images.unsplash.com/photo-1574123853664-3bb7d8f337a5?w=400", "https://images.unsplash.com/photo-1541745537411-b8046dc6d66c?w=400"]
  },
  { 
    id: 2, name: "Burger House", category: 'comida', categoryLabel: 'Hamburgueria',
    primaryColor: "#f59e0b", // Ámbar
    description: "Burgers artesanais grelhados no fogo como você nunca viu no bairro.",
    address: 'Av. Batel, 450', distance: 320, rating: 4.9, likes: 250, isLiked: true, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Burger',
    gallery: [] 
  },
  { 
    id: 3, name: "Sushi Yama", category: 'comida', categoryLabel: 'Culinária Japonesa',
    primaryColor: "#000000", // Negro
    description: "Peixes frescos e combos exclusivos para quem ama a culinária oriental.",
    address: 'Rua Chile, 88', distance: 500, rating: 4.7, likes: 180, isLiked: false, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Yama'
  },

  // --- SAÚDE ---
  { 
    id: 4, name: "Droga Vida", category: 'saude', categoryLabel: 'Farmácia 24h',
    primaryColor: "#2563eb", // Azul
    description: "Atendimento humanizado e entrega rápida em todo o bairro.",
    address: 'Av. Sete de Setembro', distance: 150, rating: 4.3, likes: 45, isLiked: false, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1586015555751-63bb77f4322a?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Vida'
  },
  { 
    id: 5, name: "Sorriso Real", category: 'saude', categoryLabel: 'Odontologia',
    primaryColor: "#0ea5e9", // Celeste
    description: "Especialistas em implantes e estética dental avançada.",
    address: 'Rua Comendador', distance: 280, rating: 4.9, likes: 67, isLiked: false, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Sorriso'
  },
  { 
    id: 6, name: "Ótica Look", category: 'saude', categoryLabel: 'Ótica',
    primaryColor: "#7c3aed", // Violeta
    description: "As melhores marcas de óculos com exame de vista computadorizado.",
    address: 'Galeria Central', distance: 300, rating: 4.7, likes: 33, isLiked: false, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1511499767350-a15104ac4aa6?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Look'
  },

  // --- MERCADO ---
  { 
    id: 7, name: "Mercado Eco", category: 'mercado', categoryLabel: 'Supermercado',
    primaryColor: "#16a34a", // Verde
    description: "Produtos frescos direto do produtor para a sua mesa.",
    address: 'Rua Nilo Peçanha', distance: 450, rating: 4.2, likes: 340, isLiked: false, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Eco'
  },
  { id: 8, name: "Frutaria Sul", category: 'mercado', categoryLabel: 'Hortifruti', primaryColor: "#ea580c", description: "Frutas selecionadas diariamente.", address: 'Rua Rebouças', distance: 200, rating: 4.9, likes: 112, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Sul' },
  { id: 9, name: "Empório VIP", category: 'mercado', categoryLabel: 'Conveniência', primaryColor: "#1e293b", description: "Bebidas geladas e snacks premium.", address: 'Esquina 3', distance: 30, rating: 4.1, likes: 45, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1534723452862-4c874e70d6f2?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=VIP' },

  // --- SERVIÇOS ---
  { 
    id: 10, name: "Barba & Arte", category: 'servicos', categoryLabel: 'Barbearia',
    primaryColor: "#451a03", // Marrón oscuro
    description: "Corte de cabelo e barba com toalha quente e estilo clássico.",
    address: 'Rua Carlos Gomes', distance: 180, rating: 4.9, likes: 95, isLiked: true, phone: '554199999999',
    image: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=800',
    logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Barba'
  },
  { id: 11, name: "Pet Feliz", category: 'servicos', categoryLabel: 'Pet Shop', primaryColor: "#db2777", description: "Banho, tosa e muito carinho para o seu melhor amigo.", address: 'Rua Principal', distance: 250, rating: 4.8, likes: 130, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Pet' },
  { id: 12, name: "Smart Fit", category: 'servicos', categoryLabel: 'Academia', primaryColor: "#facc15", description: "A maior estrutura fitness da região.", address: 'Av. Central', distance: 400, rating: 4.6, likes: 450, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Smart' },

  // --- AUTOMOTIVO ---
  { id: 13, name: "Posto Central", category: 'automotivo', categoryLabel: 'Posto de Combustível', primaryColor: "#dc2626", description: "Combustível de confiança e conveniência completa.", address: 'Av. Brasil, 1', distance: 200, rating: 4.1, likes: 78, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1563911892437-1feda0179e1b?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Posto' },
  { id: 14, name: "Oficina do João", category: 'automotivo', categoryLabel: 'Mecânica Geral', primaryColor: "#475569", description: "Reparos rápidos e manutenção preventiva.", address: 'Rua das Oficinas', distance: 800, rating: 4.8, likes: 56, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1530124560676-41bc127527c6?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Joao' },
  { id: 15, name: "Lava Jato Pro", category: 'automotivo', categoryLabel: 'Estética Automotiva', primaryColor: "#0ea5e9", description: "Limpeza detalhada e proteção para o seu veículo.", address: 'Rua Norte, 55', distance: 350, rating: 4.9, likes: 110, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Lava' },

  // --- PET (Adicional para rellenar) ---
  { id: 16, name: "Clínica VetCare", category: 'servicos', categoryLabel: 'Veterinário', primaryColor: "#0d9488", description: "Cuidando da saúde do seu pet 24h.", address: 'Rua das Flores', distance: 100, rating: 4.9, likes: 92, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1628009368231-7bb7cfcb0def?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Vet' },
  { id: 17, name: "Dog Walker", category: 'servicos', categoryLabel: 'Passeador', primaryColor: "#854d0e", description: "Passeios educativos para cães ativos.", address: 'Parque Barigui', distance: 1500, rating: 4.7, likes: 34, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1534361960057-19889db9621e?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Walk' },
  { id: 18, name: "Gato Mia", category: 'mercado', categoryLabel: 'Loja para Gatos', primaryColor: "#f472b6", description: "Tudo o que seu felino precisa.", address: 'Rua Chile', distance: 600, rating: 4.8, likes: 55, isLiked: false, phone: '554199999999', image: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=800', logo: 'https://api.dicebear.com/7.x/identicon/svg?seed=Gato' }
];