<template>
  <div class="companies-page">
    <div class="page-header">
      <div class="page-header-content">
        <h1>Companies</h1>
        <p class="text-muted">Manage your company profiles and employees</p>
      </div>
      <div class="page-header-actions">
        <BaseButton @click="showCreateModal = true" icon="fas fa-plus">
          Add Company
        </BaseButton>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--primary">
          <i class="fas fa-building"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.total }}</h3>
          <p class="stat-card-label">Total Companies</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--success">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.employees }}</h3>
          <p class="stat-card-label">Total Employees</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--info">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.active }}</h3>
          <p class="stat-card-label">Active Companies</p>
        </div>
      </div>
    </div>

    <!-- Companies Table -->
    <div class="card">
      <div class="card-header">
        <h3>Company List</h3>
        <div class="card-actions">
          <input
            v-model="searchQuery"
            type="text"
            class="form-input form-input--sm"
            placeholder="Search companies..."
          />
        </div>
      </div>
      <div class="card-body">
        <div v-if="loading" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Loading companies...</span>
        </div>
        
        <div v-else-if="filteredCompanies.length === 0" class="empty-state">
          <i class="fas fa-building"></i>
          <p>No companies found</p>
          <BaseButton @click="showCreateModal = true" variant="primary">
            Add Your First Company
          </BaseButton>
        </div>
        
        <div v-else class="companies-table">
          <div
            v-for="company in filteredCompanies"
            :key="company.id"
            class="company-item"
          >
            <div class="company-info">
              <img :src="company.logo || '/assets/images/default-company.png'" :alt="company.name" class="company-logo" />
              <div class="company-details">
                <h4>{{ company.name }}</h4>
                <p class="company-industry">{{ company.industry }}</p>
                <p class="company-location">{{ company.city }}, {{ company.country }}</p>
              </div>
            </div>
            
            <div class="company-stats">
              <div class="stat">
                <span class="stat-label">Employees</span>
                <span class="stat-value">{{ company.employee_count }}</span>
              </div>
              <div class="stat">
                <span class="stat-label">Status</span>
                <span :class="['badge', `badge--${company.status === 'active' ? 'success' : 'warning'}`]">
                  {{ company.status }}
                </span>
              </div>
            </div>
            
            <div class="company-actions">
              <BaseButton @click="viewCompany(company)" variant="outline" size="sm">
                View
              </BaseButton>
              <BaseButton @click="editCompany(company)" variant="outline" size="sm">
                Edit
              </BaseButton>
              <BaseButton @click="deleteCompany(company)" variant="danger" size="sm">
                Delete
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Company' : 'Add Company' }}</h3>
          <button class="modal-close" @click="closeModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveCompany" class="form">
            <div class="form-row">
              <div class="form-group">
                <label for="company-name" class="form-label">Company Name</label>
                <input
                  id="company-name"
                  v-model="companyForm.name"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': errors.name }"
                  placeholder="Enter company name"
                  required
                />
                <div v-if="errors.name" class="form-error">{{ errors.name }}</div>
              </div>
              
              <div class="form-group">
                <label for="company-industry" class="form-label">Industry</label>
                <select
                  id="company-industry"
                  v-model="companyForm.industry"
                  class="form-select"
                  :class="{ 'form-input--error': errors.industry }"
                  required
                >
                  <option value="">Select industry</option>
                  <option value="Technology">Technology</option>
                  <option value="Healthcare">Healthcare</option>
                  <option value="Finance">Finance</option>
                  <option value="Education">Education</option>
                  <option value="Manufacturing">Manufacturing</option>
                  <option value="Retail">Retail</option>
                  <option value="Other">Other</option>
                </select>
                <div v-if="errors.industry" class="form-error">{{ errors.industry }}</div>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="company-email" class="form-label">Email</label>
                <input
                  id="company-email"
                  v-model="companyForm.email"
                  type="email"
                  class="form-input"
                  :class="{ 'form-input--error': errors.email }"
                  placeholder="company@example.com"
                  required
                />
                <div v-if="errors.email" class="form-error">{{ errors.email }}</div>
              </div>
              
              <div class="form-group">
                <label for="company-phone" class="form-label">Phone</label>
                <input
                  id="company-phone"
                  v-model="companyForm.phone"
                  type="tel"
                  class="form-input"
                  :class="{ 'form-input--error': errors.phone }"
                  placeholder="+1234567890"
                />
                <div v-if="errors.phone" class="form-error">{{ errors.phone }}</div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="company-address" class="form-label">Address</label>
              <textarea
                id="company-address"
                v-model="companyForm.address"
                class="form-textarea"
                :class="{ 'form-input--error': errors.address }"
                placeholder="Enter company address"
                rows="3"
              ></textarea>
              <div v-if="errors.address" class="form-error">{{ errors.address }}</div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="company-city" class="form-label">City</label>
                <input
                  id="company-city"
                  v-model="companyForm.city"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': errors.city }"
                  placeholder="Enter city"
                  required
                />
                <div v-if="errors.city" class="form-error">{{ errors.city }}</div>
              </div>
              
              <div class="form-group">
                <label for="company-country" class="form-label">Country</label>
                <input
                  id="company-country"
                  v-model="companyForm.country"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': errors.country }"
                  placeholder="Enter country"
                  required
                />
                <div v-if="errors.country" class="form-error">{{ errors.country }}</div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="company-description" class="form-label">Description</label>
              <textarea
                id="company-description"
                v-model="companyForm.description"
                class="form-textarea"
                :class="{ 'form-input--error': errors.description }"
                placeholder="Enter company description"
                rows="4"
              ></textarea>
              <div v-if="errors.description" class="form-error">{{ errors.description }}</div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <BaseButton @click="closeModal" variant="secondary">
            Cancel
          </BaseButton>
          <BaseButton @click="saveCompany" variant="primary" :loading="saving">
            {{ showEditModal ? 'Update Company' : 'Create Company' }}
          </BaseButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notifications'
import BaseButton from '@/components/ui/BaseButton.vue'

export default {
  name: 'Companies',
  components: {
    BaseButton
  },
  setup() {
    const notificationStore = useNotificationStore()
    
    // State
    const companies = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const searchQuery = ref('')
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const editingCompany = ref(null)
    
    const companyForm = reactive({
      name: '',
      industry: '',
      email: '',
      phone: '',
      address: '',
      city: '',
      country: '',
      description: ''
    })
    
    const errors = reactive({})
    const stats = ref({
      total: 0,
      employees: 0,
      active: 0
    })
    
    // Computed
    const filteredCompanies = computed(() => {
      if (!searchQuery.value) return companies.value
      
      return companies.value.filter(company =>
        company.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        company.industry.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        company.city.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })
    
    // Methods
    const fetchCompanies = async () => {
      try {
        loading.value = true
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        companies.value = [
          {
            id: 1,
            name: 'TechCorp Solutions',
            industry: 'Technology',
            email: 'contact@techcorp.com',
            phone: '+1234567890',
            address: '123 Tech Street',
            city: 'San Francisco',
            country: 'USA',
            description: 'Leading technology solutions provider',
            employee_count: 150,
            status: 'active',
            logo: null
          },
          {
            id: 2,
            name: 'HealthCare Plus',
            industry: 'Healthcare',
            email: 'info@healthcareplus.com',
            phone: '+1987654321',
            address: '456 Medical Center',
            city: 'New York',
            country: 'USA',
            description: 'Comprehensive healthcare services',
            employee_count: 89,
            status: 'active',
            logo: null
          }
        ]
        
        updateStats()
      } catch (error) {
        notificationStore.error('Failed to load companies')
      } finally {
        loading.value = false
      }
    }
    
    const updateStats = () => {
      stats.value = {
        total: companies.value.length,
        employees: companies.value.reduce((sum, company) => sum + company.employee_count, 0),
        active: companies.value.filter(company => company.status === 'active').length
      }
    }
    
    const resetForm = () => {
      Object.keys(companyForm).forEach(key => {
        companyForm[key] = ''
      })
      Object.keys(errors).forEach(key => {
        delete errors[key]
      })
    }
    
    const closeModal = () => {
      showCreateModal.value = false
      showEditModal.value = false
      editingCompany.value = null
      resetForm()
    }
    
    const editCompany = (company) => {
      editingCompany.value = company
      Object.keys(companyForm).forEach(key => {
        companyForm[key] = company[key] || ''
      })
      showEditModal.value = true
    }
    
    const saveCompany = async () => {
      try {
        saving.value = true
        
        // Validate form
        if (!companyForm.name) errors.name = 'Company name is required'
        if (!companyForm.industry) errors.industry = 'Industry is required'
        if (!companyForm.email) errors.email = 'Email is required'
        if (!companyForm.city) errors.city = 'City is required'
        if (!companyForm.country) errors.country = 'Country is required'
        
        if (Object.keys(errors).length > 0) return
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        if (showEditModal.value) {
          // Update existing company
          const index = companies.value.findIndex(c => c.id === editingCompany.value.id)
          companies.value[index] = { ...editingCompany.value, ...companyForm }
          notificationStore.success('Company updated successfully')
        } else {
          // Create new company
          const newCompany = {
            id: Date.now(),
            ...companyForm,
            employee_count: 0,
            status: 'active',
            logo: null
          }
          companies.value.push(newCompany)
          notificationStore.success('Company created successfully')
        }
        
        updateStats()
        closeModal()
      } catch (error) {
        notificationStore.error('Failed to save company')
      } finally {
        saving.value = false
      }
    }
    
    const deleteCompany = async (company) => {
      if (!confirm(`Are you sure you want to delete ${company.name}?`)) return
      
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 500))
        
        companies.value = companies.value.filter(c => c.id !== company.id)
        updateStats()
        notificationStore.success('Company deleted successfully')
      } catch (error) {
        notificationStore.error('Failed to delete company')
      }
    }
    
    const viewCompany = (company) => {
      // Navigate to company detail page
      console.log('View company:', company)
    }
    
    // Lifecycle
    onMounted(() => {
      fetchCompanies()
    })
    
    return {
      // State
      companies,
      loading,
      saving,
      searchQuery,
      showCreateModal,
      showEditModal,
      companyForm,
      errors,
      stats,
      
      // Computed
      filteredCompanies,
      
      // Methods
      fetchCompanies,
      closeModal,
      editCompany,
      saveCompany,
      deleteCompany,
      viewCompany
    }
  }
}
</script>

<style lang="scss" scoped>
.companies-page {
  max-width: 1400px;
  margin: 0 auto;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.companies-table {
  @include flex-column;
  gap: 1rem;
}

.company-item {
  @include flex-between;
  padding: 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  background: var(--background-light);
  transition: all var(--transition);
  
  &:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
  }
  
  @include responsive(sm) {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}

.company-info {
  @include flex-center;
  gap: 1rem;
  flex: 1;
}

.company-logo {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-lg);
  object-fit: cover;
  background: var(--background-color);
}

.company-details h4 {
  margin-bottom: 0.25rem;
  color: var(--text-color);
}

.company-industry {
  color: var(--primary-color);
  font-weight: var(--font-weight-medium);
  margin-bottom: 0.25rem;
}

.company-location {
  color: var(--text-muted);
  font-size: var(--font-size-sm);
}

.company-stats {
  @include flex-column;
  gap: 0.5rem;
  align-items: center;
  
  @include responsive(sm) {
    flex-direction: row;
    gap: 1rem;
  }
}

.stat {
  @include flex-column;
  align-items: center;
  gap: 0.25rem;
}

.stat-label {
  font-size: var(--font-size-xs);
  color: var(--text-muted);
}

.stat-value {
  font-weight: var(--font-weight-semibold);
  color: var(--text-color);
}

.company-actions {
  @include flex-center;
  gap: 0.5rem;
  
  @include responsive(sm) {
    width: 100%;
    justify-content: flex-end;
  }
}

.loading-state,
.empty-state {
  @include flex-column;
  align-items: center;
  padding: 3rem 1rem;
  color: var(--text-muted);
  
  i {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  
  @include responsive(sm) {
    grid-template-columns: 1fr;
  }
}

@include responsive(sm) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style> 