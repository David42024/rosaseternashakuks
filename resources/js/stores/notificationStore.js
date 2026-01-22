// stores/notificationStore.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const show = ref(false)
  const message = ref('')
  const type = ref('success') // 'success' | 'error' | 'warning' | 'info'
  
  const notify = (msg, msgType = 'success', duration = 3000) => {
    message.value = msg
    type.value = msgType
    show.value = true
    
    // Auto ocultar después de duration
    setTimeout(() => {
      show.value = false
    }, duration)
  }
  
  const clear = () => {
    show.value = false
    message.value = ''
  }
  
  return { 
    show, 
    message, 
    type, 
    notify, 
    clear 
  }
})