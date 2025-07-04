<template>
  <div class="tours-page">
    <div class="page-header">
      <div class="page-header-content">
        <h1>Tours</h1>
        <p class="text-muted">Manage your tours and travel itineraries</p>
      </div>
      <div class="page-header-actions">
        <BaseButton @click="showCreateModal = true" icon="fas fa-plus">
          Add Tour
        </BaseButton>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--primary">
          <i class="fas fa-route"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.total }}</h3>
          <p class="stat-card-label">Total Tours</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--success">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.active }}</h3>
          <p class="stat-card-label">Active Tours</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--info">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.participants }}</h3>
          <p class="stat-card-label">Total Participants</p>
        </div>
      </div>
    </div>

    <!-- Tours Grid -->
    <div class="tours-grid">
      <div v-if="loading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Loading tours...</span>
      </div>
      
      <div v-else-if="filteredTours.length === 0" class="empty-state">
        <i class="fas fa-route"></i>
        <p>No tours found</p>
        <BaseButton @click="showCreateModal = true" variant="primary">
          Add Your First Tour
        </BaseButton>
      </div>
      
      <div v-else class="tours-container">
        <div
          v-for="tour in filteredTours"
          :key="tour.id"
          class="tour-card"
        >
          <div class="tour-image">
            <img :src="tour.image || '/assets/images/default-tour.jpg'" :alt="tour.name" />
            <div class="tour-status" :class="`tour-status--${tour.status}`">
              {{ tour.status }}
            </div>
          </div>
          
          <div class="tour-content">
            <h3 class="tour-title">{{ tour.name }}</h3>
            <p class="tour-destination">
              <i class="fas fa-map-marker-alt"></i>
              {{ tour.destination }}
            </p>
            
            <div class="tour-dates">
              <div class="tour-date">
                <i class="fas fa-calendar"></i>
                <span>{{ formatDate(tour.start_date) }} - {{ formatDate(tour.end_date) }}</span>
              </div>
              <div class="tour-duration">
                <i class="fas fa-clock"></i>
                <span>{{ tour.duration }} days</span>
              </div>
            </div>
            
            <p class="tour-description">{{ tour.description }}</p>
            
            <div class="tour-stats">
              <div class="tour-stat">
                <span class="tour-stat-label">Participants</span>
                <span class="tour-stat-value">{{ tour.participants }}/{{ tour.max_participants }}</span>
              </div>
              <div class="tour-stat">
                <span class="tour-stat-label">Price</span>
                <span class="tour-stat-value">{{ formatCurrency(tour.price) }}</span>
              </div>
            </div>
            
            <div class="tour-actions">
              <BaseButton @click="viewTour(tour)" variant="primary" size="sm">
                View Details
              </BaseButton>
              <BaseButton @click="editTour(tour)" variant="outline" size="sm">
                Edit
              </BaseButton>
              <BaseButton @click="deleteTour(tour)" variant="danger" size="sm">
                Delete
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModal">
      <div class="modal modal--large" @click.stop>
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Tour' : 'Add Tour' }}</h3>
          <button class="modal-close" @click="closeModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveTour" class="form">
            <div class="form-row">
              <div class="form-group">
                <label for="tour-name" class="form-label">Tour Name</label>
                <input
                  id="tour-name"
                  v-model="tourForm.name"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': errors.name }"
                  placeholder="Enter tour name"
                  required
                />
                <div v-if="errors.name" class="form-error">{{ errors.name }}</div>
              </div>
              
              <div class="form-group">
                <label for="tour-destination" class="form-label">Destination</label>
                <input
                  id="tour-destination"
                  v-model="tourForm.destination"
                  type="text"
                  class="form-input"
                  :class="{ 'form-input--error': errors.destination }"
                  placeholder="Enter destination"
                  required
                />
                <div v-if="errors.destination" class="form-error">{{ errors.destination }}</div>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="tour-start-date" class="form-label">Start Date</label>
                <input
                  id="tour-start-date"
                  v-model="tourForm.start_date"
                  type="date"
                  class="form-input"
                  :class="{ 'form-input--error': errors.start_date }"
                  required
                />
                <div v-if="errors.start_date" class="form-error">{{ errors.start_date }}</div>
              </div>
              
              <div class="form-group">
                <label for="tour-end-date" class="form-label">End Date</label>
                <input
                  id="tour-end-date"
                  v-model="tourForm.end_date"
                  type="date"
                  class="form-input"
                  :class="{ 'form-input--error': errors.end_date }"
                  required
                />
                <div v-if="errors.end_date" class="form-error">{{ errors.end_date }}</div>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="tour-price" class="form-label">Price (EUR)</label>
                <input
                  id="tour-price"
                  v-model="tourForm.price"
                  type="number"
                  class="form-input"
                  :class="{ 'form-input--error': errors.price }"
                  placeholder="0.00"
                  min="0"
                  step="0.01"
                  required
                />
                <div v-if="errors.price" class="form-error">{{ errors.price }}</div>
              </div>
              
              <div class="form-group">
                <label for="tour-max-participants" class="form-label">Max Participants</label>
                <input
                  id="tour-max-participants"
                  v-model="tourForm.max_participants"
                  type="number"
                  class="form-input"
                  :class="{ 'form-input--error': errors.max_participants }"
                  placeholder="20"
                  min="1"
                  required
                />
                <div v-if="errors.max_participants" class="form-error">{{ errors.max_participants }}</div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="tour-description" class="form-label">Description</label>
              <textarea
                id="tour-description"
                v-model="tourForm.description"
                class="form-textarea"
                :class="{ 'form-input--error': errors.description }"
                placeholder="Enter tour description"
                rows="4"
                required
              ></textarea>
              <div v-if="errors.description" class="form-error">{{ errors.description }}</div>
            </div>
            
            <div class="form-group">
              <label for="tour-itinerary" class="form-label">Itinerary</label>
              <textarea
                id="tour-itinerary"
                v-model="tourForm.itinerary"
                class="form-textarea"
                :class="{ 'form-input--error': errors.itinerary }"
                placeholder="Enter detailed itinerary"
                rows="6"
              ></textarea>
              <div v-if="errors.itinerary" class="form-error">{{ errors.itinerary }}</div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <BaseButton @click="closeModal" variant="secondary">
            Cancel
          </BaseButton>
          <BaseButton @click="saveTour" variant="primary" :loading="saving">
            {{ showEditModal ? 'Update Tour' : 'Create Tour' }}
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
  name: 'Tours',
  components: {
    BaseButton
  },
  setup() {
    const notificationStore = useNotificationStore()
    
    // State
    const tours = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const editingTour = ref(null)
    
    const tourForm = reactive({
      name: '',
      destination: '',
      start_date: '',
      end_date: '',
      price: '',
      max_participants: '',
      description: '',
      itinerary: ''
    })
    
    const errors = reactive({})
    const stats = ref({
      total: 0,
      active: 0,
      participants: 0
    })
    
    // Computed
    const filteredTours = computed(() => {
      return tours.value
    })
    
    // Methods
    const formatDate = (date) => {
      return new Intl.DateTimeFormat('de-DE').format(new Date(date))
    }
    
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }
    
    const fetchTours = async () => {
      try {
        loading.value = true
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        tours.value = [
          {
            id: 1,
            name: 'Alpine Adventure',
            destination: 'Swiss Alps',
            start_date: '2024-06-15',
            end_date: '2024-06-22',
            duration: 8,
            price: 1299.99,
            max_participants: 15,
            participants: 12,
            description: 'Explore the beautiful Swiss Alps with guided hiking tours and mountain activities.',
            itinerary: 'Day 1: Arrival in Zurich\nDay 2-3: Hiking in Zermatt\nDay 4-5: Mountain biking\nDay 6-7: Relaxation and spa\nDay 8: Departure',
            status: 'active',
            image: null
          },
          {
            id: 2,
            name: 'Mediterranean Cruise',
            destination: 'Greek Islands',
            start_date: '2024-07-10',
            end_date: '2024-07-17',
            duration: 8,
            price: 899.99,
            max_participants: 25,
            participants: 18,
            description: 'Sail through the beautiful Greek islands and experience Mediterranean culture.',
            itinerary: 'Day 1: Athens arrival\nDay 2: Santorini\nDay 3: Mykonos\nDay 4: Rhodes\nDay 5: Crete\nDay 6: Corfu\nDay 7: Return to Athens\nDay 8: Departure',
            status: 'active',
            image: null
          }
        ]
        
        updateStats()
      } catch (error) {
        notificationStore.error('Failed to load tours')
      } finally {
        loading.value = false
      }
    }
    
    const updateStats = () => {
      stats.value = {
        total: tours.value.length,
        active: tours.value.filter(tour => tour.status === 'active').length,
        participants: tours.value.reduce((sum, tour) => sum + tour.participants, 0)
      }
    }
    
    const resetForm = () => {
      Object.keys(tourForm).forEach(key => {
        tourForm[key] = ''
      })
      Object.keys(errors).forEach(key => {
        delete errors[key]
      })
    }
    
    const closeModal = () => {
      showCreateModal.value = false
      showEditModal.value = false
      editingTour.value = null
      resetForm()
    }
    
    const editTour = (tour) => {
      editingTour.value = tour
      Object.keys(tourForm).forEach(key => {
        tourForm[key] = tour[key] || ''
      })
      showEditModal.value = true
    }
    
    const saveTour = async () => {
      try {
        saving.value = true
        
        // Validate form
        if (!tourForm.name) errors.name = 'Tour name is required'
        if (!tourForm.destination) errors.destination = 'Destination is required'
        if (!tourForm.start_date) errors.start_date = 'Start date is required'
        if (!tourForm.end_date) errors.end_date = 'End date is required'
        if (!tourForm.price) errors.price = 'Price is required'
        if (!tourForm.max_participants) errors.max_participants = 'Max participants is required'
        if (!tourForm.description) errors.description = 'Description is required'
        
        if (Object.keys(errors).length > 0) return
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        if (showEditModal.value) {
          // Update existing tour
          const index = tours.value.findIndex(t => t.id === editingTour.value.id)
          tours.value[index] = { ...editingTour.value, ...tourForm }
          notificationStore.success('Tour updated successfully')
        } else {
          // Create new tour
          const newTour = {
            id: Date.now(),
            ...tourForm,
            participants: 0,
            status: 'active',
            image: null,
            duration: Math.ceil((new Date(tourForm.end_date) - new Date(tourForm.start_date)) / (1000 * 60 * 60 * 24))
          }
          tours.value.push(newTour)
          notificationStore.success('Tour created successfully')
        }
        
        updateStats()
        closeModal()
      } catch (error) {
        notificationStore.error('Failed to save tour')
      } finally {
        saving.value = false
      }
    }
    
    const deleteTour = async (tour) => {
      if (!confirm(`Are you sure you want to delete ${tour.name}?`)) return
      
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 500))
        
        tours.value = tours.value.filter(t => t.id !== tour.id)
        updateStats()
        notificationStore.success('Tour deleted successfully')
      } catch (error) {
        notificationStore.error('Failed to delete tour')
      }
    }
    
    const viewTour = (tour) => {
      // Navigate to tour detail page
      console.log('View tour:', tour)
    }
    
    // Lifecycle
    onMounted(() => {
      fetchTours()
    })
    
    return {
      // State
      tours,
      loading,
      saving,
      showCreateModal,
      showEditModal,
      tourForm,
      errors,
      stats,
      
      // Computed
      filteredTours,
      
      // Methods
      fetchTours,
      formatDate,
      formatCurrency,
      closeModal,
      editTour,
      saveTour,
      deleteTour,
      viewTour
    }
  }
}
</script>

<style lang="scss" scoped>
.tours-page {
  max-width: 1400px;
  margin: 0 auto;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.tours-grid {
  margin-bottom: 2rem;
}

.tours-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.tour-card {
  @include card;
  overflow: hidden;
  transition: all var(--transition);
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
  }
}

.tour-image {
  position: relative;
  height: 200px;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.tour-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-full);
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  
  &--active {
    background: var(--success-color);
    color: white;
  }
  
  &--completed {
    background: var(--secondary-color);
    color: white;
  }
  
  &--cancelled {
    background: var(--danger-color);
    color: white;
  }
}

.tour-content {
  padding: 1.5rem;
}

.tour-title {
  font-size: var(--font-size-lg);
  font-weight: var(--font-weight-semibold);
  margin-bottom: 0.5rem;
  color: var(--text-color);
}

.tour-destination {
  @include flex-center;
  gap: 0.5rem;
  color: var(--primary-color);
  font-weight: var(--font-weight-medium);
  margin-bottom: 1rem;
  
  i {
    font-size: var(--font-size-sm);
  }
}

.tour-dates {
  @include flex-column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.tour-date,
.tour-duration {
  @include flex-center;
  gap: 0.5rem;
  color: var(--text-muted);
  font-size: var(--font-size-sm);
  
  i {
    width: 16px;
  }
}

.tour-description {
  color: var(--text-muted);
  font-size: var(--font-size-sm);
  line-height: var(--line-height-relaxed);
  margin-bottom: 1.5rem;
}

.tour-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: var(--background-color);
  border-radius: var(--radius-md);
}

.tour-stat {
  @include flex-column;
  align-items: center;
  gap: 0.25rem;
}

.tour-stat-label {
  font-size: var(--font-size-xs);
  color: var(--text-muted);
}

.tour-stat-value {
  font-weight: var(--font-weight-semibold);
  color: var(--text-color);
}

.tour-actions {
  @include flex-center;
  gap: 0.5rem;
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
  
  .tours-container {
    grid-template-columns: 1fr;
  }
}
</style> 