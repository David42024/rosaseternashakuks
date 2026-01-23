<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    product: Object,
    categories: Array,
})

const form = useForm({
    name: props.product.name,
    category_id: props.product.category_id,
    description: props.product.description || '',
    price: props.product.price,
    sale_price: props.product.sale_price || '',
    stock: props.product.stock,
    is_featured: props.product.is_featured,
    is_active: props.product.is_active,
})

const newImages = ref([])
const newImagePreviews = ref([])

const handleImagesChange = (e) => {
    const files = Array.from(e.target.files)
    newImages.value = files
    newImagePreviews.value = files.map(file => URL.createObjectURL(file))
}

const removeNewImage = (index) => {
    newImages.value.splice(index, 1)
    newImagePreviews.value.splice(index, 1)
}

const deleteExistingImage = (imageId) => {
    if (confirm('¿Eliminar esta imagen?')) {
        router.delete(`/admin/products/images/${imageId}`, {
            preserveScroll: true,
        })
    }
}

const setPrimaryImage = (imageId) => {
    router.patch(`/admin/products/images/${imageId}/primary`, {}, {
        preserveScroll: true,
    })
}

const submit = () => {
    const formData = new FormData()
    
    formData.append('_method', 'PUT')
    formData.append('name', form.name)
    formData.append('category_id', form.category_id)
    formData.append('description', form.description)
    formData.append('price', form.price)
    formData.append('sale_price', form.sale_price || '')
    formData.append('stock', form.stock)
    formData.append('is_featured', form.is_featured ? '1' : '0')
    formData.append('is_active', form.is_active ? '1' : '0')
    
    newImages.value.forEach((image, index) => {
        formData.append(`images[${index}]`, image)
    })
    
    router.post(`/admin/products/${props.product.id}`, formData, {
        forceFormData: true,
    })
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl">
            <div class="mb-6">
                <Link href="/admin/products" class="text-pink-600 hover:text-pink-700 text-sm">
                    ← Volver a productos
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Editar Producto</h1>
            </div>

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
                        ></textarea>
                    </div>
                </div>

                <!-- Precios -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Precios e inventario</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio (MXN) *</label>
                            <input 
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500"
                            >
                            <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio oferta (MXN)</label>
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

                <!-- Imágenes existentes -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Imágenes actuales</h2>
                    
                    <div v-if="product.images && product.images.length > 0" class="grid grid-cols-4 gap-4">
                        <div v-for="image in product.images" :key="image.id" class="relative group">
                            <img 
                                :src="image.url || `https://res.cloudinary.com/${$page.props.cloudinaryCloudName || 'dl4y5pap8'}/image/upload/${image.image_path}.jpg`"
                                :alt="product.name"
                                class="w-full aspect-square object-cover rounded-lg"
                            >
                            <!-- Badge principal -->
                            <span 
                                v-if="image.is_primary" 
                                class="absolute top-2 left-2 bg-pink-600 text-white text-xs px-2 py-1 rounded"
                            >
                                Principal
                            </span>
                            <!-- Acciones -->
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">
                                <button 
                                    v-if="!image.is_primary"
                                    type="button"
                                    @click="setPrimaryImage(image.id)"
                                    class="bg-white text-gray-800 px-2 py-1 rounded text-xs hover:bg-gray-100"
                                >
                                    Principal
                                </button>
                                <button 
                                    type="button"
                                    @click="deleteExistingImage(image.id)"
                                    class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-gray-500">No hay imágenes</p>
                </div>

                <!-- Nuevas imágenes -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Agregar nuevas imágenes</h2>
                    
                    <div>
                        <input 
                            type="file"
                            accept="image/*"
                            multiple
                            @change="handleImagesChange"
                            class="w-full"
                        >
                    </div>

                    <div v-if="newImagePreviews.length > 0" class="grid grid-cols-4 gap-4">
                        <div v-for="(preview, index) in newImagePreviews" :key="index" class="relative">
                            <img :src="preview" class="w-full aspect-square object-cover rounded-lg">
                            <button 
                                type="button"
                                @click="removeNewImage(index)"
                                class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-sm hover:bg-red-600"
                            >
                                ×
                            </button>
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
                            <span class="text-sm text-gray-700">Activo (visible en tienda)</span>
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
                        {{ form.processing ? 'Guardando...' : 'Actualizar Producto' }}
                    </button>
                    <Link href="/admin/products" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>