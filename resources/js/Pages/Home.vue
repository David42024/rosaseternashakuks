<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineProps({
    featuredProducts: Array,
    categories: Array,
    latestProducts: Array,
})
</script>

<template>
    <PublicLayout>
        <section className="relative min-h-[700px] flex items-center overflow-hidden bg-[#8b4347]">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-[#fdf2f2] via-[#d4a3a7] to-[#8b4347] opacity-100"></div>
            <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-white/40 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] left-[10%] w-[400px] h-[400px] bg-[#fdf2f2]/30 rounded-full blur-[100px]"></div>
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-12">
                <div className="flex flex-col md:flex-row items-center justify-between gap-16">
            
            <div className="w-full md:w-1/2 text-center md:text-left order-2 md:order-1">
                <span className="inline-block py-1.5 px-4 rounded-full bg-[#fdf2f2]/10 border border-[#fdf2f2]/20 text-[#fdf2f2] text-sm font-medium mb-8 backdrop-blur-md">
                    ✨ Arte Floral Artesanal
                </span>

                <h1 className="text-6xl md:text-8xl font-serif font-bold mb-8 text-white leading-[1.1] drop-shadow-2xl">
                    Regala Momentos <br/>
                    <span className="text-[#fdf2f2] italic opacity-90">Eternos</span>
                </h1>

                <p className="text-lg md:text-xl mb-12 text-[#fdf2f2]/80 font-light leading-relaxed max-w-xl mx-auto md:mx-0">
                    Descubre nuestros arreglos exclusivos con rosas eternas y luces LED. Un detalle que captura la belleza para siempre.
                </p>

                <div className="flex flex-col sm:flex-row gap-5 justify-center md:justify-start">
                    <Link 
                        href="/catalogo" 
                        className="bg-[#fdf2f2] text-[#632a2d] px-10 py-4 rounded-full font-bold hover:shadow-[0_0_30px_rgba(253,242,242,0.4)] transition-all duration-300 transform hover:-translate-y-1 text-center"
                    >
                        Ver Colección
                    </Link>
                    
                    <Link 
                        href="/contacto" 
                        className="border-2 border-[#fdf2f2]/30 text-[#fdf2f2] px-10 py-4 rounded-full font-semibold hover:bg-[#fdf2f2]/10 transition-all duration-300 text-center backdrop-blur-sm"
                    >
                        Personalizar
                    </Link>
                </div>
            </div>

            <div className="w-full md:w-1/2 flex justify-center items-center order-1 md:order-2">
                <div className="relative">
                    <div className="absolute inset-0 bg-[#f5d0d3]/30 blur-[80px] rounded-full animate-pulse"></div>
                    
                    <img 
                        src="haku-logo.png" 
                        alt="Haku Ks" 
                        className="relative z-10 w-full max-w-[450px] h-auto drop-shadow-[0_20px_50px_rgba(0,0,0,0.3)] hover:scale-105 transition-transform duration-700 ease-in-out"
                    />
                </div>
            </div>

        </div>
    </div>
</section>

        <section class="py-24 bg-stone-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                    <div>
                        <h2 class="text-4xl font-serif font-bold text-gray-900 mb-2">
                            Categorías
                        </h2>
                        <p class="text-gray-500 text-lg">Encuentra el detalle perfecto para cada ocasión</p>
                    </div>
                    <Link href="/catalogo" class="hidden md:flex items-center text-rose-600 font-semibold hover:text-rose-700 transition">
                        Ver todo el catálogo <span class="ml-2 text-xl">→</span>
                    </Link>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <Link 
                        v-for="category in categories" 
                        :key="category.id"
                        :href="`/categoria/${category.slug}`"
                        class="group relative h-64 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500"
                    >
                        <div class="absolute inset-0 bg-gray-200">
                            <img 
                                v-if="category.image_url"
                                :src="category.image_url" 
                                :alt="category.name"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out"
                            >
                            <div v-else class="w-full h-full flex items-center justify-center bg-rose-100 text-rose-300">
                                <span class="text-6xl">🌹</span>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-6">
                            <h3 class="text-white font-bold text-xl mb-1 transform translate-y-2 group-hover:translate-y-0 transition duration-300">{{ category.name }}</h3>
                            <p class="text-rose-200 text-sm opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition duration-300 delay-75">Explorar colección</p>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <section class="py-24 bg-white relative">
            <div class="absolute top-0 left-0 w-full h-64 bg-stone-50 skew-y-2 transform origin-top-left -z-0"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-rose-600 font-bold tracking-wider text-sm uppercase">Lo más vendido</span>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 mt-2 mb-4">
                        Favoritos de la Semana
                    </h2>
                    <div class="w-24 h-1 bg-rose-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                    <Link 
                        v-for="product in featuredProducts" 
                        :key="product.id"
                        :href="`/catalogo/${product.slug}`"
                        class="group bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-2"
                    >
                        <div class="aspect-[4/5] bg-gray-50 relative overflow-hidden">
                            <img 
                                :src="product.primary_image_url" 
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            >
                            <span 
                                v-if="product.sale_price" 
                                class="absolute top-3 left-3 bg-red-500 text-white text-xs px-2.5 py-1 rounded-md font-bold shadow-sm"
                            >
                                -{{ product.discount_percent }}%
                            </span>
                            <div class="absolute bottom-4 right-4 translate-y-12 group-hover:translate-y-0 transition duration-300">
                                <button class="bg-white text-gray-900 p-3 rounded-full shadow-lg hover:bg-rose-600 hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-xs font-bold text-rose-500 mb-1 uppercase tracking-wide">{{ product.category?.name }}</p>
                            <h3 class="font-serif text-lg text-gray-900 mb-2 truncate group-hover:text-rose-600 transition">{{ product.name }}</h3>
                            
                            <div class="flex items-end gap-3 mt-4 border-t border-gray-50 pt-4">
                                <span class="text-xl font-bold text-gray-900">
                                    S/{{ product.current_price }}
                                </span>
                                <span v-if="product.sale_price" class="text-sm text-gray-400 line-through mb-1">
                                    S/{{ product.price }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <section class="py-24 bg-[#c2698f] text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-rose-500/20 rounded-full blur-[100px]"></div>

            <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
                <h2 class="text-4xl md:text-5xl font-serif font-bold mb-6">¿Buscas algo único?</h2>
                <p class="text-xl text-rose-100/80 mb-10 font-light leading-relaxed">
                    Creamos arreglos personalizados para bodas, aniversarios y eventos corporativos. 
                    Cuéntanos tu idea y la haremos realidad.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a 
                        href="https://wa.me/5218673160224?text=Hola%20Hakuks!%20Me%20gustaría%20solicitar%20información%20sobre%20un%20arreglo%20personalizado%20para%20un%20evento%20especial." 
                        target="_blank" 
                        class="inline-flex items-center justify-center gap-3 bg-[#25D366] text-white px-10 py-4 rounded-full font-bold hover:scale-105 transition-all shadow-lg hover:shadow-green-500/30"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Cotizar por WhatsApp
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>