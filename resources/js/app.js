import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
//import { ZiggyVue } from 'ziggy-js'

// Crear Pinia con plugin de persistencia
const pinia = createPinia()

// Opcional: Middleware global para Pinia
pinia.use(({ store }) => {
    // Inicializar el store del carrito automáticamente
    if (store.$id === 'cart') {
        // Cargar datos persistentes al inicializar
        store.$hydrate?.()
        
        // Sincronizar al recibir foco en la ventana
        if (typeof window !== 'undefined') {
            window.addEventListener('focus', () => {
                store.syncWithBackend?.()
            })
        }
    }
})

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        
        app.use(plugin)
        app.use(pinia)
        
        app.mount(el)
        
        return app
    },
    progress: {
        color: '#ec4899',
        showSpinner: true,
    },
})