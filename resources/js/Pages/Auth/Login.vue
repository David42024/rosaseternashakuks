<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
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
                    Iniciar Sesión
                </h1>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Correo electrónico
                        </label>
                        <input 
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition"
                            placeholder="tu@email.com"
                        >
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                            {{ form.errors.email }}
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
                            placeholder="••••••••"
                        >
                        <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                v-model="form.remember"
                                class="w-4 h-4 text-pink-600 rounded focus:ring-pink-500"
                            >
                            <span class="text-sm text-gray-600">Recordarme</span>
                        </label>
                        <Link href="/forgot-password" class="text-sm text-pink-600 hover:text-pink-700">
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <!-- Submit -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-pink-600 text-white py-3 rounded-lg font-semibold hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ form.processing ? 'Ingresando...' : 'Ingresar' }}
                    </button>
                </form>

                <!-- Register link -->
                <p class="text-center text-gray-600 mt-6">
                    ¿No tienes cuenta? 
                    <Link href="/register" class="text-pink-600 hover:text-pink-700 font-medium">
                        Regístrate
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