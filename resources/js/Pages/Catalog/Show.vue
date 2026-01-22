<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { useCartStore } from '@/stores/cartStore'
// Y agrega el import del store de notificaciones:
import { useNotificationStore } from '@/stores/notificationStore'

const notificationStore = useNotificationStore()

const props = defineProps({
    product: Object,
    relatedProducts: Array,
})

const quantity = ref(1)
const selectedImage = ref(0)
const adding = ref(false)
const cartStore = useCartStore()

// Calcular cantidad máxima permitida considerando ya en carrito
const maxAllowedQuantity = computed(() => {
  const inCart = cartStore.getProductQuantity(props.product.id)
  return Math.max(0, props.product.stock - inCart)
})

// Verificar si ya está en carrito
const isInCart = computed(() => {
  return cartStore.getProductQuantity(props.product.id) > 0
})

const images = computed(() => {
    return props.product.images?.length > 0 
        ? props.product.images 
        : [{ image_url: props.product.primary_image_url }]
})

// En CatalogShow.vue, modifica la función addToCart:
const addToCart = async () => {
    if (props.product.stock === 0 || quantity.value <= 0) return
    
    adding.value = true
    
    try {
        // Validar cantidad disponible
        if (quantity.value > maxAllowedQuantity.value) {
            notificationStore.notify(
                `❌ No puedes agregar ${quantity.value} unidades. Ya tienes ${cartStore.getProductQuantity(props.product.id)} en el carrito y solo hay ${props.product.stock} disponibles.`,
                'error'
            )
            adding.value = false
            return
        }
        
        const result = await cartStore.addItem(
            props.product.id, 
            quantity.value, 
            props.product.stock
        )
        
        if (result.success) {
            // Usar el store de notificaciones
            notificationStore.notify(result.message || '✅ Producto agregado al carrito', 'success')
            
            // Resetear cantidad a 1 para futuras compras
            quantity.value = 1
            
            // Si el producto ya no tiene stock disponible
            if (maxAllowedQuantity.value - quantity.value <= 0) {
                notificationStore.notify('⚠️ ¡Has alcanzado el stock máximo disponible de este producto!', 'warning')
            }
        } else {
            notificationStore.notify(`❌ ${result.error || 'Error al agregar al carrito'}`, 'error')
        }
    } catch (error) {
        console.error('Error inesperado:', error)
        notificationStore.notify('❌ Error inesperado', 'error')
    } finally {
        adding.value = false
    }
}



const buyWhatsApp = () => {
    const message = `🌹 ¡Hola Hakuks! Me interesa este producto:\n\n*${props.product.name}*\nPrecio: $${props.product.current_price}\nCantidad: ${quantity.value}\n\n¿Me podrían dar más información?`
    const phone = '5218673160224'
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank')
}

// Función para notificaciones (puedes usar una librería como Toast)
const showNotification = (message, type = 'success') => {
    // Aquí puedes implementar notificaciones toast
    // Por ahora usamos console.log y alert
    console.log(`${type}: ${message}`)
    
    // Para desarrollo, usar alert
    if (import.meta.env.DEV) {
        if (type === 'error') {
            alert(message)
        }
    }
    
    // En producción, implementa un sistema de notificaciones
    // Ejemplo con Alpine.js o un store de notificaciones
}

// Inicializar el store al montar
onMounted(() => {
    cartStore.initialize()
})
</script>

<template>
    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-12">
            <nav class="mb-8 flex items-center gap-2 text-xs md:text-sm tracking-wide uppercase text-gray-400">
                <Link href="/" class="hover:text-rose-500 transition">Inicio</Link>
                <span>/</span>
                <Link href="/catalogo" class="hover:text-rose-500 transition">Catálogo</Link>
                <span>/</span>
                <span class="text-gray-900 font-bold">{{ product.name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16">
                <div class="lg:col-span-7">
                    <div class="sticky top-28 space-y-4">
                        <div class="aspect-[4/5] md:aspect-square bg-white rounded-3xl overflow-hidden shadow-inner border border-gray-100 flex items-center justify-center">
                            <img 
                                :src="images[selectedImage]?.image_url || product.primary_image_url"
                                :alt="product.name"
                                class="w-full h-full object-contain hover:scale-105 transition-transform duration-500"
                            >
                        </div>

                        <div v-if="images.length > 1" class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                            <button 
                                v-for="(image, index) in images" 
                                :key="index"
                                @click="selectedImage = index"
                                :class="[
                                    'relative min-w-[80px] h-20 md:min-w-[100px] md:h-24 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0',
                                    selectedImage === index ? 'border-rose-500 shadow-md' : 'border-transparent opacity-60 hover:opacity-100'
                                ]"
                            >
                                <img :src="image.image_url" class="w-full h-full object-cover">
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="space-y-6">
                        <div>
                            <span v-if="product.category" class="bg-rose-50 text-rose-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                                {{ product.category.name }}
                            </span>
                            <h1 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mt-4 leading-tight">
                                {{ product.name }}
                            </h1>
                        </div>

                        <div class="flex items-baseline gap-4 border-b border-gray-100 pb-6">
                            <span class="text-4xl font-light text-rose-600">
                                ${{ product.current_price }}
                            </span>
                            <span v-if="product.sale_price" class="text-xl text-gray-300 line-through">
                                ${{ product.price }}
                            </span>
                        </div>

                        <div v-if="product.description" class="prose prose-sm text-gray-500 leading-relaxed">
                            {{ product.description }}
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div :class="['w-2 h-2 rounded-full', product.stock > 0 ? 'bg-green-500' : 'bg-red-500']"></div>
                                <span :class="['text-sm font-medium', product.stock > 0 ? 'text-green-600' : 'text-red-600']">
                                    {{ product.stock > 0 ? `Disponible (${product.stock} unidades)` : 'Agotado' }}
                                </span>
                            </div>
                            
                            <!-- Indicador de cantidad en carrito -->
                            <div v-if="isInCart" class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                <span class="text-rose-600 font-medium">
                                    Ya tienes {{ cartStore.getProductQuantity(product.id) }} en el carrito
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 py-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold uppercase text-gray-400 tracking-widest">Cantidad</label>
                                <span v-if="maxAllowedQuantity > 0" class="text-xs text-gray-500">
                                    Máximo: {{ maxAllowedQuantity }}
                                </span>
                                <span v-else class="text-xs text-red-500 font-medium">
                                    Stock máximo alcanzado
                                </span>
                            </div>
                            
                            <div class="flex items-center w-max border-2 border-gray-100 rounded-2xl p-1">
                                <button 
                                    @click="quantity = Math.max(1, quantity - 1)" 
                                    :disabled="quantity <= 1"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-rose-50 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    -
                                </button>
                                <span class="w-12 text-center font-bold">{{ quantity }}</span>
                                <button 
                                    @click="quantity = Math.min(maxAllowedQuantity, quantity + 1)" 
                                    :disabled="quantity >= maxAllowedQuantity || maxAllowedQuantity === 0"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-rose-50 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-4">
                            <button 
                                @click="addToCart"
                                :disabled="adding || product.stock === 0 || maxAllowedQuantity === 0"
                                class="w-full bg-gray-900 text-white py-5 rounded-2xl font-bold hover:bg-rose-600 transition-all shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 relative group"
                            >
                                <span v-if="!adding">
                                    <span v-if="isInCart">➕ Agregar más al carrito</span>
                                    <span v-else>🛒 Agregar al carrito</span>
                                </span>
                                <span v-else>Procesando...</span>
                                
                                <!-- Tooltip para stock máximo -->
                                <div v-if="maxAllowedQuantity === 0" 
                                     class="absolute bottom-full mb-2 hidden group-hover:block w-64 p-2 bg-gray-800 text-white text-xs rounded-lg">
                                    Ya tienes el stock máximo disponible en tu carrito ({{ cartStore.getProductQuantity(product.id) }} unidades)
                                </div>
                            </button>
                            
                            <button 
                                @click="buyWhatsApp"
                                class="w-full bg-white border-2 border-green-500 text-green-600 py-5 rounded-2xl font-bold hover:bg-green-50 transition-all flex items-center justify-center gap-3"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Consultar disponibilidad
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <section v-if="relatedProducts.length > 0" class="mt-24">
                <div class="flex justify-between items-end mb-8">
                    <h2 class="text-2xl font-serif font-bold text-gray-900">También podría gustarte</h2>
                    <Link href="/catalogo" class="text-rose-500 font-semibold text-sm hover:underline">Ver todo</Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                    <Link 
                        v-for="related in relatedProducts" 
                        :key="related.id"
                        :href="`/catalogo/${related.slug}`"
                        class="group"
                    >
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-white border border-gray-100 mb-4 shadow-sm group-hover:shadow-md transition-all">
                            <img :src="related.primary_image_url" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </div>
                        <h3 class="font-medium text-gray-800 truncate group-hover:text-rose-500 transition">{{ related.name }}</h3>
                        <p class="text-rose-600 font-bold mt-1">${{ related.current_price }}</p>
                    </Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>