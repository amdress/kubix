<template>
    <div class="bg-gray-800 p-10 mt-30 md:p-12 rounded-2xl shadow-2xl w-full max-w-4xl mx-auto border border-gray-700 pt-24">
        <div class="flex flex-col items-center text-center mb-8">
            <!-- The SVG namespace is http://www.w3.org/2000/svg -->
             <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 2.5l-6 6a4.99 4.99 0 0 0-4.5 4.5l6 6a4.99 4.99 0 0 0 4.5-4.5l-6-6a2.5 2.5 0 0 1 2.5-2.5z"/>
                <path d="M21 16l-6 6a2.5 2.5 0 0 1-4.5-4.5l6-6a2.5 2.5 0 0 1 4.5 4.5z"/>
                <path d="M12 12l2-2"/>
                <path d="M10 14l-2 2"/>
            </svg>
            <h1 class="text-3xl font-extrabold mt-4">Pronto para liberar seus clientes?</h1>
            <p class="text-lg text-gray-400 mt-2 max-w-lg">Preencha o formulário abaixo e nossa equipe entrará em contato para agendar uma demonstração.</p>
        </div>

        <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-1">
                <label for="name" class="block text-gray-300 text-sm font-semibold mb-2">Nome da Empresa</label>
                <input type="text" id="name" class="w-full px-4 py-3 rounded-lg bg-gray-700 text-light placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200" placeholder="Nome da sua empresa">
            </div>
            <div class="md:col-span-1">
                <label for="contact-name" class="block text-gray-300 text-sm font-semibold mb-2">Seu Nome</label>
                <input type="text" id="contact-name" class="w-full px-4 py-3 rounded-lg bg-gray-700 text-light placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200" placeholder="Nome do responsável">
            </div>
            <div class="md:col-span-1">
                <label for="email" class="block text-gray-300 text-sm font-semibold mb-2">Email Profissional</label>
                <input type="email" id="email" class="w-full px-4 py-3 rounded-lg bg-gray-700 text-light placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200" placeholder="seuemail@empresa.com.br">
            </div>
            <div class="md:col-span-1">
                <label for="phone" class="block text-gray-300 text-sm font-semibold mb-2">Telefone</label>
                <input type="tel" id="phone" class="w-full px-4 py-3 rounded-lg bg-gray-700 text-light placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200" placeholder="(XX) XXXXX-XXXX">
            </div>
            <div class="md:col-span-2">
                <label for="message" class="block text-gray-300 text-sm font-semibold mb-2">Mensagem (Opcional)</label>
                <textarea id="message" rows="4" class="w-full px-4 py-3 rounded-lg bg-gray-700 text-light placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200" placeholder="Fale-nos sobre suas necessidades..."></textarea>
            </div>
            <div class="md:col-span-2 mt-4">
                <button type="submit" class="w-full bg-accent text-dark font-bold py-3 rounded-lg shadow-lg hover:bg-blue-600 transition-colors duration-200">
                    Solicitar Contato
                </button>
            </div>
        </form>
    </div> 
</template>

<script setup>
// Script setup section is currently empty in the original code snippet.
</script>
