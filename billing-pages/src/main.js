import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'

// Import global styles
import './assets/styles/main.scss'

// Create app
const app = createApp(App)

// Use plugins
app.use(createPinia())
app.use(router)

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

// Dispatch app-ready event to hide loading screen
window.dispatchEvent(new Event('app-ready')) 