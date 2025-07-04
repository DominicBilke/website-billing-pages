import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: () => import('@/Views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/Views/Login.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/companies',
    name: 'companies',
    component: () => import('@/Views/Companies.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tours',
    name: 'tours',
    component: () => import('@/Views/Tours.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/work',
    name: 'work',
    component: () => import('@/Views/Work.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks',
    name: 'tasks',
    component: () => import('@/Views/Tasks.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/invoices',
    name: 'invoices',
    component: () => import('@/Views/Invoices.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'reports',
    component: () => import('@/Views/Reports.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'settings',
    component: () => import('@/Views/Settings.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('@/Views/About.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/imprint',
    name: 'imprint',
    component: () => import('@/Views/Imprint.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/privacy-policy',
    name: 'privacy-policy',
    component: () => import('@/Views/PrivacyPolicy.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/terms',
    name: 'terms',
    component: () => import('@/Views/Terms.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/Views/NotFound.vue'),
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
  const isAuthenticated = localStorage.getItem('auth_token')
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.name === 'login' && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router 