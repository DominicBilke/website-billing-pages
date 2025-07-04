import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userRole = computed(() => user.value?.role || 'user')
  const userPermissions = computed(() => user.value?.permissions || [])

  // Actions
  const login = async (credentials) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.post('/api/auth/login', credentials)
      const { user: userData, token: authToken } = response.data
      
      // Store token and user data
      token.value = authToken
      user.value = userData
      localStorage.setItem('auth_token', authToken)
      
      // Set axios default header
      axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
      
      return userData
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const register = async (userData) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.post('/api/auth/register', userData)
      const { user: newUser, token: authToken } = response.data
      
      // Store token and user data
      token.value = authToken
      user.value = newUser
      localStorage.setItem('auth_token', authToken)
      
      // Set axios default header
      axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
      
      return newUser
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      // Call logout endpoint if available
      if (token.value) {
        await axios.post('/api/auth/logout')
      }
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      // Clear local state regardless of API call success
      clearAuth()
    }
  }

  const clearAuth = () => {
    user.value = null
    token.value = null
    error.value = null
    localStorage.removeItem('auth_token')
    delete axios.defaults.headers.common['Authorization']
  }

  const fetchUser = async () => {
    if (!token.value) return null
    
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get('/api/auth/user')
      user.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      // If token is invalid, clear auth
      if (err.response?.status === 401) {
        clearAuth()
      }
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const updateProfile = async (profileData) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.put('/api/auth/profile', profileData)
      user.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update profile'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const changePassword = async (passwordData) => {
    try {
      isLoading.value = true
      error.value = null
      
      await axios.put('/api/auth/password', passwordData)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to change password'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const initialize = async () => {
    if (token.value) {
      // Set axios default header
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      
      // Fetch user data
      try {
        await fetchUser()
      } catch (err) {
        console.error('Failed to initialize auth:', err)
        clearAuth()
      }
    }
  }

  const hasPermission = (permission) => {
    return userPermissions.value.includes(permission)
  }

  const hasRole = (role) => {
    return userRole.value === role
  }

  return {
    // State
    user,
    token,
    isLoading,
    error,
    
    // Getters
    isAuthenticated,
    userRole,
    userPermissions,
    
    // Actions
    login,
    register,
    logout,
    clearAuth,
    fetchUser,
    updateProfile,
    changePassword,
    initialize,
    hasPermission,
    hasRole
  }
}) 