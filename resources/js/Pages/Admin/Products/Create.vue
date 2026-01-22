<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    categories: Array,
})

const form = useForm({
    name: '',
    category_id: '',
    description: '',
    price: '',
    sale_price: '',
    stock: 0,
    is_featured: false,
    is_active: true,
    images: [],
})

const imagePreviews = ref([])

const handleImagesChange = (e) => {
    const files = Array.from(e.target.files)
    form.images = files
    imagePreviews.value = files.map(file => URL.createObjectURL(file))
}

const removeImage = (index) => {
    const newImages = [...form.images]
    newImages.splice(index, 1)
    form.images = newImages
    imagePreviews.value.splice(index, 1)
}

const submit = () => {
    form.post('/admin/products', {
        forceFormData: true,
    })
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuevo Producto</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Info básica -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Información básica</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input 
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                            placeholder="Ej: Ramo de 25 Rosas Rojas"
                        >
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                        <select 
                            v-model="form.category_id"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        >
                            <option value="">Seleccionar categoría</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea 
                            v-model="form.description"
                            rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                            placeholder="Descripción del producto..."
                        ></textarea>
                    </div>
                </div>

                <!-- Precios -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Precios e inventario</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio (S/) *</label>
                            <input 
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                                placeholder="150.00"
                            >
                            <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio oferta (S/)</label>
                            <input 
                                v-model="form.sale_price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                                placeholder="Dejar vacío si no hay oferta"
                            >
                            <p v-if="form.errors.sale_price" class="text-red-500 text-sm mt-1">{{ form.errors.sale_price }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input 
                            v-model="form.stock"
                            type="number"
                            min="0"
                            class="w-32 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                        >
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Imágenes</h2>
                    
                    <div>
                        <input 
                            type="file"
                            accept="image/*"
                            multiple
                            @change="handleImagesChange"
                            class="w-full"
                        >
                        <p class="text-sm text-gray-500 mt-1">La primera imagen será la principal</p>
                    </div>

                    <div v-if="imagePreviews.length > 0" class="grid grid-cols-4 gap-4">
                        <div v-for="(preview, index) in imagePreviews" :key="index" class="relative">
                            <img :src="preview" class="w-full aspect-square object-cover rounded-lg">
                            <button 
                                type="button"
                                @click="removeImage(index)"
                                class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-sm hover:bg-red-600"
                            >
                                ×
                            </button>
                            <span v-if="index === 0" class="absolute bottom-1 left-1 bg-pink-600 text-white text-xs px-2 py-0.5 rounded">
                                Principal
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Opciones -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Opciones</h2>
                    
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 text-pink-600 rounded">
                            <span class="text-sm text-gray-700">Producto destacado</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.is_active" class="w-4 h-4 text-pink-600 rounded">
                            <span class="text-sm text-gray-700">Activo</span>
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : 'Crear Producto' }}
                    </button>
                    <Link href="/admin/products" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>