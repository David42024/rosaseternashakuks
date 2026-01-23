<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: 'cliente',
    newsletter: false,
})

const submit = () => {
    form.post('/admin/users')
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-2xl">
            <div class="mb-6">
                <Link href="/admin/users" class="text-pink-600 hover:text-pink-700 text-sm">
                    ← Volver a usuarios
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Nuevo Usuario</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre completo *
                    </label>
                    <input 
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                        placeholder="Nombre del usuario"
                    >
                    <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email *
                    </label>
                    <input 
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                        placeholder="correo@ejemplo.com"
                    >
                    <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono
                    </label>
                    <input 
                        v-model="form.phone"
                        type="tel"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                        placeholder="999 999 999"
                    >
                    <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">
                        {{ form.errors.phone }}
                    </p>
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña *
                    </label>
                    <input 
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                        placeholder="Mínimo 8 caracteres"
                    >
                    <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                        {{ form.errors.password }}
                    </p>
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmar Contraseña *
                    </label>
                    <input 
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                        placeholder="Repite la contraseña"
                    >
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Rol *
                    </label>
                    <select 
                        v-model="form.role"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    >
                        <option value="cliente">Cliente</option>
                        <option value="admin">Administrador</option>
                    </select>
                    <p v-if="form.errors.role" class="text-red-500 text-sm mt-1">
                        {{ form.errors.role }}
                    </p>
                </div>

                <!-- Newsletter -->
                <div class="flex items-center gap-2">
                    <input 
                        type="checkbox"
                        v-model="form.newsletter"
                        id="newsletter"
                        class="w-4 h-4 text-pink-600 rounded focus:ring-pink-500"
                    >
                    <label for="newsletter" class="text-sm text-gray-700">
                        Suscrito al newsletter
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : 'Crear Usuario' }}
                    </button>
                    <Link 
                        href="/admin/users"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>