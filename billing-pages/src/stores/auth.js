import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role || 'user')
  const userPermissions = computed(() => user.value?.permissions || [])

  // Actions
  const login = async (credentials) => {
    isLoading.value = true
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Mock response
      const response = {
        user: {
          id: 1,
          name: 'John Doe',
          email: credentials.email,
          role: 'admin'
        },
        token: 'mock_jwt_token_' + Date.now()
      }
      
      user.value = response.user
      token.value = response.token
      localStorage.setItem('auth_token', response.token)
      
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
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

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  const checkAuth = async () => {
    if (!token.value) return false
    
    try {
      // Simulate API call to verify token
      await new Promise(resolve => setTimeout(resolve, 500))
      
      // Mock user data
      user.value = {
        id: 1,
        name: 'John Doe',
        email: 'john.doe@example.com',
        role: 'admin'
      }
      
      return true
    } catch (error) {
      logout()
      return false
    }
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
        logout()
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
        logout()
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
    checkAuth,
    fetchUser,
    updateProfile,
    changePassword,
    initialize,
    hasPermission,
    hasRole
  }
}) 