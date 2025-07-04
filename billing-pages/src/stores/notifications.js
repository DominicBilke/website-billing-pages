import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notifications', () => {
  // State
  const notifications = ref([])
  let nextId = 1

  // Actions
  const addNotification = (notification) => {
    const id = nextId++
    const newNotification = {
      id,
      type: 'info',
      message: '',
      duration: 5000,
      ...notification
    }
    
    notifications.value.push(newNotification)
    
    // Auto-remove notification after duration
    if (newNotification.duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }
    
    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearNotifications = () => {
    notifications.value = []
  }

  // Convenience methods
  const success = (message, options = {}) => {
    return addNotification({
      type: 'success',
      message,
      ...options
    })
  }

  const error = (message, options = {}) => {
    return addNotification({
      type: 'error',
      message,
      duration: 8000, // Longer duration for errors
      ...options
    })
  }

  const warning = (message, options = {}) => {
    return addNotification({
      type: 'warning',
      message,
      ...options
    })
  }

  const info = (message, options = {}) => {
    return addNotification({
      type: 'info',
      message,
      ...options
    })
  }

  return {
    // State
    notifications,
    
    // Actions
    addNotification,
    removeNotification,
    clearNotifications,
    
    // Convenience methods
    success,
    error,
    warning,
    info
  }
}) 