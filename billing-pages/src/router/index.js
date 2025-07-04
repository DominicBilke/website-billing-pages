import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/Login.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/companies',
    name: 'companies',
    component: () => import('@/views/Companies.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tours',
    name: 'tours',
    component: () => import('@/views/Tours.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/work',
    name: 'work',
    component: () => import('@/views/Work.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks',
    name: 'tasks',
    component: () => import('@/views/Tasks.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/invoices',
    name: 'invoices',
    component: () => import('@/views/Invoices.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'reports',
    component: () => import('@/views/Reports.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'settings',
    component: () => import('@/views/Settings.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('@/views/About.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/imprint',
    name: 'imprint',
    component: () => import('@/views/Imprint.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/privacy-policy',
    name: 'privacy-policy',
    component: () => import('@/views/PrivacyPolicy.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/terms',
    name: 'terms',
    component: () => import('@/views/Terms.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFound.vue'),
    meta: { requiresAuth: false }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.name === 'login' && authStore.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router 