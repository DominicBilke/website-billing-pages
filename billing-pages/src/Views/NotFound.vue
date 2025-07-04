<template>
  <div class="not-found-page">
    <div class="not-found-content">
      <div class="error-code">404</div>
      <h1>Page Not Found</h1>
      <p>The page you're looking for doesn't exist or has been moved.</p>
      
      <div class="not-found-actions">
        <router-link to="/" class="btn btn-primary">
          <i class="fas fa-home"></i>
          Go to Dashboard
        </router-link>
        <button @click="goBack" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i>
          Go Back
        </button>
      </div>
      
      <div class="helpful-links">
        <h3>Helpful Links</h3>
        <div class="links-grid">
          <router-link to="/companies" class="help-link">
            <i class="fas fa-building"></i>
            <span>Companies</span>
          </router-link>
          <router-link to="/tours" class="help-link">
            <i class="fas fa-route"></i>
            <span>Tours</span>
          </router-link>
          <router-link to="/work" class="help-link">
            <i class="fas fa-clock"></i>
            <span>Work Hours</span>
          </router-link>
          <router-link to="/tasks" class="help-link">
            <i class="fas fa-tasks"></i>
            <span>Tasks</span>
          </router-link>
          <router-link to="/invoices" class="help-link">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Invoices</span>
          </router-link>
          <router-link to="/reports" class="help-link">
            <i class="fas fa-chart-line"></i>
            <span>Reports</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const goBack = () => {
  router.go(-1)
}
</script>

<style scoped lang="scss">
.not-found-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-background);
  padding: 2rem;
}

.not-found-content {
  text-align: center;
  max-width: 600px;
}

.error-code {
  font-size: 8rem;
  font-weight: 900;
  color: var(--color-primary);
  line-height: 1;
  margin-bottom: 1rem;
  
  @media (max-width: 768px) {
    font-size: 6rem;
  }
}

h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: 1rem;
  
  @media (max-width: 768px) {
    font-size: 2rem;
  }
}

p {
  font-size: 1.125rem;
  color: var(--color-text-secondary);
  margin-bottom: 2rem;
  line-height: 1.6;
}

.not-found-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 3rem;
  flex-wrap: wrap;
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
  text-decoration: none;
  
  &.btn-primary {
    background: var(--color-primary);
    color: white;
    
    &:hover {
      background: var(--color-primary-dark);
      transform: translateY(-2px);
    }
  }
  
  &.btn-secondary {
    background: var(--color-background-tertiary);
    color: var(--color-text-primary);
    border: 1px solid var(--color-border);
    
    &:hover {
      background: var(--color-border);
      transform: translateY(-2px);
    }
  }
}

.helpful-links {
  h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 1.5rem;
  }
}

.links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  max-width: 500px;
  margin: 0 auto;
}

.help-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1.5rem;
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  text-decoration: none;
  color: var(--color-text-secondary);
  transition: all 0.3s ease;
  
  &:hover {
    background: var(--color-primary);
    color: white;
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }
  
  i {
    font-size: 1.5rem;
  }
  
  span {
    font-weight: 500;
    font-size: 0.875rem;
  }
}

// Dark theme adjustments
[data-theme="dark"] {
  .help-link {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .btn-secondary {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
}
</style> 