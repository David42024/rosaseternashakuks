<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    categories: Object,
})

const deleteCategory = (id) => {
    if (confirm('¿Estás seguro de eliminar esta categoría?')) {
        router.delete(`/admin/categories/${id}`)
    }
}
</script>

<template>
    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Categorías</h1>
            <Link 
                href="/admin/categories/create"
                class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition"
            >
                + Nueva Categoría
            </Link>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="category in categories.data" :key="category.id">
                        <td class="px-6 py-4">
                            <img 
                                v-if="category.image"
                                :src="`/storage/${category.image}`" 
                                :alt="category.name"
                                class="w-12 h-12 object-cover rounded-lg"
                            >
                            <div v-else class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-xl">🌹</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ category.name }}</p>
                            <p class="text-sm text-gray-500">{{ category.slug }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">{{ category.products_count }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2 py-1 text-xs rounded-full',
                                category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            ]">
                                {{ category.is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link 
                                :href="`/admin/categories/${category.id}/edit`"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Editar
                            </Link>
                            <button 
                                @click="deleteCategory(category.id)"
                                class="text-red-600 hover:text-red-800"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty state -->
            <div v-if="categories.data.length === 0" class="text-center py-12">
                <span class="text-4xl">🏷️</span>
                <p class="text-gray-500 mt-4">No hay categorías</p>
                <Link 
                    href="/admin/categories/create"
                    class="inline-block mt-4 text-pink-600 hover:text-pink-700 font-medium"
                >
                    Crear primera categoría
                </Link>
            </div>
        </div>

        <div v-if="categories.last_page > 1" class="mt-6 flex justify-center gap-2">
            <template v-for="link in categories.links" :key="link.label">

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