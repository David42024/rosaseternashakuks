<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    category: Object, // null para crear, objeto para editar
})

const isEdit = !!props.category

const form = useForm({
    name: props.category?.name || '',
    description: props.category?.description || '',
    image: null,
    is_active: props.category?.is_active ?? true,
})
const imagePreview = ref(
    props.category?.image ? props.category.image : null
)
const handleImageChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

const submit = () => {
    if (isEdit) {
        form.post(`/admin/categories/${props.category.id}`, {
            _method: 'PUT',
            forceFormData: true,
        })
    } else {
        form.post('/admin/categories')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-2xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">
                {{ isEdit ? 'Editar Categoría' : 'Nueva Categoría' }}
            </h1>

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
                        placeholder="Ej: Rosas Rojas"
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
                        placeholder="Descripción de la categoría..."
                    ></textarea>
                    <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                        {{ form.errors.description }}
                    </p>
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
                        <input 
                            type="file"
                            accept="image/*"
                            @change="handleImageChange"
                            class="flex-1"
                        >
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
                        Categoría activa
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Categoría') }}
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