<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    category: Object,
})

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
    image: null,
    is_active: props.category.is_active,
})

// Usar image_url del accessor
const imagePreview = ref(props.category.image_url || null)

const handleImageChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

const submit = () => {
    const formData = new FormData()
    
    formData.append('_method', 'PUT')
    formData.append('name', form.name)
    formData.append('description', form.description || '')
    formData.append('is_active', form.is_active ? '1' : '0')
    
    if (form.image) {
        formData.append('image', form.image)
    }
    
    router.post(`/admin/categories/${props.category.id}`, formData)
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-2xl">
            <div class="mb-6">
                <Link href="/admin/categories" class="text-pink-600 hover:text-pink-700 text-sm">
                    ← Volver a categorías
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Editar Categoría</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre *
                    </label>
                    <input 
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    >
                    <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea 
                        v-model="form.description"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    ></textarea>
                </div>

                <!-- Imagen -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Imagen
                    </label>
                    <div class="flex items-center gap-4">
                        <div v-if="imagePreview" class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100">
                            <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center">
                            <span class="text-3xl">🌹</span>
                        </div>
                        <div>
                            <input 
                                type="file"
                                accept="image/*"
                                @change="handleImageChange"
                            >
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG o WebP. Máx 2MB</p>
                        </div>
                    </div>
                    <p v-if="form.errors.image" class="text-red-500 text-sm mt-1">
                        {{ form.errors.image }}
                    </p>
                </div>

                <!-- Activa -->
                <div class="flex items-center gap-2">
                    <input 
                        type="checkbox"
                        v-model="form.is_active"
                        id="is_active"
                        class="w-4 h-4 text-pink-600 rounded focus:ring-pink-500"
                    >
                    <label for="is_active" class="text-sm text-gray-700">
                        Categoría activa (visible en tienda)
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : 'Actualizar Categoría' }}
                    </button>
                    <Link 
                        href="/admin/categories"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>