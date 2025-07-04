<template>
  <div class="settings-page">
    <div class="page-header">
      <h1>Settings</h1>
      <p>Configure your application preferences and account settings</p>
    </div>

    <div class="settings-content">
      <div class="settings-nav">
        <button 
          v-for="section in sections" 
          :key="section.id"
          @click="activeSection = section.id"
          class="nav-button"
          :class="{ active: activeSection === section.id }"
        >
          <i :class="section.icon"></i>
          <span>{{ section.name }}</span>
        </button>
      </div>

      <div class="settings-panel">
        <!-- Profile Settings -->
        <div v-if="activeSection === 'profile'" class="settings-section">
          <h2>Profile Settings</h2>
          <form class="settings-form">
            <div class="form-group">
              <label>Profile Picture</label>
              <div class="avatar-upload">
                <div class="avatar-preview">
                  <i class="fas fa-user"></i>
                </div>
                <button type="button" class="btn btn-secondary">
                  <i class="fas fa-camera"></i>
                  Upload Photo
                </button>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" v-model="profile.firstName" class="form-input">
              </div>
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" v-model="profile.lastName" class="form-input">
              </div>
            </div>
            
            <div class="form-group">
              <label>Email</label>
              <input type="email" v-model="profile.email" class="form-input">
            </div>
            
            <div class="form-group">
              <label>Phone</label>
              <input type="tel" v-model="profile.phone" class="form-input">
            </div>
            
            <div class="form-group">
              <label>Company</label>
              <input type="text" v-model="profile.company" class="form-input">
            </div>
            
            <div class="form-group">
              <label>Position</label>
              <input type="text" v-model="profile.position" class="form-input">
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i>
              Save Changes
            </button>
          </form>
        </div>

        <!-- Security Settings -->
        <div v-if="activeSection === 'security'" class="settings-section">
          <h2>Security Settings</h2>
          <form class="settings-form">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" v-model="security.currentPassword" class="form-input">
            </div>
            
            <div class="form-group">
              <label>New Password</label>
              <input type="password" v-model="security.newPassword" class="form-input">
            </div>
            
            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" v-model="security.confirmPassword" class="form-input">
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="security.twoFactor">
                <span class="checkmark"></span>
                Enable Two-Factor Authentication
              </label>
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="security.emailNotifications">
                <span class="checkmark"></span>
                Email notifications for security events
              </label>
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-shield-alt"></i>
              Update Security Settings
            </button>
          </form>
        </div>

        <!-- Preferences -->
        <div v-if="activeSection === 'preferences'" class="settings-section">
          <h2>Preferences</h2>
          <form class="settings-form">
            <div class="form-group">
              <label>Language</label>
              <select v-model="preferences.language" class="form-select">
                <option value="de">Deutsch</option>
                <option value="en">English</option>
                <option value="fr">Français</option>
                <option value="es">Español</option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Timezone</label>
              <select v-model="preferences.timezone" class="form-select">
                <option value="Europe/Berlin">Europe/Berlin</option>
                <option value="Europe/London">Europe/London</option>
                <option value="America/New_York">America/New_York</option>
                <option value="Asia/Tokyo">Asia/Tokyo</option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Currency</label>
              <select v-model="preferences.currency" class="form-select">
                <option value="EUR">Euro (€)</option>
                <option value="USD">US Dollar ($)</option>
                <option value="GBP">British Pound (£)</option>
                <option value="JPY">Japanese Yen (¥)</option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Date Format</label>
              <select v-model="preferences.dateFormat" class="form-select">
                <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                <option value="YYYY-MM-DD">YYYY-MM-DD</option>
              </select>
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="preferences.darkMode">
                <span class="checkmark"></span>
                Dark Mode
              </label>
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="preferences.emailNotifications">
                <span class="checkmark"></span>
                Email Notifications
              </label>
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="preferences.pushNotifications">
                <span class="checkmark"></span>
                Push Notifications
              </label>
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i>
              Save Preferences
            </button>
          </form>
        </div>

        <!-- Notifications -->
        <div v-if="activeSection === 'notifications'" class="settings-section">
          <h2>Notification Settings</h2>
          <form class="settings-form">
            <div class="notification-group">
              <h3>Email Notifications</h3>
              
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="notifications.email.invoices">
                  <span class="checkmark"></span>
                  Invoice reminders
                </label>
              </div>
              
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="notifications.email.payments">
                  <span class="checkmark"></span>
                  Payment confirmations
                </label>
              </div>
              
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="notifications.email.reports">
                  <span class="checkmark"></span>
                  Weekly reports
                </label>
              </div>
            </div>
            
            <div class="notification-group">
              <h3>System Notifications</h3>
              
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="notifications.system.updates">
                  <span class="checkmark"></span>
                  System updates
                </label>
              </div>
              
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="notifications.system.maintenance">
                  <span class="checkmark"></span>
                  Maintenance alerts
                </label>
              </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-bell"></i>
              Save Notification Settings
            </button>
          </form>
        </div>

        <!-- API Settings -->
        <div v-if="activeSection === 'api'" class="settings-section">
          <h2>API Settings</h2>
          <div class="api-info">
            <div class="api-key-section">
              <h3>API Key</h3>
              <div class="api-key-display">
                <input type="text" :value="apiKey" readonly class="form-input">
                <button type="button" class="btn btn-secondary">
                  <i class="fas fa-copy"></i>
                  Copy
                </button>
                <button type="button" class="btn btn-danger">
                  <i class="fas fa-refresh"></i>
                  Regenerate
                </button>
              </div>
            </div>
            
            <div class="api-docs">
              <h3>API Documentation</h3>
              <p>Access our comprehensive API documentation to integrate with your systems.</p>
              <a href="#" class="btn btn-secondary">
                <i class="fas fa-book"></i>
                View Documentation
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

const activeSection = ref('profile')

const sections = [
  { id: 'profile', name: 'Profile', icon: 'fas fa-user' },
  { id: 'security', name: 'Security', icon: 'fas fa-shield-alt' },
  { id: 'preferences', name: 'Preferences', icon: 'fas fa-cog' },
  { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell' },
  { id: 'api', name: 'API', icon: 'fas fa-code' }
]

const profile = reactive({
  firstName: 'John',
  lastName: 'Doe',
  email: 'john.doe@example.com',
  phone: '+49 123 456 789',
  company: 'Tech Solutions GmbH',
  position: 'Project Manager'
})

const security = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  twoFactor: false,
  emailNotifications: true
})

const preferences = reactive({
  language: 'de',
  timezone: 'Europe/Berlin',
  currency: 'EUR',
  dateFormat: 'DD/MM/YYYY',
  darkMode: false,
  emailNotifications: true,
  pushNotifications: false
})

const notifications = reactive({
  email: {
    invoices: true,
    payments: true,
    reports: false
  },
  system: {
    updates: true,
    maintenance: true
  }
})

const apiKey = 'sk_test_1234567890abcdefghijklmnopqrstuvwxyz'
</script>

<style scoped lang="scss">
.settings-page {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
  
  h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.5rem;
  }
  
  p {
    color: var(--color-text-secondary);
    font-size: 1.1rem;
  }
}

.settings-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}

.settings-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  
  @media (max-width: 768px) {
    flex-direction: row;
    overflow-x: auto;
    padding-bottom: 1rem;
  }
}

.nav-button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-background);
  color: var(--color-text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
  
  &:hover {
    background: var(--color-background-tertiary);
    color: var(--color-text-primary);
  }
  
  &.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
  }
  
  i {
    width: 16px;
    text-align: center;
  }
}

.settings-panel {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 2rem;
}

.settings-section h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: 2rem;
}

.settings-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: var(--color-text-primary);
}

.form-input,
.form-select {
  padding: 0.75rem;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-background);
  color: var(--color-text-primary);
  
  &:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px var(--color-primary-light);
  }
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  
  input[type="checkbox"] {
    display: none;
  }
  
  .checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid var(--color-border);
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
  }
  
  input[type="checkbox"]:checked + .checkmark {
    background: var(--color-primary);
    border-color: var(--color-primary);
    
    &::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 0.75rem;
      font-weight: bold;
    }
  }
}

.avatar-upload {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.avatar-preview {
  width: 64px;
  height: 64px;
  background: var(--color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  
  &.btn-primary {
    background: var(--color-primary);
    color: white;
    
    &:hover {
      background: var(--color-primary-dark);
    }
  }
  
  &.btn-secondary {
    background: var(--color-background-tertiary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border);
    
    &:hover {
      background: var(--color-border);
    }
  }
  
  &.btn-danger {
    background: var(--color-danger);
    color: white;
    
    &:hover {
      background: var(--color-danger-dark);
    }
  }
}

.notification-group {
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  
  h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 1rem;
  }
}

.api-info {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.api-key-section h3,
.api-docs h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: 1rem;
}

.api-key-display {
  display: flex;
  gap: 1rem;
  align-items: center;
  
  .form-input {
    flex: 1;
  }
}

.api-docs p {
  color: var(--color-text-secondary);
  margin-bottom: 1rem;
}

// Dark theme adjustments
[data-theme="dark"] {
  .settings-panel {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .nav-button {
    background: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
  
  .form-input,
  .form-select {
    background: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
  
  .btn-secondary {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
  
  .notification-group {
    border-color: var(--color-border-dark);
  }
}
</style> 