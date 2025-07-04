<template>
  <div id="app">
    <!-- Loading state -->
    <div v-if="isLoading" class="app-loading">
      <div class="loading-spinner">
        <i class="fas fa-spinner"></i>
        <span>Loading Billing Pages...</span>
      </div>
    </div>
    
    <!-- App content -->
    <div v-else class="app-content">
      <!-- Navigation for authenticated users -->
      <Navigation v-if="isAuthenticated && !isPublicRoute" />
      
      <!-- Main content -->
      <main class="main-content" :class="{ 'with-nav': isAuthenticated && !isPublicRoute }">
        <router-view />
      </main>
      
      <!-- Footer for public pages -->
      <Footer v-if="isPublicRoute" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import Navigation from '@/components/Navigation.vue'
import Footer from '@/components/Footer.vue'

const route = useRoute()
const authStore = useAuthStore()
const themeStore = useThemeStore()

const isLoading = ref(true)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isPublicRoute = computed(() => {
  return ['login', 'about', 'imprint', 'privacy-policy', 'terms', 'not-found'].includes(route.name)
})

onMounted(async () => {
  try {
    // Check authentication status
    await authStore.checkAuth()
  } catch (error) {
    console.error('Auth check failed:', error)
  } finally {
    // Hide loading screen
    isLoading.value = false
  }
})
</script>

<style scoped lang="scss">
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
}

.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  color: white;
  
  i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
  }
  
  span {
    font-size: 1.125rem;
    font-weight: 500;
  }
}

.app-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  
  &.with-nav {
    margin-top: var(--header-height);
  }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

// Dark theme adjustments
[data-theme="dark"] {
  .app-loading {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  }
}
</style> 