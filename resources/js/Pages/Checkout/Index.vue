<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    items: Array,
    subtotal: Number,
    total: Number,
})

const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    address: '',
    notes: '',
})

const submit = () => {
    form.post('/checkout')
}
</script>

<template>
    <PublicLayout>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">📝 Finalizar Pedido</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                        <h2 class="text-xl font-semibold text-gray-900">Datos de contacto</h2>

                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre completo *
                            </label>
                            <input 
                                v-model="form.customer_name"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="Tu nombre"
                            >
                            <p v-if="form.errors.customer_name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.customer_name }}
                            </p>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono / WhatsApp *
                            </label>
                            <input 
                                v-model="form.customer_phone"
                                type="tel"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="999 999 999"
                            >
                            <p v-if="form.errors.customer_phone" class="text-red-500 text-sm mt-1">
                                {{ form.errors.customer_phone }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email (opcional)
                            </label>
                            <input 
                                v-model="form.customer_email"
                                type="email"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="tu@email.com"
                            >
                            <p v-if="form.errors.customer_email" class="text-red-500 text-sm mt-1">
                                {{ form.errors.customer_email }}
                            </p>
                        </div>

                        <!-- Dirección -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Dirección de entrega (opcional)
                            </label>
                            <textarea 
                                v-model="form.address"
                                rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="Calle, número, distrito..."
                            ></textarea>
                            <p v-if="form.errors.address" class="text-red-500 text-sm mt-1">
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <!-- Notas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notas del pedido (opcional)
                            </label>
                            <textarea 
                                v-model="form.notes"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                placeholder="Instrucciones especiales, dedicatoria, etc..."
                            ></textarea>
                            <p v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                                {{ form.errors.notes }}
                            </p>
                        </div>

                        <!-- Botón submit -->
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-pink-600 text-white py-4 px-8 rounded-xl font-semibold hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Procesando...' : '✓ Confirmar Pedido' }}
                        </button>

                        <p class="text-sm text-gray-500 text-center">
                            Te contactaremos por WhatsApp para coordinar el pago y entrega
                        </p>
                    </form>
                </div>

                <!-- Resumen del pedido -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumen del pedido</h2>

                        <!-- Items -->
                        <div class="space-y-4 mb-6">
                            <div 
                                v-for="item in items" 
                                :key="item.id"
                                class="flex items-center gap-3"
                            >
                                <img 
                                    :src="item.image" 
                                    :alt="item.name"
                                    class="w-16 h-16 object-cover rounded-lg"
                                >
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ item.name }}</p>
                                    <p class="text-sm text-gray-500">x{{ item.quantity }}</p>
                                </div>
                                <span class="font-semibold">S/{{ item.total.toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Totales -->
                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>S/{{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Envío</span>
                                <span>A coordinar</span>
                            </div>
                            <div class="flex justify-between text-xl font-bold pt-2 border-t">
                                <span>Total</span>
                                <span class="text-pink-600">S/{{ total.toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Volver -->
                        <Link 
                            href="/carrito"
                            class="block text-center text-pink-600 hover:text-pink-700 font-medium mt-6"
                        >
                            ← Volver al carrito
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>