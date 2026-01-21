<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    product: Object,
    relatedProducts: Array,
})

const page = usePage()
const quantity = ref(1)
const selectedImage = ref(0)
const adding = ref(false)

const images = computed(() => {
    return props.product.images?.length > 0 
        ? props.product.images 
        : [{ image_path: null, url: props.product.primary_image_url }]
})

const addToCart = () => {
    adding.value = true
    router.post('/carrito/agregar', {
        product_id: props.product.id,
        quantity: quantity.value,
    }, {
        preserveScroll: true,
        onFinish: () => {
            adding.value = false
        }
    })
}

const buyWhatsApp = () => {
    const message = `🌹 Hola! Me interesa este producto:\n\n*${props.product.name}*\nPrecio: S/${props.product.current_price}\nCantidad: ${quantity.value}\n\n¿Está disponible?`
    const phone = '51999999999' // Configurar número
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank')
}
</script>

<template>
    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm">
                <ol class="flex items-center gap-2 text-gray-500">
                    <li><Link href="/" class="hover:text-pink-600">Inicio</Link></li>
                    <li>/</li>
                    <li><Link href="/catalogo" class="hover:text-pink-600">Catálogo</Link></li>
                    <li>/</li>
                    <li v-if="product.category">
                        <Link :href="`/categoria/${product.category.slug}`" class="hover:text-pink-600">
                            {{ product.category.name }}
                        </Link>
                    </li>
                    <li v-if="product.category">/</li>
                    <li class="text-gray-900 font-medium">{{ product.name }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Galería de imágenes -->
                <div>
                    <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden mb-4">
                        <img 
                            :src="images[selectedImage]?.url || images[selectedImage]?.image_path ? `/storage/${images[selectedImage].image_path}` : product.primary_image_url" 
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        >
                    </div>
                    <div v-if="images.length > 1" class="grid grid-cols-5 gap-2">
                        <button 
                            v-for="(image, index) in images" 
                            :key="index"
                            @click="selectedImage = index"
                            :class="[
                                'aspect-square rounded-lg overflow-hidden border-2 transition',
                                selectedImage === index ? 'border-pink-600' : 'border-transparent'
                            ]"
                        >
                            <img 
                                :src="image.url || `/storage/${image.image_path}`" 
                                :alt="`${product.name} ${index + 1}`"
                                class="w-full h-full object-cover"
                            >
                        </button>
                    </div>
                </div>

                <!-- Información del producto -->
                <div>
                    <span v-if="product.category" class="text-pink-600 font-medium">
                        {{ product.category.name }}
                    </span>
                    
                    <h1 class="text-3xl font-bold text-gray-900 mt-2">
                        {{ product.name }}
                    </h1>

                    <!-- Precio -->
                    <div class="mt-4 flex items-center gap-4">
                        <span class="text-4xl font-bold text-pink-600">
                            S/{{ product.current_price }}
                        </span>
                        <span v-if="product.sale_price" class="text-xl text-gray-400 line-through">
                            S/{{ product.price }}
                        </span>
                        <span v-if="product.sale_price" class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            -{{ product.discount_percent }}% OFF
                        </span>
                    </div>

                    <!-- Descripción -->
                    <div v-if="product.description" class="mt-6 prose prose-pink">
                        <p class="text-gray-600">{{ product.description }}</p>
                    </div>

                    <!-- Stock -->
                    <div class="mt-6">
                        <span v-if="product.stock > 0" class="text-green-600 font-medium">
                            ✓ En stock ({{ product.stock }} disponibles)
                        </span>
                        <span v-else class="text-red-600 font-medium">
                            ✗ Agotado
                        </span>
                    </div>

                    <!-- Cantidad y botones -->
                    <div class="mt-8 space-y-4">
                        <!-- Cantidad -->
                        <div class="flex items-center gap-4">
                            <label class="text-gray-700 font-medium">Cantidad:</label>
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button 
                                    @click="quantity = Math.max(1, quantity - 1)"
                                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition"
                                >
                                    -
                                </button>
                                <span class="px-4 py-2 font-medium">{{ quantity }}</span>
                                <button 
                                    @click="quantity = Math.min(10, quantity + 1)"
                                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                @click="addToCart"
                                :disabled="adding || product.stock === 0"
                                class="flex-1 bg-pink-600 text-white py-4 px-8 rounded-xl font-semibold hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ adding ? 'Agregando...' : '🛒 Agregar al carrito' }}
                            </button>
                            <button 
                                @click="buyWhatsApp"
                                class="flex-1 bg-green-500 text-white py-4 px-8 rounded-xl font-semibold hover:bg-green-600 transition flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Pedir por WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos Relacionados -->
            <section v-if="relatedProducts.length > 0" class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Productos Relacionados</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <Link 
                        v-for="related in relatedProducts" 
                        :key="related.id"
                        :href="`/catalogo/${related.slug}`"
                        class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition"
                    >
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            <img 
                                :src="related.primary_image_url" 
                                :alt="related.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition"
                            >
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 truncate">{{ related.name }}</h3>
                            <span class="text-lg font-bold text-pink-600">S/{{ related.current_price }}</span>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>