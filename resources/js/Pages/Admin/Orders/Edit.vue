<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    order: Object,
    products: Array,
    statuses: Object,
})

// Preparar items existentes
const initialItems = props.order.items.map(item => ({
    product_id: item.product_id,
    product_name: item.product_name,
    price: parseFloat(item.price),
    quantity: item.quantity,
    subtotal: parseFloat(item.subtotal),
    image: item.product?.primary_image_url || '',
}))

const form = useForm({
    customer_name: props.order.customer_name,
    customer_phone: props.order.customer_phone,
    customer_email: props.order.customer_email || '',
    address: props.order.address || '',
    notes: props.order.notes || '',
    status: props.order.status,
    items: initialItems,
})

const selectedProduct = ref('')
const selectedQuantity = ref(1)

const addItem = () => {
    if (!selectedProduct.value) return
    
    const product = props.products.find(p => p.id === parseInt(selectedProduct.value))
    if (!product) return

    const existingIndex = form.items.findIndex(i => i.product_id === product.id)
    
    if (existingIndex >= 0) {
        form.items[existingIndex].quantity += selectedQuantity.value
        form.items[existingIndex].subtotal = form.items[existingIndex].price * form.items[existingIndex].quantity
    } else {
        form.items.push({
            product_id: product.id,
            product_name: product.name,
            price: product.current_price,
            quantity: selectedQuantity.value,
            subtotal: product.current_price * selectedQuantity.value,
            image: product.primary_image_url,
        })
    }

    selectedProduct.value = ''
    selectedQuantity.value = 1
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

const updateQuantity = (index, quantity) => {
    if (quantity < 1) return
    form.items[index].quantity = quantity
    form.items[index].subtotal = form.items[index].price * quantity
}

const total = computed(() => {
    return form.items.reduce((sum, item) => sum + item.subtotal, 0)
})

const submit = () => {
    form.put(`/admin/orders/${props.order.id}`)
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl">
            <div class="mb-6">
                <Link href="/admin/orders" class="text-pink-600 hover:text-pink-700 text-sm">
                    ← Volver a pedidos
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Editar Pedido #{{ order.id }}</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Datos del cliente -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Datos del cliente</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input 
                                v-model="form.customer_name"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                            >
                            <p v-if="form.errors.customer_name" class="text-red-500 text-sm mt-1">{{ form.errors.customer_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                            <input 
                                v-model="form.customer_phone"
                                type="tel"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                            >
                            <p v-if="form.errors.customer_phone" class="text-red-500 text-sm mt-1">{{ form.errors.customer_phone }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input 
                            v-model="form.customer_email"
                            type="email"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <textarea 
                            v-model="form.address"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea 
                            v-model="form.notes"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select 
                            v-model="form.status"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        >
                            <option v-for="(label, key) in statuses" :key="key" :value="key">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Productos -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Productos</h2>
                    
                    <!-- Agregar producto -->
                    <div class="flex gap-4">
                        <select 
                            v-model="selectedProduct"
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        >
                            <option value="">Seleccionar producto...</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }} - MXN {{ product.current_price }}
                            </option>
                        </select>
                        <input 
                            v-model.number="selectedQuantity"
                            type="number"
                            min="1"
                            class="w-20 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        >
                        <button 
                            type="button"
                            @click="addItem"
                            class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700"
                        >
                            Agregar
                        </button>
                    </div>

                    <!-- Lista de items -->
                    <div v-if="form.items.length > 0" class="space-y-3">
                        <div 
                            v-for="(item, index) in form.items" 
                            :key="index"
                            class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg"
                        >
                            <img 
                                v-if="item.image"
                                :src="item.image" 
                                :alt="item.product_name"
                                class="w-12 h-12 object-cover rounded"
                            >
                            <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-xl">🌹</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">{{ item.product_name }}</p>
                                <p class="text-sm text-gray-500">MXN {{ item.price }} c/u</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button"
                                    @click="updateQuantity(index, item.quantity - 1)"
                                    class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300"
                                >-</button>
                                <span class="w-8 text-center">{{ item.quantity }}</span>
                                <button 
                                    type="button"
                                    @click="updateQuantity(index, item.quantity + 1)"
                                    class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300"
                                >+</button>
                            </div>
                            <span class="font-bold w-24 text-right">MXN {{ item.subtotal.toFixed(2) }}</span>
                            <button 
                                type="button"
                                @click="removeItem(index)"
                                class="text-red-500 hover:text-red-700"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <p v-else class="text-gray-500 text-center py-4">No hay productos agregados</p>

                    <!-- Total -->
                    <div v-if="form.items.length > 0" class="border-t pt-4 flex justify-between items-center">
                        <span class="text-lg font-semibold">Total:</span>
                        <span class="text-2xl font-bold text-pink-600">MXN {{ total.toFixed(2) }}</span>
                    </div>

                    <p v-if="form.errors.items" class="text-red-500 text-sm">{{ form.errors.items }}</p>
                </div>

                <!-- Info -->
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                    <p><strong>Creado:</strong> {{ new Date(order.created_at).toLocaleString('es-PE') }}</p>
                    <p><strong>Actualizado:</strong> {{ new Date(order.updated_at).toLocaleString('es-PE') }}</p>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button 
                        type="submit"
                        :disabled="form.processing || form.items.length === 0"
                        class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : 'Actualizar Pedido' }}
                    </button>
                    <Link href="/admin/orders" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>