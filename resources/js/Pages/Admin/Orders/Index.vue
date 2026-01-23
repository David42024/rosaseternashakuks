<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    orders: Object,
    statuses: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')
const dateFrom = ref(props.filters?.date_from || '')
const dateTo = ref(props.filters?.date_to || '')

const applyFilters = () => {
    router.get('/admin/orders', {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, { preserveState: true, preserveScroll: true })
}

watch([statusFilter, dateFrom, dateTo], applyFilters)

let searchTimeout
const onSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilters, 300)
}

const deleteOrder = (id) => {
    if (confirm('¿Estás seguro de eliminar este pedido?')) {
        router.delete(`/admin/orders/${id}`)
    }
}

const statusColors = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    confirmado: 'bg-blue-100 text-blue-800',
    preparando: 'bg-purple-100 text-purple-800',
    entregado: 'bg-green-100 text-green-800',
    cancelado: 'bg-red-100 text-red-800',
}
</script>

<template>
    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pedidos</h1>
            <Link 
                href="/admin/orders/create"
                class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition"
            >
                + Nuevo Pedido
            </Link>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input 
                    v-model="search"
                    @input="onSearch"
                    type="text"
                    placeholder="Buscar por cliente, teléfono..."
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                <select 
                    v-model="statusFilter"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                    <option value="">Todos los estados</option>
                    <option v-for="(label, key) in statuses" :key="key" :value="key">
                        {{ label }}
                    </option>
                </select>
                <input 
                    v-model="dateFrom"
                    type="date"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    placeholder="Desde"
                >
                <input 
                    v-model="dateTo"
                    type="date"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    placeholder="Hasta"
                >
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-pink-600">#{{ order.id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ order.customer_name }}</p>
                                <p class="text-sm text-gray-500">{{ order.customer_phone }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900">MXN {{ parseFloat(order.total).toFixed(2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['px-2 py-1 text-xs rounded-full', statusColors[order.status]]">
                                {{ statuses[order.status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ new Date(order.created_at).toLocaleDateString('es-PE') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link 
                                :href="`/admin/orders/${order.id}`"
                                class="text-gray-600 hover:text-gray-800"
                            >
                                Ver
                            </Link>
                            <Link 
                                :href="`/admin/orders/${order.id}/edit`"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Editar
                            </Link>
                            <button 
                                @click="deleteOrder(order.id)"
                                class="text-red-600 hover:text-red-800"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="orders.data.length === 0" class="text-center py-12">
                <span class="text-4xl">📦</span>
                <p class="text-gray-500 mt-4">No hay pedidos</p>
            </div>
        </div>

        <!-- Paginación -->
        <div v-if="orders.last_page > 1" class="mt-6 flex justify-center gap-2">
            <template v-for="link in orders.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-4 py-2 rounded-lg transition"
                    :class="link.active
                        ? 'bg-pink-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100'"
                    v-html="link.label"
                    preserve-scroll
                />
                <span
                    v-else
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed"
                    v-html="link.label"
                />
            </template>
        </div>
    </AdminLayout>
</template>