<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-pink-100 to-rose-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex items-center gap-2">
                    <span class="text-4xl">🌹</span>
                    <span class="text-2xl font-bold text-pink-600">Hakuks</span>
                </Link>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">
                    Crear Cuenta
                </h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre completo
                        </label>
                        <input 
                            v-model="form.name"
                            type="text"
                            required
                            autofocus
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            placeholder="Tu nombre"
                        >
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Correo electrónico
                        </label>
                        <input 
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            placeholder="tu@email.com"
                        >
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Teléfono / WhatsApp (opcional)
                        </label>
                        <input 
                            v-model="form.phone"
                            type="tel"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            placeholder="999 999 999"
                        >
                        <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña
                        </label>
                        <input 
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            placeholder="Mínimo 8 caracteres"
                        >
                        <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirmar contraseña
                        </label>
                        <input 
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            placeholder="Repite tu contraseña"
                        >
                    </div>

                    <!-- Submit -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-pink-600 text-white py-3 rounded-lg font-semibold hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed mt-2"
                    >
                        {{ form.processing ? 'Creando cuenta...' : 'Crear cuenta' }}
                    </button>
                </form>

                <!-- Terms -->
                <p class="text-xs text-gray-500 text-center mt-4">
                    Al registrarte aceptas recibir notificaciones sobre tus pedidos
                </p>

                <!-- Login link -->
                <p class="text-center text-gray-600 mt-6">
                    ¿Ya tienes cuenta? 
                    <Link href="/login" class="text-pink-600 hover:text-pink-700 font-medium">
                        Inicia sesión
                    </Link>
                </p>
            </div>

            <!-- Back to home -->
            <p class="text-center mt-6">
                <Link href="/" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Volver a la tienda
                </Link>
            </p>
        </div>
    </div>
</template>