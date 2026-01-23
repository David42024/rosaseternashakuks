<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const categoryFilter = ref(props.filters?.category || '')
const statusFilter = ref(props.filters?.status || '')

const applyFilters = () => {
    router.get('/admin/products', {
        search: search.value || undefined,
        category: categoryFilter.value || undefined,
        status: statusFilter.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

watch([categoryFilter, statusFilter], () => {
    applyFilters()
})

let searchTimeout
const onSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilters, 300)
}

const toggleActive = (product) => {
    const action = product.is_active ? 'desactivar' : 'activar'
    if (confirm(`¿Estás seguro de ${action} "${product.name}"?`)) {
        router.delete(`/admin/products/${product.id}`, {
            data: {
                search: search.value || undefined,
                category: categoryFilter.value || undefined,
                status: statusFilter.value || undefined,
            },
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Productos</h1>
            <Link 
                href="/admin/products/create"
                class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition"
            >
                + Nuevo Producto
            </Link>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input 
                    v-model="search"
                    @input="onSearch"
                    type="text"
                    placeholder="Buscar productos..."
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                <select 
                    v-model="categoryFilter"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                    <option value="">Todas las categorías</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>
                <select 
                    v-model="statusFilter"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                    <option value="">Todos los estados</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="product in products.data" :key="product.id">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img 
                                    :src="product.primary_image_url" 
                                    :alt="product.name"
                                    class="w-12 h-12 object-cover rounded-lg"
                                >
                                <div>
                                    <p class="font-medium text-gray-900">{{ product.name }}</p>
                                    <p v-if="product.is_featured" class="text-xs text-yellow-600">⭐ Destacado</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">{{ product.category?.name || '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-medium text-gray-900">S/{{ product.price }}</span>
                                <span v-if="product.sale_price" class="block text-sm text-red-600">
                                    Oferta: S/{{ product.sale_price }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                {{ product.stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2 py-1 text-xs rounded-full',
                                product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            ]">
                                {{ product.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link 
                                :href="`/admin/products/${product.id}/edit`"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Editar
                            </Link>
                            <button 
                                @click="toggleActive(product)"
                                :class="product.is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'"
                            >
                                {{ product.is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="products.data.length === 0" class="text-center py-12">
                <span class="text-4xl">📦</span>
                <p class="text-gray-500 mt-4">No hay productos</p>
            </div>
        </div>

        <!-- Paginación -->
        <div v-if="products.last_page > 1" class="mt-6 flex justify-center gap-2">
            <template v-for="link in products.links" :key="link.label">
                
                <!-- Link clickeable -->
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

                <!-- Link deshabilitado (Prev / Next cuando no hay) -->
                <span
                    v-else
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed"
                    v-html="link.label"
                />
            </template>
        </div>

    </AdminLayout>
</template>