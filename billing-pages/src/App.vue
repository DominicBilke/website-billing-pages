<template>
  <div id="app" class="app">
    <!-- Navigation -->
    <nav v-if="isAuthenticated" class="navbar">
      <div class="navbar-container">
        <div class="navbar-brand">
          <img src="/assets/images/logo.png" alt="Billing Pages" class="navbar-logo" />
          <span class="navbar-title">Billing Pages</span>
        </div>
        
        <div class="navbar-menu">
          <router-link to="/dashboard" class="navbar-item">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </router-link>
          
          <div class="navbar-dropdown">
            <button class="navbar-dropdown-toggle">
              <i class="fas fa-building"></i>
              <span>Companies</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="navbar-dropdown-menu">
              <router-link to="/companies" class="navbar-dropdown-item">Overview</router-link>
              <router-link to="/companies/employees" class="navbar-dropdown-item">Employees</router-link>
              <router-link to="/companies/reports" class="navbar-dropdown-item">Reports</router-link>
            </div>
          </div>
          
          <div class="navbar-dropdown">
            <button class="navbar-dropdown-toggle">
              <i class="fas fa-route"></i>
              <span>Tours</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="navbar-dropdown-menu">
              <router-link to="/tours" class="navbar-dropdown-item">Overview</router-link>
              <router-link to="/tours/maps" class="navbar-dropdown-item">Maps</router-link>
              <router-link to="/tours/reports" class="navbar-dropdown-item">Reports</router-link>
            </div>
          </div>
          
          <div class="navbar-dropdown">
            <button class="navbar-dropdown-toggle">
              <i class="fas fa-briefcase"></i>
              <span>Work</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="navbar-dropdown-menu">
              <router-link to="/work" class="navbar-dropdown-item">Overview</router-link>
              <router-link to="/work/timesheets" class="navbar-dropdown-item">Timesheets</router-link>
              <router-link to="/work/reports" class="navbar-dropdown-item">Reports</router-link>
            </div>
          </div>
          
          <div class="navbar-dropdown">
            <button class="navbar-dropdown-toggle">
              <i class="fas fa-tasks"></i>
              <span>Tasks</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="navbar-dropdown-menu">
              <router-link to="/tasks" class="navbar-dropdown-item">Overview</router-link>
              <router-link to="/tasks/projects" class="navbar-dropdown-item">Projects</router-link>
              <router-link to="/tasks/reports" class="navbar-dropdown-item">Reports</router-link>
            </div>
          </div>
          
          <div class="navbar-dropdown">
            <button class="navbar-dropdown-toggle">
              <i class="fas fa-euro-sign"></i>
              <span>Money</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="navbar-dropdown-menu">
              <router-link to="/money" class="navbar-dropdown-item">Overview</router-link>
              <router-link to="/money/invoices" class="navbar-dropdown-item">Invoices</router-link>
              <router-link to="/money/reports" class="navbar-dropdown-item">Reports</router-link>
            </div>
          </div>
        </div>
        
        <div class="navbar-user">
          <div class="navbar-dropdown">
            <button class="navbar-user-toggle">
              <img :src="userAvatar" alt="User" class="navbar-user-avatar" />
              <span class="navbar-user-name">{{ userName }}</span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="navbar-dropdown-menu">
              <router-link to="/profile" class="navbar-dropdown-item">
                <i class="fas fa-user"></i>
                Profile
              </router-link>
              <router-link to="/settings" class="navbar-dropdown-item">
                <i class="fas fa-cog"></i>
                Settings
              </router-link>
              <div class="navbar-dropdown-divider"></div>
              <button @click="logout" class="navbar-dropdown-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <router-view />
    </main>

    <!-- Notifications -->
    <div class="notifications" v-if="notifications.length">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="['notification', `notification-${notification.type}`]"
      >
        <div class="notification-content">
          <i :class="notificationIcon(notification.type)"></i>
          <span>{{ notification.message }}</span>
        </div>
        <button @click="removeNotification(notification.id)" class="notification-close">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Loading...</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'

export default {
  name: 'App',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const notificationStore = useNotificationStore()
    
    const isLoading = ref(false)
    
    const isAuthenticated = computed(() => authStore.isAuthenticated)
    const userName = computed(() => authStore.user?.name || 'User')
    const userAvatar = computed(() => authStore.user?.avatar || '/assets/images/default-avatar.png')
    const notifications = computed(() => notificationStore.notifications)
    
    const notificationIcon = (type) => {
      const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
      }
      return icons[type] || icons.info
    }
    
    const removeNotification = (id) => {
      notificationStore.removeNotification(id)
    }
    
    const logout = async () => {
      try {
        await authStore.logout()
        router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
      }
    }
    
    onMounted(() => {
      // Initialize app
      authStore.initialize()
    })
    
    return {
      isLoading,
      isAuthenticated,
      userName,
      userAvatar,
      notifications,
      notificationIcon,
      removeNotification,
      logout
    }
  }
}
</script>

<style lang="scss" scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: white;
  padding: 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}

.navbar-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.25rem;
  font-weight: 600;
}

.navbar-logo {
  height: 40px;
  width: auto;
}

.navbar-title {
  @media (max-width: 768px) {
    display: none;
  }
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
  
  @media (max-width: 1024px) {
    display: none;
  }
}

.navbar-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  
  &:hover {
    background: rgba(255, 255, 255, 0.1);
  }
  
  &.router-link-active {
    background: rgba(255, 255, 255, 0.2);
  }
}

.navbar-dropdown {
  position: relative;
}

.navbar-dropdown-toggle {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s ease;
  
  &:hover {
    background: rgba(255, 255, 255, 0.1);
  }
}

.navbar-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s ease;
  
  .navbar-dropdown:hover & {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }
}

.navbar-dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  color: var(--text-color);
  text-decoration: none;
  transition: background 0.2s ease;
  
  &:hover {
    background: var(--background-light);
  }
  
  &.router-link-active {
    background: var(--primary-color);
    color: white;
  }
}

.navbar-dropdown-divider {
  height: 1px;
  background: var(--border-color);
  margin: 0.5rem 0;
}

.navbar-user {
  display: flex;
  align-items: center;
}

.navbar-user-toggle {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s ease;
  
  &:hover {
    background: rgba(255, 255, 255, 0.1);
  }
}

.navbar-user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.navbar-user-name {
  @media (max-width: 768px) {
    display: none;
  }
}

.main-content {
  flex: 1;
  padding: 2rem;
  background: var(--background-color);
  
  @media (max-width: 768px) {
    padding: 1rem;
  }
}

.notifications {
  position: fixed;
  top: 90px;
  right: 1rem;
  z-index: 1001;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-width: 400px;
}

.notification {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  animation: slideIn 0.3s ease;
  
  &-success {
    background: var(--success-color);
    color: white;
  }
  
  &-error {
    background: var(--danger-color);
    color: white;
  }
  
  &-warning {
    background: var(--warning-color);
    color: var(--text-color);
  }
  
  &-info {
    background: var(--info-color);
    color: white;
  }
}

.notification-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
}

.notification-close {
  background: none;
  border: none;
  color: inherit;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background 0.2s ease;
  
  &:hover {
    background: rgba(255, 255, 255, 0.2);
  }
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  font-size: 1.25rem;
  color: var(--primary-color);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style> 