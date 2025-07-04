import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'

// Import global styles
import './assets/styles/main.scss'

// Import components
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import BaseTable from '@/components/ui/BaseTable.vue'
import BaseForm from '@/components/ui/BaseForm.vue'

// Import views
import Dashboard from '@/views/Dashboard.vue'
import Login from '@/views/Login.vue'
import Companies from '@/views/Companies.vue'
import Tours from '@/views/Tours.vue'
import Work from '@/views/Work.vue'
import Tasks from '@/views/Tasks.vue'
import Money from '@/views/Money.vue'
import Profile from '@/views/Profile.vue'
import Settings from '@/views/Settings.vue'

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      meta: { requiresAuth: false }
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard,
      meta: { requiresAuth: true }
    },
    {
      path: '/companies',
      name: 'Companies',
      component: Companies,
      meta: { requiresAuth: true }
    },
    {
      path: '/tours',
      name: 'Tours',
      component: Tours,
      meta: { requiresAuth: true }
    },
    {
      path: '/work',
      name: 'Work',
      component: Work,
      meta: { requiresAuth: true }
    },
    {
      path: '/tasks',
      name: 'Tasks',
      component: Tasks,
      meta: { requiresAuth: true }
    },
    {
      path: '/money',
      name: 'Money',
      component: Money,
      meta: { requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'Profile',
      component: Profile,
      meta: { requiresAuth: true }
    },
    {
      path: '/settings',
      name: 'Settings',
      component: Settings,
      meta: { requiresAuth: true }
    }
  ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token')
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

// Create app
const app = createApp(App)

// Use plugins
app.use(createPinia())
app.use(router)

// Register global components
app.component('BaseButton', BaseButton)
app.component('BaseCard', BaseCard)
app.component('BaseModal', BaseModal)
app.component('BaseTable', BaseTable)
app.component('BaseForm', BaseForm)

// Global properties
app.config.globalProperties.$formatCurrency = (amount, currency = 'EUR') => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: currency
  }).format(amount)
}

app.config.globalProperties.$formatDate = (date) => {
  return new Intl.DateTimeFormat('de-DE').format(new Date(date))
}

// Mount app
app.mount('#app') 