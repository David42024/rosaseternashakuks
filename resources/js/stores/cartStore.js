// stores/cartStore.js - VERSIÓN MEJORADA
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export const useCartStore = defineStore('cart', () => {
  const count = ref(0)
  const items = ref([])
  
  // Inicializar desde localStorage
  const initialize = () => {
    try {
      const saved = localStorage.getItem('hakuks_cart')
      if (saved) {
        const data = JSON.parse(saved)
        count.value = data.count || 0
        items.value = data.items || []
      }
    } catch (error) {
      console.error('Error loading cart:', error)
      reset()
    }
  }
  
  // Guardar en localStorage
  const saveToStorage = () => {
    try {
      localStorage.setItem('hakuks_cart', JSON.stringify({
        count: count.value,
        items: items.value,
        updatedAt: new Date().toISOString()
      }))
    } catch (error) {
      console.error('Error saving cart:', error)
    }
  }
  
  // Obtener cantidad de un producto específico
  const getProductQuantity = (productId) => {
    const item = items.value.find(i => i.id === productId)
    return item ? item.quantity : 0
  }
  
  // Verificar si se puede agregar más cantidad (validación de stock)
  const canAddMore = (productId, quantityToAdd = 1, productStock) => {
    const currentQuantity = getProductQuantity(productId)
    return (currentQuantity + quantityToAdd) <= productStock
  }
  
  // Obtener cantidad máxima que se puede agregar
  const getMaxAddableQuantity = (productId, productStock) => {
    const currentQuantity = getProductQuantity(productId)
    return Math.max(0, productStock - currentQuantity)
  }
  
  // Agregar producto con validación de stock
  const addItem = async (productId, quantity = 1, productStock = Infinity) => {
    try {
      // Validar stock primero
      if (!canAddMore(productId, quantity, productStock)) {
        const current = getProductQuantity(productId)
        throw new Error(`No puedes agregar ${quantity} más. Ya tienes ${current} y el stock máximo es ${productStock}.`)
      }
      
      // Verificar si ya existe en el carrito
      const existingItemIndex = items.value.findIndex(i => i.id === productId)
      const currentQuantity = existingItemIndex >= 0 ? items.value[existingItemIndex].quantity : 0
      
      // Actualización optimista (local)
      if (existingItemIndex >= 0) {
        items.value[existingItemIndex].quantity = currentQuantity + quantity
      } else {
        items.value.push({
          id: productId,
          quantity: quantity,
          addedAt: new Date().toISOString()
        })
      }
      
      // Actualizar contador total
      count.value += quantity
      saveToStorage()
      
      // Enviar al backend
      await router.post('/carrito/agregar', {
        product_id: productId,
        quantity: quantity
      }, {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
          // Revertir si hay error
          if (existingItemIndex >= 0) {
            items.value[existingItemIndex].quantity = currentQuantity
          } else {
            items.value = items.value.filter(i => i.id !== productId)
          }
          count.value -= quantity
          saveToStorage()
          throw errors
        }
      })
      
      return { 
        success: true, 
        currentQuantity: currentQuantity + quantity,
        message: `✅ ${quantity} unidad(es) agregada(s) al carrito`
      }
    } catch (error) {
      console.error('Error adding to cart:', error)
      return { 
        success: false, 
        error: error.message,
        type: error.response?.status === 422 ? 'validation' : 'general'
      }
    }
  }
  
  // Actualizar cantidad de un item
  const updateItemQuantity = async (productId, newQuantity, productStock) => {
    const existingItemIndex = items.value.findIndex(i => i.id === productId)
    if (existingItemIndex === -1) return { success: false, error: 'Item no encontrado' }
    
    const oldQuantity = items.value[existingItemIndex].quantity
    
    // Validar stock
    if (newQuantity > productStock) {
      return { 
        success: false, 
        error: `No puedes tener ${newQuantity} unidades. El stock máximo es ${productStock}.` 
      }
    }
    
    // Actualizar localmente
    items.value[existingItemIndex].quantity = newQuantity
    count.value += (newQuantity - oldQuantity)
    saveToStorage()
    
    return { success: true, quantity: newQuantity }
  }
  
  // Resetear
  const reset = () => {
    count.value = 0
    items.value = []
    saveToStorage()
  }
  
  // Computadas
  const totalItems = computed(() => count.value)
  const hasItems = computed(() => count.value > 0)
  const itemCount = computed(() => items.value.length)
  
  return {
    // State
    count,
    items,
    
    // Getters
    totalItems,
    hasItems,
    itemCount,
    getProductQuantity,
    canAddMore,
    getMaxAddableQuantity,
    
    // Actions
    initialize,
    addItem,
    updateItemQuantity,
    reset,
    saveToStorage
  }
})