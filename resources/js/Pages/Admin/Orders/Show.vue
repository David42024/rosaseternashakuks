<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    order: Object,
    statuses: Object,
})

const statusColors = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    confirmado: 'bg-blue-100 text-blue-800',
    preparando: 'bg-purple-100 text-purple-800',
    entregado: 'bg-green-100 text-green-800',
    cancelado: 'bg-red-100 text-red-800',
}

const updateStatus = (status) => {
    router.patch(`/admin/orders/${props.order.id}/status`, { status })
}

const openWhatsApp = () => {
    const message = `Hola ${props.order.customer_name}! 🌹\n\nTu pedido #${props.order.id} está siendo procesado.\n\nTotal: S/${props.order.total}\n\n¿Cómo prefieres coordinar el pago y entrega?`
    window.open(`https://wa.me/51${props.order.customer_phone.replace(/\D/g, '')}?text=${encodeURIComponent(message)}`, '_blank')
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <Link href="/admin/orders" class="text-pink-600 hover:text-pink-700 text-sm mb-2 inline-block">
                        ← Volver a pedidos
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ order.id }}</h1>
                    <p class="text-gray-500">{{ new Date(order.created_at).toLocaleString('es-PE') }}</p>
                </div>
                <span :class="['px-4 py-2 text-sm font-medium rounded-full', statusColors[order.status]]">
                    {{ order.status_label }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info del pedido -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Items -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Productos</h2>
                        <div class="space-y-4">
                            <div 
                                v-for="item in order.items" 
                                :key="item.id"
                                class="flex items-center gap-4 py-3 border-b last:border-b-0"
                            >
                                <img 
                                    v-if="item.product?.images?.[0]"
                                    :src="`/storage/${item.product.images[0].image_path}`"
                                    class="w-16 h-16 object-cover rounded-lg"
                                >
                                <div v-else class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">🌹</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ item.product_name }}</p>
                                    <p class="text-sm text-gray-500">S/{{ item.price }} x {{ item.quantity }}</p>
                                </div>
                                <span class="font-semibold">S/{{ item.subtotal }}</span>
                            </div>
                        </div>
                        <div class="border-t mt-4 pt-4 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-pink-600">S/{{ order.total }}</span>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div v-if="order.notes" class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Notas del cliente</h2>
                        <p class="text-gray-600">{{ order.notes }}</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Cliente -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Nombre</p>
                                <p class="font-medium">{{ order.customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Teléfono</p>
                                <p class="font-medium">{{ order.customer_phone }}</p>
                            </div>
                            <div v-if="order.customer_email">
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ order.customer_email }}</p>
                            </div>
                            <div v-if="order.address">
                                <p class="text-sm text-gray-500">Dirección</p>
                                <p class="font-medium">{{ order.address }}</p>
                            </div>
                        </div>
                        <button 
                            @click="openWhatsApp"
                            class="w-full mt-4 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            </svg>
                            Contactar por WhatsApp
                        </button>
                    </div>

                    <!-- Cambiar estado -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cambiar estado</h2>
                        <div class="space-y-2">
                            <button 
                                v-for="(label, key) in statuses" 
                                :key="key"
                                @click="updateStatus(key)"
                                :class="[
                                    'w-full py-2 px-4 rounded-lg text-sm font-medium transition',
                                    order.status === key 
                                        ? statusColors[key] + ' ring-2 ring-offset-2 ring-gray-300'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                {{ label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>