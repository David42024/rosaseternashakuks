<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    stats: Object,
    recentOrders: Array,
    topProducts: Array,
})
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Productos</p>
                        <p class="text-3xl font-bold text-gray-900">{{ stats.totalProducts }}</p>
                    </div>
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📦</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pedidos Pendientes</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ stats.pendingOrders }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">⏳</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pedidos Hoy</p>
                        <p class="text-3xl font-bold text-blue-600">{{ stats.todayOrders }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📋</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Ventas del Mes</p>
                        <p class="text-3xl font-bold text-green-600">S/{{ stats.monthRevenue }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pedidos Recientes -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Pedidos Recientes</h2>
                    <Link href="/admin/orders" class="text-pink-600 hover:text-pink-700 text-sm font-medium">
                        Ver todos →
                    </Link>
                </div>

                <div v-if="recentOrders.length > 0" class="space-y-4">
                    <div 
                        v-for="order in recentOrders" 
                        :key="order.id"
                        class="flex items-center justify-between py-3 border-b last:border-b-0"
                    >
                        <div>
                            <p class="font-medium text-gray-900">#{{ order.id }} - {{ order.customer_name }}</p>
                            <p class="text-sm text-gray-500">{{ order.items_count }} items</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">S/{{ order.total }}</p>
                            <span :class="[
                                'text-xs px-2 py-1 rounded-full',
                                order.status === 'pendiente' ? 'bg-yellow-100 text-yellow-800' :
                                order.status === 'confirmado' ? 'bg-blue-100 text-blue-800' :
                                order.status === 'preparando' ? 'bg-purple-100 text-purple-800' :
                                order.status === 'entregado' ? 'bg-green-100 text-green-800' :
                                'bg-red-100 text-red-800'
                            ]">
                                {{ order.status_label }}
                            </span>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-4">No hay pedidos recientes</p>
            </div>

            <!-- Top Productos -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Productos Más Vendidos</h2>
                    <Link href="/admin/products" class="text-pink-600 hover:text-pink-700 text-sm font-medium">
                        Ver todos →
                    </Link>
                </div>

                <div v-if="topProducts.length > 0" class="space-y-4">
                    <div 
                        v-for="(product, index) in topProducts" 
                        :key="product.id"
                        class="flex items-center gap-4 py-3 border-b last:border-b-0"
                    >
                        <span class="text-lg font-bold text-gray-400 w-6">{{ index + 1 }}</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ product.name }}</p>
                            <p class="text-sm text-gray-500">{{ product.order_items_count }} vendidos</p>
                        </div>
                        <span class="font-semibold text-pink-600">S/{{ product.price }}</span>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-4">No hay datos de ventas</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <Link 
                href="/admin/products/create"
                class="bg-pink-600 text-white rounded-xl p-4 text-center hover:bg-pink-700 transition"
            >
                <span class="text-2xl block mb-2">➕</span>
                <span class="font-medium">Nuevo Producto</span>
            </Link>
            <Link 
                href="/admin/categories/create"
                class="bg-purple-600 text-white rounded-xl p-4 text-center hover:bg-purple-700 transition"
            >
                <span class="text-2xl block mb-2">🏷️</span>
                <span class="font-medium">Nueva Categoría</span>
            </Link>
            <Link 
                href="/admin/orders?status=pendiente"
                class="bg-yellow-500 text-white rounded-xl p-4 text-center hover:bg-yellow-600 transition"
            >
                <span class="text-2xl block mb-2">⏳</span>
                <span class="font-medium">Pedidos Pendientes</span>
            </Link>
            <Link 
                href="/"
                target="_blank"
                class="bg-gray-600 text-white rounded-xl p-4 text-center hover:bg-gray-700 transition"
            >
                <span class="text-2xl block mb-2">👁️</span>
                <span class="font-medium">Ver Tienda</span>
            </Link>
        </div>
    </AdminLayout>
</template>