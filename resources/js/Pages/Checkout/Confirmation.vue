<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    order: Object,
})

// Generar mensaje de WhatsApp
const whatsappMessage = () => {
    let message = `🌹 *Hola! Acabo de realizar el pedido #${props.order.id}*\n\n`
    message += `📋 *Resumen:*\n`
    
    props.order.items.forEach(item => {
        message += `• ${item.product_name} x${item.quantity} - MXN ${item.subtotal}\n`
    })
    
    message += `\n💰 *Total: MXN ${props.order.total}*\n\n`
    message += `¿Cómo coordinamos el pago y entrega?`
    
    return encodeURIComponent(message)
}

const whatsappUrl = `https://wa.me/5218673160224?text=${whatsappMessage()}`
</script>

<template>
    <PublicLayout>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Icono de éxito -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    ¡Pedido Realizado!
                </h1>
                <p class="text-xl text-gray-600">
                    Gracias por tu compra, {{ order.customer_name }} 🌹
                </p>
            </div>

            <!-- Detalles del pedido -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <!-- Header -->
                <div class="bg-pink-50 px-6 py-4 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Número de pedido</p>
                            <p class="text-2xl font-bold text-pink-600">#{{ order.id }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Estado</p>
                            <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                {{ order.status_label || 'Pendiente' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="px-6 py-4">
                    <h3 class="font-semibold text-gray-900 mb-4">Productos</h3>
                    <div class="space-y-3">
                        <div 
                            v-for="item in order.items" 
                            :key="item.id"
                            class="flex justify-between items-center py-2 border-b last:border-b-0"
                        >
                            <div>
                                <p class="font-medium text-gray-900">{{ item.product_name }}</p>
                                <p class="text-sm text-gray-500">
                                    MXN {{ item.price }} × {{ item.quantity }}
                                </p>
                            </div>
                            <span class="font-semibold">MXN {{ item.subtotal }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total</span>
                        <span class="text-pink-600">MXN {{ order.total }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos de contacto -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Datos de contacto</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nombre</p>
                        <p class="font-medium">{{ order.customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Teléfono</p>
                        <p class="font-medium">{{ order.customer_phone }}</p>
                    </div>
                    <div v-if="order.customer_email">
                        <p class="text-gray-500">Email</p>
                        <p class="font-medium">{{ order.customer_email }}</p>
                    </div>
                    <div v-if="order.address" class="col-span-2">
                        <p class="text-gray-500">Dirección</p>
                        <p class="font-medium">{{ order.address }}</p>
                    </div>
                    <div v-if="order.notes" class="col-span-2">
                        <p class="text-gray-500">Notas</p>
                        <p class="font-medium">{{ order.notes }}</p>
                    </div>
                </div>
            </div>

            <!-- Siguiente paso -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
                <h3 class="font-semibold text-green-800 mb-2">📱 ¿Qué sigue?</h3>
                <p class="text-green-700 mb-4">
                    Contáctanos por WhatsApp para coordinar el pago y la entrega de tu pedido.
                </p>
                <a 
                    :href="whatsappUrl"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 w-full bg-green-500 text-white py-3 px-6 rounded-xl font-semibold hover:bg-green-600 transition"
                >
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Coordinar por WhatsApp
                </a>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-4">
                <Link 
                    href="/catalogo"
                    class="flex-1 text-center bg-pink-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-pink-700 transition"
                >
                    Seguir comprando
                </Link>
                <Link 
                    href="/"
                    class="flex-1 text-center border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-50 transition"
                >
                    Volver al inicio
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>