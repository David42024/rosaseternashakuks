<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    users: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const roleFilter = ref(props.filters?.role || '')

const applyFilters = () => {
    router.get('/admin/users', {
        search: search.value || undefined,
        role: roleFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true })
}

watch(roleFilter, applyFilters)

let searchTimeout
const onSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(applyFilters, 300)
}

const deleteUser = (id, name) => {
    if (confirm(`¿Estás seguro de eliminar a "${name}"?`)) {
        router.delete(`/admin/users/${id}`)
    }
}
</script>

<template>
    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
            <Link 
                href="/admin/users/create"
                class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition"
            >
                + Nuevo Usuario
            </Link>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input 
                    v-model="search"
                    @input="onSearch"
                    type="text"
                    placeholder="Buscar por nombre, email, teléfono..."
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                <select 
                    v-model="roleFilter"
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                >
                    <option value="">Todos los roles</option>
                    <option value="admin">Administradores</option>
                    <option value="cliente">Clientes</option>
                </select>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Newsletter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center">
                                    <span class="text-pink-600 font-semibold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">{{ user.phone || '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2 py-1 text-xs rounded-full font-medium',
                                user.role === 'admin' 
                                    ? 'bg-purple-100 text-purple-800' 
                                    : 'bg-blue-100 text-blue-800'
                            ]">
                                {{ user.role === 'admin' ? 'Administrador' : 'Cliente' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="user.newsletter" class="text-green-600">✓ Suscrito</span>
                            <span v-else class="text-gray-400">No</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ new Date(user.created_at).toLocaleDateString('es-PE') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link 
                                :href="`/admin/users/${user.id}/edit`"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Editar
                            </Link>
                            <button 
                                @click="deleteUser(user.id, user.name)"
                                class="text-red-600 hover:text-red-800"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="users.data.length === 0" class="text-center py-12">
                <span class="text-4xl">👥</span>
                <p class="text-gray-500 mt-4">No hay usuarios</p>
            </div>
        </div>

        <!-- Paginación -->
        <div v-if="users.last_page > 1" class="mt-6 flex justify-center gap-2">
            <Link 
                v-for="link in users.links" 
                :key="link.label"
                :href="link.url"
                :class="[
                    'px-4 py-2 rounded-lg transition',
                    link.active ? 'bg-pink-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                    !link.url && 'opacity-50 cursor-not-allowed'
                ]"
                v-html="link.label"
            />
        </div>
    </AdminLayout>
</template>