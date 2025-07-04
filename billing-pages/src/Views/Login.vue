<template>
  <div class="login-page">
    <div class="login-container">
      <!-- Logo Section -->
      <div class="login-header">
        <img src="/assets/images/logo.png" alt="Billing Pages" class="login-logo" />
        <h1>Welcome Back</h1>
        <p class="text-muted">Sign in to your account to continue</p>
      </div>

      <!-- Login Form -->
      <div class="login-form">
        <form @submit.prevent="handleLogin" class="form">
          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="form-input"
              :class="{ 'form-input--error': errors.email }"
              placeholder="Enter your email"
              required
              autocomplete="email"
            />
            <div v-if="errors.email" class="form-error">{{ errors.email }}</div>
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="password-input-wrapper">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-input"
                :class="{ 'form-input--error': errors.password }"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
              />
              <button
                type="button"
                class="password-toggle"
                @click="showPassword = !showPassword"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <div v-if="errors.password" class="form-error">{{ errors.password }}</div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="form-options">
            <label class="form-checkbox">
              <input
                v-model="form.remember"
                type="checkbox"
              />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-password" @click.prevent="showForgotPassword = true">
              Forgot password?
            </a>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn btn--primary btn--full"
            :disabled="isLoading"
          >
            <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
            <span v-else>Sign In</span>
          </button>
        </form>

        <!-- Divider -->
        <div class="divider">
          <span>or</span>
        </div>

        <!-- Social Login -->
        <div class="social-login">
          <button class="btn btn--outline btn--full social-btn" @click="socialLogin('google')">
            <i class="fab fa-google"></i>
            <span>Continue with Google</span>
          </button>
          <button class="btn btn--outline btn--full social-btn" @click="socialLogin('microsoft')">
            <i class="fab fa-microsoft"></i>
            <span>Continue with Microsoft</span>
          </button>
        </div>

        <!-- Register Link -->
        <div class="register-link">
          <p>
            Don't have an account?
            <a href="#" @click.prevent="showRegister = true">Sign up</a>
          </p>
        </div>
      </div>
    </div>

    <!-- Forgot Password Modal -->
    <div v-if="showForgotPassword" class="modal-overlay" @click="showForgotPassword = false">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>Reset Password</h3>
          <button class="modal-close" @click="showForgotPassword = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Enter your email address and we'll send you a link to reset your password.</p>
          <div class="form-group">
            <label for="reset-email" class="form-label">Email Address</label>
            <input
              id="reset-email"
              v-model="resetEmail"
              type="email"
              class="form-input"
              placeholder="Enter your email"
              required
            />
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn--secondary" @click="showForgotPassword = false">
            Cancel
          </button>
          <button class="btn btn--primary" @click="handleForgotPassword">
            Send Reset Link
          </button>
        </div>
      </div>
    </div>

    <!-- Register Modal -->
    <div v-if="showRegister" class="modal-overlay" @click="showRegister = false">
      <div class="modal modal--large" @click.stop>
        <div class="modal-header">
          <h3>Create Account</h3>
          <button class="modal-close" @click="showRegister = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleRegister" class="form">
            <div class="form-row">
              <div class="form-group">
                <label for="register-first-name" class="form-label">First Name</label>
                <input
                  id="register-first-name"
                  v-model="registerForm.firstName"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': registerErrors.firstName }"
                  placeholder="Enter your first name"
                  required
                />
                <div v-if="registerErrors.firstName" class="form-error">
                  {{ registerErrors.firstName }}
                </div>
              </div>
              <div class="form-group">
                <label for="register-last-name" class="form-label">Last Name</label>
                <input
                  id="register-last-name"
                  v-model="registerForm.lastName"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': registerErrors.lastName }"
                  placeholder="Enter your last name"
                  required
                />
                <div v-if="registerErrors.lastName" class="form-error">
                  {{ registerErrors.lastName }}
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="register-email" class="form-label">Email Address</label>
              <input
                id="register-email"
                v-model="registerForm.email"
                type="email"
                class="form-input"
                :class="{ 'form-input--error': registerErrors.email }"
                placeholder="Enter your email"
                required
              />
              <div v-if="registerErrors.email" class="form-error">
                {{ registerErrors.email }}
              </div>
            </div>
            <div class="form-group">
              <label for="register-password" class="form-label">Password</label>
              <div class="password-input-wrapper">
                <input
                  id="register-password"
                  v-model="registerForm.password"
                  :type="showRegisterPassword ? 'text' : 'password'"
                  class="form-input"
                  :class="{ 'form-input--error': registerErrors.password }"
                  placeholder="Create a password"
                  required
                />
                <button
                  type="button"
                  class="password-toggle"
                  @click="showRegisterPassword = !showRegisterPassword"
                >
                  <i :class="showRegisterPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <div v-if="registerErrors.password" class="form-error">
                {{ registerErrors.password }}
              </div>
            </div>
            <div class="form-group">
              <label for="register-confirm-password" class="form-label">Confirm Password</label>
              <div class="password-input-wrapper">
                <input
                  id="register-confirm-password"
                  v-model="registerForm.confirmPassword"
                  :type="showRegisterPassword ? 'text' : 'password'"
                  class="form-input"
                  :class="{ 'form-input--error': registerErrors.confirmPassword }"
                  placeholder="Confirm your password"
                  required
                />
                <button
                  type="button"
                  class="password-toggle"
                  @click="showRegisterPassword = !showRegisterPassword"
                >
                  <i :class="showRegisterPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <div v-if="registerErrors.confirmPassword" class="form-error">
                {{ registerErrors.confirmPassword }}
              </div>
            </div>
            <div class="form-group">
              <label class="form-checkbox">
                <input
                  v-model="registerForm.agreeTerms"
                  type="checkbox"
                  required
                />
                <span>
                  I agree to the
                  <a href="#" target="_blank">Terms of Service</a>
                  and
                  <a href="#" target="_blank">Privacy Policy</a>
                </span>
              </label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn--secondary" @click="showRegister = false">
            Cancel
          </button>
          <button
            class="btn btn--primary"
            @click="handleRegister"
            :disabled="registerLoading"
          >
            <i v-if="registerLoading" class="fas fa-spinner fa-spin"></i>
            <span v-else>Create Account</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const notificationStore = useNotificationStore()
    
    // Form data
    const form = reactive({
      email: '',
      password: '',
      remember: false
    })
    
    const registerForm = reactive({
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      confirmPassword: '',
      agreeTerms: false
    })
    
    // UI state
    const showPassword = ref(false)
    const showRegisterPassword = ref(false)
    const showForgotPassword = ref(false)
    const showRegister = ref(false)
    const resetEmail = ref('')
    
    // Loading states
    const isLoading = ref(false)
    const registerLoading = ref(false)
    
    // Validation errors
    const errors = reactive({})
    const registerErrors = reactive({})
    
    // Methods
    const validateForm = () => {
      errors.value = {}
      
      if (!form.email) {
        errors.email = 'Email is required'
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address'
      }
      
      if (!form.password) {
        errors.password = 'Password is required'
      } else if (form.password.length < 6) {
        errors.password = 'Password must be at least 6 characters'
      }
      
      return Object.keys(errors).length === 0
    }
    
    const validateRegisterForm = () => {
      registerErrors.value = {}
      
      if (!registerForm.firstName) {
        registerErrors.firstName = 'First name is required'
      }
      
      if (!registerForm.lastName) {
        registerErrors.lastName = 'Last name is required'
      }
      
      if (!registerForm.email) {
        registerErrors.email = 'Email is required'
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(registerForm.email)) {
        registerErrors.email = 'Please enter a valid email address'
      }
      
      if (!registerForm.password) {
        registerErrors.password = 'Password is required'
      } else if (registerForm.password.length < 8) {
        registerErrors.password = 'Password must be at least 8 characters'
      }
      
      if (!registerForm.confirmPassword) {
        registerErrors.confirmPassword = 'Please confirm your password'
      } else if (registerForm.password !== registerForm.confirmPassword) {
        registerErrors.confirmPassword = 'Passwords do not match'
      }
      
      if (!registerForm.agreeTerms) {
        registerErrors.agreeTerms = 'You must agree to the terms'
      }
      
      return Object.keys(registerErrors).length === 0
    }
    
    const handleLogin = async () => {
      if (!validateForm()) return
      
      try {
        isLoading.value = true
        await authStore.login(form)
        notificationStore.success('Welcome back!')
        router.push('/dashboard')
      } catch (error) {
        notificationStore.error(error.message || 'Login failed')
      } finally {
        isLoading.value = false
      }
    }
    
    const handleRegister = async () => {
      if (!validateRegisterForm()) return
      
      try {
        registerLoading.value = true
        await authStore.register({
          name: `${registerForm.firstName} ${registerForm.lastName}`,
          email: registerForm.email,
          password: registerForm.password
        })
        notificationStore.success('Account created successfully!')
        showRegister.value = false
        router.push('/dashboard')
      } catch (error) {
        notificationStore.error(error.message || 'Registration failed')
      } finally {
        registerLoading.value = false
      }
    }
    
    const handleForgotPassword = async () => {
      if (!resetEmail.value) {
        notificationStore.error('Please enter your email address')
        return
      }
      
      try {
        // Call forgot password API
        await fetch('/api/auth/forgot-password', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ email: resetEmail.value })
        })
        
        notificationStore.success('Password reset link sent to your email')
        showForgotPassword.value = false
        resetEmail.value = ''
      } catch (error) {
        notificationStore.error('Failed to send reset link')
      }
    }
    
    const socialLogin = (provider) => {
      // Implement social login
      notificationStore.info(`${provider} login coming soon`)
    }
    
    return {
      // Form data
      form,
      registerForm,
      
      // UI state
      showPassword,
      showRegisterPassword,
      showForgotPassword,
      showRegister,
      resetEmail,
      
      // Loading states
      isLoading,
      registerLoading,
      
      // Validation errors
      errors,
      registerErrors,
      
      // Methods
      handleLogin,
      handleRegister,
      handleForgotPassword,
      socialLogin
    }
  }
}
</script>

<style lang="scss" scoped>
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  @include flex-center;
  padding: 2rem 1rem;
}

.login-container {
  background: white;
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-2xl);
  width: 100%;
  max-width: 450px;
  overflow: hidden;
}

.login-header {
  text-align: center;
  padding: 2rem 2rem 1rem;
  background: var(--background-color);
}

.login-logo {
  height: 60px;
  width: auto;
  margin-bottom: 1rem;
}

.login-header h1 {
  margin-bottom: 0.5rem;
  color: var(--text-color);
}

.login-form {
  padding: 2rem;
}

.form {
  @include flex-column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  
  @include responsive(sm) {
    grid-template-columns: 1fr;
  }
}

.password-input-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  padding: 0.25rem;
  
  &:hover {
    color: var(--text-color);
  }
}

.form-options {
  @include flex-between;
  align-items: center;
  
  @include responsive(sm) {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}

.forgot-password {
  color: var(--primary-color);
  font-size: var(--font-size-sm);
  
  &:hover {
    text-decoration: underline;
  }
}

.divider {
  position: relative;
  text-align: center;
  margin: 2rem 0;
  
  &::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--border-color);
  }
  
  span {
    background: white;
    padding: 0 1rem;
    color: var(--text-muted);
    font-size: var(--font-size-sm);
  }
}

.social-login {
  @include flex-column;
  gap: 1rem;
}

.social-btn {
  @include flex-center;
  gap: 0.75rem;
  
  i {
    font-size: 1.25rem;
  }
}

.register-link {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
  
  a {
    color: var(--primary-color);
    font-weight: var(--font-weight-medium);
    
    &:hover {
      text-decoration: underline;
    }
  }
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  @include flex-center;
  z-index: var(--z-modal-backdrop);
  padding: 1rem;
}

.modal {
  background: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  
  &--large {
    max-width: 600px;
  }
}

.modal-header {
  @include flex-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: var(--text-muted);
  cursor: pointer;
  padding: 0.25rem;
  border-radius: var(--radius);
  
  &:hover {
    background: var(--background-color);
    color: var(--text-color);
  }
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  @include flex-between;
  padding: 1.5rem;
  border-top: 1px solid var(--border-color);
  gap: 1rem;
  
  @include responsive(sm) {
    flex-direction: column-reverse;
  }
}

@include responsive(sm) {
  .login-container {
    margin: 0;
    border-radius: 0;
    min-height: 100vh;
  }
  
  .login-page {
    padding: 0;
  }
}
</style> 