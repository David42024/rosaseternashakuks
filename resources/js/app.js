import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'

const pinia = createPinia()

pinia.use(({ store }) => {
    if (store.$id === 'cart') {
        store.$hydrate?.()
        
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
        
        // Limpiar carrito si no hay usuario logueado
        const auth = props.initialPage.props.auth
        if (!auth?.user) {
            // Limpiar localStorage del carrito
            localStorage.removeItem('cart')
        }
        
        return app
    },
    progress: {
        color: '#ec4899',
        showSpinner: true,
    },
})