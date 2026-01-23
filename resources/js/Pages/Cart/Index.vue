<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, reactive, computed, onMounted } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { useCartStore } from '@/stores/cartStore'
import { useNotificationStore } from '@/stores/notificationStore'

const props = defineProps({
    items: Array,
    subtotal: Number,
    total: Number,
})

// Stores
const cartStore = useCartStore()
const notificationStore = useNotificationStore()

// Inicializar al montar
onMounted(() => {
  cartStore.initialize()
})

// Usar reactive para manejar items localmente
const localItems = reactive([...props.items])
const localTotals = reactive({
    subtotal: props.subtotal,
    total: props.total
})

const updating = ref({})

// Función para calcular subtotal de un item
const calculateItemTotal = (item) => {
    return item.price * item.quantity
}

// Función para calcular totales globales
const calculateTotals = () => {
    localTotals.subtotal = localItems.reduce((sum, item) => {
        return sum + calculateItemTotal(item)
    }, 0)
    localTotals.total = localTotals.subtotal
}

// Verificar si se puede aumentar la cantidad (validación de stock)
const canIncreaseQuantity = (item) => {
  if (!item.max_stock) return item.quantity < 10 // Límite por defecto
  return item.quantity < item.max_stock
}

// Verificar si se puede disminuir la cantidad
const canDecreaseQuantity = (item) => {
  return item.quantity > 1
}

// Actualizar cantidad inmediatamente
const updateQuantity = async (id, newQuantity) => {
    if (newQuantity < 1) return
    
    const itemIndex = localItems.findIndex(i => i.id === id)
    if (itemIndex === -1) return
    
    const item = localItems[itemIndex]
    
    // Validar stock máximo
    if (item.max_stock && newQuantity > item.max_stock) {
      notificationStore.notify(
        `❌ No puedes tener más de ${item.max_stock} unidades. Stock limitado.`,
        'error'
      )
      return
    }
    
    // Límite general por seguridad
    if (newQuantity > 100) {
      notificationStore.notify('❌ La cantidad máxima permitida es 100', 'error')
      return
    }
    
    const oldQuantity = item.quantity
    const quantityDiff = newQuantity - oldQuantity
    
    // 1. ACTUALIZACIÓN INMEDIATA EN UI (optimista)
    item.quantity = newQuantity
    item.total = calculateItemTotal(item)
    calculateTotals()
    
    // Actualizar contador global en el store
    cartStore.count += quantityDiff
    
    // Actualizar el item en el store de cart
    const itemInCart = cartStore.items.find(i => i.id === id)
    if (itemInCart) {
      itemInCart.quantity = newQuantity
    }
    
    cartStore.saveToStorage?.()
    
    updating.value[id] = true
    
    // 2. SINCRONIZAR CON BACKEND (en segundo plano)
    try {
        await router.patch(`/carrito/actualizar/${id}`, { 
            quantity: newQuantity 
        }, {
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => {
                // 3. REVERTIR SI HAY ERROR
                console.error('Error actualizando cantidad:', errors)
                item.quantity = oldQuantity
                item.total = calculateItemTotal(item)
                calculateTotals()
                cartStore.count -= quantityDiff
                
                // Revertir en el store de cart
                if (itemInCart) {
                  itemInCart.quantity = oldQuantity
                }
                
                cartStore.saveToStorage?.()
                
                notificationStore.notify(
                  '❌ Error al actualizar cantidad. Por favor, intenta nuevamente.',
                  'error'
                )
            },
            onSuccess: () => {
              notificationStore.notify(
                `✅ Cantidad actualizada a ${newQuantity} unidad(es)`,
                'success'
              )
            }
        })
    } catch (error) {
        console.error('Error de conexión:', error)
        notificationStore.notify('❌ Error de conexión', 'error')
    } finally {
        updating.value[id] = false
    }
}

// Eliminar item inmediatamente
const removeItem = async (id) => {
    const itemIndex = localItems.findIndex(i => i.id === id)
    if (itemIndex === -1) return
    
    const item = localItems[itemIndex]
    
    // 1. ELIMINACIÓN INMEDIATA EN UI
    localItems.splice(itemIndex, 1)
    calculateTotals()
    
    // Actualizar contador global
    cartStore.count -= item.quantity
    
    // Remover del store de cart
    cartStore.items = cartStore.items.filter(i => i.id !== id)
    cartStore.saveToStorage?.()
    
    // 2. SINCRONIZAR CON BACKEND
    try {
        await router.delete(`/carrito/eliminar/${id}`, { 
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => {
                // REVERTIR SI HAY ERROR
                console.error('Error eliminando item:', errors)
                localItems.splice(itemIndex, 0, item)
                calculateTotals()
                cartStore.count += item.quantity
                cartStore.items.push({
                  id: item.id,
                  quantity: item.quantity,
                  addedAt: new Date().toISOString()
                })
                cartStore.saveToStorage?.()
                
                notificationStore.notify(
                  '❌ Error al eliminar item. Por favor, intenta nuevamente.',
                  'error'
                )
            },
            onSuccess: () => {
              notificationStore.notify(
                `✅ "${item.name}" eliminado del carrito`,
                'success'
              )
            }
        })
    } catch (error) {
        console.error('Error de conexión:', error)
        notificationStore.notify('❌ Error de conexión', 'error')
    }
}

// Vaciar carrito
const clearCart = async () => {
    if (!confirm('¿Estás seguro de vaciar el carrito?')) return
    
    // 1. LIMPIAR INMEDIATAMENTE
    const itemsBackup = [...localItems]
    localItems.length = 0
    calculateTotals()
    
    const oldCount = cartStore.count
    const oldItems = [...cartStore.items]
    cartStore.count = 0
    cartStore.items = []
    cartStore.saveToStorage?.()
    
    // 2. SINCRONIZAR CON BACKEND
    try {
        await router.delete('/carrito/vaciar', {
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => {
                // REVERTIR SI HAY ERROR
                console.error('Error vaciando carrito:', errors)
                localItems.push(...itemsBackup)
                calculateTotals()
                cartStore.count = oldCount
                cartStore.items = oldItems
                cartStore.saveToStorage?.()
                
                notificationStore.notify(
                  '❌ Error al vaciar carrito. Por favor, intenta nuevamente.',
                  'error'
                )
            },
            onSuccess: () => {
              notificationStore.notify('✅ Carrito vaciado correctamente', 'success')
            }
        })
    } catch (error) {
        console.error('Error de conexión:', error)
        notificationStore.notify('❌ Error de conexión', 'error')
    }
}

// Calcular totales iniciales
calculateTotals()

// Generar mensaje de WhatsApp
const generateWhatsAppMessage = () => {
    let message = `🌹 *¡Hola Hakuks!* \n\nQuiero hacer un pedido:\n\n`
    
    localItems.forEach(item => {
        message += `• *${item.name}*\n  Cantidad: ${item.quantity}\n  Precio: MXN ${item.price}\n  Subtotal: MXN ${item.total.toFixed(2)}\n\n`
    })
    
    message += `*Subtotal: MXN ${localTotals.subtotal.toFixed(2)}*\n`
    message += `*Total: MXN ${localTotals.total.toFixed(2)}*\n\n`
    message += `Por favor, confírmenme disponibilidad y proceso de pago. ¡Gracias!`
    
    return message
}
</script>

<template>
    <PublicLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">
                🛒 Mi Carrito 
                <span v-if="localItems.length > 0" class="text-pink-600 text-xl">
                    ({{ cartStore.totalItems }} items)
                </span>
            </h1>

            <!-- Carrito con items -->
            <div v-if="localItems.length > 0">
                <!-- Lista de items -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div 
                        v-for="item in localItems" 
                        :key="item.id"
                        class="flex items-center gap-4 p-4 border-b last:border-b-0 hover:bg-gray-50 transition-colors"
                    >
                        <!-- Imagen -->
                        <Link :href="`/catalogo/${item.slug}`" class="flex-shrink-0">
                            <img 
                                :src="item.image" 
                                :alt="item.name"
                                class="w-24 h-24 object-cover rounded-lg"
                            >
                        </Link>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <Link :href="`/catalogo/${item.slug}`" class="font-semibold text-gray-900 hover:text-pink-600 truncate block">
                                {{ item.name }}
                            </Link>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-pink-600 font-bold">MXN {{ item.price }}</span>
                                <span v-if="item.original_price !== item.price" class="text-gray-400 line-through text-sm">
                                    MXN{{ item.original_price }}
                                </span>
                            </div>
                            <!-- Indicador de stock disponible -->
                            <div v-if="item.max_stock" class="text-xs text-gray-500 mt-1">
                                Stock disponible: {{ item.max_stock }} unidades
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button 
                                @click="updateQuantity(item.id, item.quantity - 1)"
                                :disabled="updating[item.id] || !canDecreaseQuantity(item)"
                                class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Disminuir"
                            >
                                -
                            </button>
                            <span class="px-3 py-1 font-medium min-w-[40px] text-center">
                                {{ item.quantity }}
                            </span>
                            <button 
                                @click="updateQuantity(item.id, item.quantity + 1)"
                                :disabled="updating[item.id] || !canIncreaseQuantity(item)"
                                class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Aumentar"
                            >
                                +
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <div class="text-right w-24">
                            <span class="font-bold text-gray-900">MXN {{ item.total.toFixed(2) }}</span>
                        </div>

                        <!-- Eliminar -->
                        <button 
                            @click="removeItem(item.id)"
                            :disabled="updating[item.id]"
                            class="text-red-500 hover:text-red-700 transition p-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Eliminar"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center text-lg mb-4">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">MXN {{ localTotals.subtotal.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xl font-bold border-t pt-4">
                        <span>Total</span>
                        <span class="text-pink-600">MXN {{ localTotals.total.toFixed(2) }}</span>
                    </div>
                </div>

                <!-- Botones -->
                <div class="mt-6 flex flex-col sm:flex-row gap-4">
                    <Link 
                        href="/checkout"
                        class="flex-1 bg-pink-600 text-white py-4 px-8 rounded-xl font-semibold hover:bg-pink-700 transition text-center"
                    >
                        Proceder al checkout
                    </Link>
                    <a 
                        :href="`https://wa.me/5218673160224?text=${encodeURIComponent(generateWhatsAppMessage())}`"
                        target="_blank"
                        class="flex-1 bg-green-500 text-white py-4 px-8 rounded-xl font-semibold hover:bg-green-600 transition text-center flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Pedir por WhatsApp
                    </a>
                </div>

                <!-- Acciones secundarias -->
                <div class="mt-4 flex justify-between items-center">
                    <Link href="/catalogo" class="text-pink-600 hover:text-pink-700 font-medium">
                        ← Seguir comprando
                    </Link>
                    <button @click="clearCart" class="text-red-500 hover:text-red-700 font-medium">
                        Vaciar carrito
                    </button>
                </div>
            </div>

            <!-- Carrito vacío -->
            <div v-else class="text-center py-16">
                <div class="text-8xl mb-6">🛒</div>
                <h2 class="text-2xl font-semibold text-gray-900">Tu carrito está vacío</h2>
                <p class="text-gray-600 mt-2 mb-8">¡Agrega algunos productos hermosos!</p>
                <Link 
                    href="/catalogo" 
                    class="inline-block bg-pink-600 text-white py-3 px-8 rounded-xl font-semibold hover:bg-pink-700 transition"
                >
                    Ver catálogo
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>