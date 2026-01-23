<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    products: Object,
    categories: Array,
    currentCategory: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const sort = ref(props.filters?.sort || 'latest')
const selectedCategory = ref(props.filters?.category || '')

const applyFilters = () => {
    router.get('/catalogo', {
        search: search.value || undefined,
        sort: sort.value !== 'latest' ? sort.value : undefined,
        category: selectedCategory.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

watch([sort, selectedCategory], () => {
    applyFilters()
})

let searchTimeout
const onSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 300)
}
</script>

<template>
    <PublicLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ currentCategory ? currentCategory.name : 'Catálogo' }}
                </h1>
                <p class="text-gray-600 mt-2">
                    {{ products.total }} productos encontrados
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filtros -->
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                        <!-- Búsqueda -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                            <input 
                                v-model="search"
                                @input="onSearch"
                                type="text" 
                                placeholder="Buscar productos..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Categorías -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                            <select 
                                v-model="selectedCategory"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            >
                                <option value="">Todas</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                                    {{ cat.name }} ({{ cat.products_count }})
                                </option>
                            </select>
                        </div>

                        <!-- Ordenar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                            <select 
                                v-model="sort"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            >
                                <option value="latest">Más recientes</option>
                                <option value="price_asc">Precio: Menor a Mayor</option>
                                <option value="price_desc">Precio: Mayor a Menor</option>
                                <option value="name">Nombre A-Z</option>
                            </select>
                        </div>
                    </div>
                </aside>

                <!-- Grid de Productos -->
                <div class="flex-1">
                    <div v-if="products.data.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <Link 
                            v-for="product in products.data" 
                            :key="product.id"
                            :href="`/catalogo/${product.slug}`"
                            class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition"
                        >
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                <img 
                                    :src="product.primary_image_url" 
                                    :alt="product.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition"
                                >
                                <span 
                                    v-if="product.sale_price" 
                                    class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full"
                                >
                                    -{{ product.discount_percent }}%
                                </span>
                                <span 
                                    v-if="product.is_featured" 
                                    class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full"
                                >
                                    ⭐ Destacado
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 truncate">{{ product.name }}</h3>
                                <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-lg font-bold text-pink-600">
                                        MXN {{ product.current_price }}
                                    </span>
                                    <span v-if="product.sale_price" class="text-sm text-gray-400 line-through">
                                        MXN {{ product.price }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Sin productos -->
                    <div v-else class="text-center py-16">
                        <span class="text-6xl">🌹</span>
                        <h3 class="text-xl font-semibold text-gray-900 mt-4">No hay productos</h3>
                        <p class="text-gray-600 mt-2">Prueba con otros filtros de búsqueda</p>
                    </div>

                    <div v-if="products.last_page > 1" class="mt-8 flex justify-center gap-2">
                        <template v-for="link in products.links" :key="link.label">
                            
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
                </div>
            </div>
        </div>
    </PublicLayout>
</template>