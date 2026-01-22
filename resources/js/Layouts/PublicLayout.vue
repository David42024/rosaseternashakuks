<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    title: String
})
</script>

<template>
    <div class="min-h-screen bg-stone-50 font-sans selection:bg-rose-500 selection:text-white">
        <nav class="fixed w-full top-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-rose-100/50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center gap-3 group">
                            <div class="relative w-11 h-11 bg-gradient-to-br from-rose-200 to-rose-400 rounded-full flex items-center justify-center shadow-lg shadow-black/10 border border-white/10 group-hover:scale-110 transition-all duration-300">
                                <span class="text-xl">🌹</span>
                                <div class="absolute inset-0 rounded-full bg-rose-400/20 blur-md group-hover:blur-lg transition-all"></div>
                            </div>
                            <span class="text-2xl font-serif font-bold tracking-tight">
                                <span class="text-rose-400">Hakuks</span>
                            </span>
                        </Link>
                    </div>

                    <div class="hidden md:flex items-center gap-10">
                        <Link href="/" class="text-gray-600 hover:text-rose-600 font-medium text-sm transition relative group">
                            Inicio
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-rose-600 transition-all group-hover:w-full"></span>
                        </Link>
                        <Link href="/catalogo" class="text-gray-600 hover:text-rose-600 font-medium text-sm transition relative group">
                            Catálogo
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-rose-600 transition-all group-hover:w-full"></span>
                        </Link>
                        <Link href="/carrito" class="relative group p-2 rounded-full hover:bg-rose-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-rose-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="absolute top-0 right-0 bg-rose-600 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold shadow-sm ring-2 ring-white">0</span>
                        </Link>
                    </div>

                    <div class="flex items-center gap-4">
                        <Link href="/login" class="text-gray-700 hover:text-rose-700 transition text-sm font-semibold">Ingresar</Link>
                        <Link href="/register" class="bg-gray-900 text-white px-6 py-2.5 rounded-full hover:bg-rose-600 transition text-sm font-medium shadow-lg">Registrarse</Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-20">
            <slot />
        </main>

        <footer class="bg-[#9e5559] text-white py-20 mt-20 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-rose-300 via-rose-500 to-rose-300"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                    <div class="col-span-1 md:col-span-1">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-11 h-11 bg-gradient-to-br from-rose-200 to-rose-400 rounded-full flex items-center justify-center shadow-lg shadow-black/20">
                                <span class="text-xl">🌹</span>
                            </div>
                            <span class="font-serif font-bold text-2xl tracking-wide text-white">Hakuks</span>
                        </div>
                        <p class="text-rose-100/70 text-sm leading-relaxed mb-6">
                            Transformamos sentimientos en arte floral. Especialistas en rosas eternas y diseños lumínicos que perduran en el tiempo.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-serif font-semibold text-lg mb-6 text-rose-200">Explorar</h3>
                        <ul class="space-y-4 text-rose-100/60">
                            <li><Link href="/catalogo" class="hover:text-white hover:translate-x-1 transition-all inline-block text-sm">Catálogo Completo</Link></li>
                            <li><Link href="/nosotros" class="hover:text-white hover:translate-x-1 transition-all inline-block text-sm">Nuestra Historia</Link></li>
                            <li><Link href="/faq" class="hover:text-white hover:translate-x-1 transition-all inline-block text-sm">Preguntas Frecuentes</Link></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-serif font-semibold text-lg mb-6 text-rose-200">Contacto</h3>
                        <ul class="space-y-4 text-rose-100/60 text-sm">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>+52 867 316 0224</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>hola@hakuks.com</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-serif font-semibold text-lg mb-6 text-rose-200">Mantente al día</h3>
                        <div class="relative">
                            <input type="email" placeholder="Tu correo electrónico" class="w-full pl-4 pr-12 py-3 rounded-xl bg-white/5 text-white text-sm border border-rose-300/20 focus:border-rose-400 focus:outline-none placeholder-rose-100/30" />
                            <button class="absolute right-2 top-2 bg-rose-500 text-white p-1.5 rounded-lg hover:bg-rose-400 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border-t border-rose-300/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-rose-100/40 text-[10px] uppercase tracking-widest">
                    <p>&copy; 2026 Hakuks. Hecho con ❤️ para ti.</p>
                    <div class="flex gap-6 font-bold">
                        <a href="#" class="hover:text-white transition">Privacidad</a>
                        <a href="#" class="hover:text-white transition">Términos</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>