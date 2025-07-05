<template>
  <nav class="navigation" :class="{ 'nav-open': isNavOpen }">
    <div class="nav-header">
      <div class="nav-brand">
        <img src="../assets/logo-billing-pages.png" alt="Billing Pages Logo" class="nav-logo" />
        <span class="nav-title">Billing Pages</span>
      </div>
      
      <button class="nav-toggle" @click="toggleNav" :aria-expanded="isNavOpen">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </div>

    <div class="nav-content">
      <div class="nav-section">
        <h3 class="nav-section-title">Main</h3>
        <ul class="nav-menu">
          <li>
            <router-link to="/" class="nav-link" @click="closeNav">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/companies" class="nav-link" @click="closeNav">
              <i class="fas fa-building"></i>
              <span>Companies</span>
            </router-link>
          </li>
          <li>
            <router-link to="/tours" class="nav-link" @click="closeNav">
              <i class="fas fa-route"></i>
              <span>Tours</span>
            </router-link>
          </li>
          <li>
            <router-link to="/work" class="nav-link" @click="closeNav">
              <i class="fas fa-clock"></i>
              <span>Work Hours</span>
            </router-link>
          </li>
          <li>
            <router-link to="/tasks" class="nav-link" @click="closeNav">
              <i class="fas fa-tasks"></i>
              <span>Tasks</span>
            </router-link>
          </li>
          <li>
            <router-link to="/invoices" class="nav-link" @click="closeNav">
              <i class="fas fa-file-invoice-dollar"></i>
              <span>Invoices</span>
            </router-link>
          </li>
          <li>
            <router-link to="/reports" class="nav-link" @click="closeNav">
              <i class="fas fa-chart-line"></i>
              <span>Reports</span>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="nav-section">
        <h3 class="nav-section-title">System</h3>
        <ul class="nav-menu">
          <li>
            <router-link to="/settings" class="nav-link" @click="closeNav">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </router-link>
          </li>
          <li>
            <router-link to="/about" class="nav-link" @click="closeNav">
              <i class="fas fa-info-circle"></i>
              <span>About</span>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="nav-section">
        <h3 class="nav-section-title">Legal</h3>
        <ul class="nav-menu">
          <li>
            <router-link to="/imprint" class="nav-link" @click="closeNav">
              <i class="fas fa-file-contract"></i>
              <span>Imprint</span>
            </router-link>
          </li>
          <li>
            <router-link to="/privacy-policy" class="nav-link" @click="closeNav">
              <i class="fas fa-shield-alt"></i>
              <span>Privacy Policy</span>
            </router-link>
          </li>
          <li>
            <router-link to="/terms" class="nav-link" @click="closeNav">
              <i class="fas fa-gavel"></i>
              <span>Terms & Conditions</span>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="nav-footer">
        <div class="user-info">
          <div class="user-avatar">
            <i class="fas fa-user"></i>
          </div>
          <div class="user-details">
            <div class="user-name">{{ user?.name || 'User' }}</div>
            <div class="user-email">{{ user?.email || 'user@example.com' }}</div>
          </div>
        </div>
        
        <div class="nav-actions">
          <button class="nav-action-btn" @click="toggleTheme" :title="theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
            <i :class="theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'"></i>
          </button>
          <button class="nav-action-btn" @click="logout" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const router = useRouter()
const authStore = useAuthStore()
const themeStore = useThemeStore()

const isNavOpen = ref(false)

const user = computed(() => authStore.user)
const theme = computed(() => themeStore.theme)

const toggleNav = () => {
  isNavOpen.value = !isNavOpen.value
}

const closeNav = () => {
  isNavOpen.value = false
}

const toggleTheme = () => {
  themeStore.toggleTheme()
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
  closeNav()
}
</script>

<style scoped lang="scss">
.navigation {
  width: 280px;
  height: 100vh;
  background: var(--color-background-secondary);
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
  transition: transform 0.3s ease;
  
  @media (max-width: 768px) {
    transform: translateX(-100%);
    
    &.nav-open {
      transform: translateX(0);
    }
  }
}

.nav-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--color-border);
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.nav-logo {
  width: 32px;
  height: 32px;
  object-fit: contain;
}

.nav-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--color-primary);
}

.nav-toggle {
  display: none;
  flex-direction: column;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  
  @media (max-width: 768px) {
    display: flex;
  }
}

.hamburger-line {
  width: 20px;
  height: 2px;
  background: var(--color-text-primary);
  transition: all 0.3s ease;
}

.nav-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.nav-section {
  padding: 1.5rem 0;
  border-bottom: 1px solid var(--color-border);
  
  &:last-of-type {
    border-bottom: none;
  }
}

.nav-section-title {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 1.5rem 1rem;
}

.nav-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: var(--color-text-secondary);
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  
  &:hover {
    background: var(--color-background-tertiary);
    color: var(--color-text-primary);
  }
  
  &.router-link-active {
    background: var(--color-primary-light);
    color: var(--color-primary);
    border-left-color: var(--color-primary);
  }
  
  i {
    width: 16px;
    text-align: center;
    font-size: 0.875rem;
  }
  
  span {
    font-weight: 500;
  }
}

.nav-footer {
  margin-top: auto;
  padding: 1.5rem;
  border-top: 1px solid var(--color-border);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: var(--color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-weight: 600;
  color: var(--color-text-primary);
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.user-email {
  color: var(--color-text-secondary);
  font-size: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-actions {
  display: flex;
  gap: 0.5rem;
}

.nav-action-btn {
  width: 36px;
  height: 36px;
  background: var(--color-background-tertiary);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  color: var(--color-text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  
  &:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
  }
  
  i {
    font-size: 0.875rem;
  }
}

// Dark theme adjustments
[data-theme="dark"] .navigation {
  background: var(--color-background-secondary-dark);
  border-right-color: var(--color-border-dark);
  
  .nav-section {
    border-bottom-color: var(--color-border-dark);
  }
  
  .nav-link {
    &.router-link-active {
      background: var(--color-primary-dark);
    }
  }
  
  .nav-footer {
    border-top-color: var(--color-border-dark);
  }
  
  .nav-action-btn {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
}

// Mobile overlay
@media (max-width: 768px) {
  .navigation::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: -1;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }
  
  .navigation.nav-open::before {
    opacity: 1;
    visibility: visible;
  }
}
</style> 