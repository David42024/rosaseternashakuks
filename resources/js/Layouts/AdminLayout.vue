<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const sidebarOpen = ref(true)

const menuItems = [
    { name: 'Dashboard', href: '/admin', icon: 'dashboard' },
    { name: 'Productos', href: '/admin/products', icon: 'products' },
    { name: 'Categorías', href: '/admin/categories', icon: 'categories' },
    { name: 'Pedidos', href: '/admin/orders', icon: 'orders' },
    { name: 'Usuarios', href: '/admin/users', icon: 'users' },
]

const isActive = (href) => {
    return page.url.startsWith(href)
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform transition-transform duration-200',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-gray-800">
                <Link href="/admin" class="flex items-center gap-2">
                    <span class="text-2xl">🌹</span>
                    <span class="text-lg font-bold text-white">Hakuks Admin</span>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="mt-8 px-4">
                <ul class="space-y-2">
                    <li v-for="item in menuItems" :key="item.name">
                        <Link 
                            :href="item.href"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg transition',
                                isActive(item.href) 
                                    ? 'bg-pink-600 text-white' 
                                    : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                            ]"
                        >
                            <!-- Icons -->
                            <svg v-if="item.icon === 'dashboard'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <svg v-if="item.icon === 'products'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <svg v-if="item.icon === 'categories'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <svg v-if="item.icon === 'orders'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <svg v-if="item.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{{ item.name }}</span>
                        </Link>
                    </li>
                </ul>
            </nav>

            <!-- Back to site -->
            <div class="absolute bottom-4 left-4 right-4">
                <Link 
                    href="/"
                    class="flex items-center justify-center gap-2 px-4 py-3 text-gray-400 hover:text-white transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Volver al sitio</span>
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div :class="['transition-all duration-200', sidebarOpen ? 'ml-64' : 'ml-0']">
            <!-- Top bar -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <!-- Toggle sidebar -->
                <button 
                    @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- User menu -->
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">{{ user?.name }}</span>
                    <Link 
                        href="/logout" 
                        method="post" 
                        as="button"
                        class="text-red-600 hover:text-red-800"
                    >
                        Cerrar sesión
                    </Link>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>