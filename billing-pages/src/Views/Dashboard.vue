<template>
  <div class="dashboard">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-content">
        <h1>Dashboard</h1>
        <p class="text-muted">Welcome back, {{ user?.name }}! Here's what's happening today.</p>
      </div>
      <div class="page-header-actions">
        <button class="btn btn--primary" @click="refreshData">
          <i class="fas fa-sync-alt"></i>
          Refresh
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--primary">
          <i class="fas fa-building"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.companies }}</h3>
          <p class="stat-card-label">Active Companies</p>
          <div class="stat-card-change stat-card-change--positive">
            <i class="fas fa-arrow-up"></i>
            <span>+12% from last month</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--success">
          <i class="fas fa-route"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.tours }}</h3>
          <p class="stat-card-label">Total Tours</p>
          <div class="stat-card-change stat-card-change--positive">
            <i class="fas fa-arrow-up"></i>
            <span>+8% from last month</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--warning">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ stats.workHours }}</h3>
          <p class="stat-card-label">Work Hours</p>
          <div class="stat-card-change stat-card-change--negative">
            <i class="fas fa-arrow-down"></i>
            <span>-3% from last month</span>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-icon stat-card-icon--info">
          <i class="fas fa-euro-sign"></i>
        </div>
        <div class="stat-card-content">
          <h3 class="stat-card-value">{{ formatCurrency(stats.revenue) }}</h3>
          <p class="stat-card-label">Total Revenue</p>
          <div class="stat-card-change stat-card-change--positive">
            <i class="fas fa-arrow-up"></i>
            <span>+15% from last month</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
      <div class="charts-grid">
        <!-- Revenue Chart -->
        <div class="chart-card">
          <div class="chart-card-header">
            <h3>Revenue Overview</h3>
            <div class="chart-card-actions">
              <select v-model="revenueChartPeriod" class="form-select form-select--sm">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
              </select>
            </div>
          </div>
          <div class="chart-card-body">
            <canvas ref="revenueChart" width="400" height="200"></canvas>
          </div>
        </div>

        <!-- Work Hours Chart -->
        <div class="chart-card">
          <div class="chart-card-header">
            <h3>Work Hours Distribution</h3>
            <div class="chart-card-actions">
              <select v-model="workChartPeriod" class="form-select form-select--sm">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
              </select>
            </div>
          </div>
          <div class="chart-card-body">
            <canvas ref="workChart" width="400" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
      <div class="activity-grid">
        <!-- Recent Companies -->
        <div class="activity-card">
          <div class="activity-card-header">
            <h3>Recent Companies</h3>
            <router-link to="/companies" class="btn btn--outline btn--sm">
              View All
            </router-link>
          </div>
          <div class="activity-card-body">
            <div v-if="recentCompanies.length === 0" class="empty-state">
              <i class="fas fa-building"></i>
              <p>No recent companies</p>
            </div>
            <div v-else class="activity-list">
              <div
                v-for="company in recentCompanies"
                :key="company.id"
                class="activity-item"
              >
                <div class="activity-item-avatar">
                  <img :src="company.logo || '/assets/images/default-company.png'" :alt="company.name" />
                </div>
                <div class="activity-item-content">
                  <h4 class="activity-item-title">{{ company.name }}</h4>
                  <p class="activity-item-subtitle">{{ company.industry }}</p>
                  <p class="activity-item-time">{{ formatDate(company.created_at) }}</p>
                </div>
                <div class="activity-item-actions">
                  <router-link :to="`/companies/${company.id}`" class="btn btn--outline btn--sm">
                    View
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Tours -->
        <div class="activity-card">
          <div class="activity-card-header">
            <h3>Recent Tours</h3>
            <router-link to="/tours" class="btn btn--outline btn--sm">
              View All
            </router-link>
          </div>
          <div class="activity-card-body">
            <div v-if="recentTours.length === 0" class="empty-state">
              <i class="fas fa-route"></i>
              <p>No recent tours</p>
            </div>
            <div v-else class="activity-list">
              <div
                v-for="tour in recentTours"
                :key="tour.id"
                class="activity-item"
              >
                <div class="activity-item-avatar activity-item-avatar--tour">
                  <i class="fas fa-route"></i>
                </div>
                <div class="activity-item-content">
                  <h4 class="activity-item-title">{{ tour.name }}</h4>
                  <p class="activity-item-subtitle">{{ tour.destination }}</p>
                  <p class="activity-item-time">{{ formatDate(tour.start_date) }}</p>
                </div>
                <div class="activity-item-actions">
                  <router-link :to="`/tours/${tour.id}`" class="btn btn--outline btn--sm">
                    View
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h3>Quick Actions</h3>
      <div class="quick-actions-grid">
        <router-link to="/companies/create" class="quick-action-card">
          <div class="quick-action-icon">
            <i class="fas fa-plus"></i>
          </div>
          <h4>Add Company</h4>
          <p>Create a new company profile</p>
        </router-link>

        <router-link to="/tours/create" class="quick-action-card">
          <div class="quick-action-icon">
            <i class="fas fa-route"></i>
          </div>
          <h4>Create Tour</h4>
          <p>Plan a new tour or trip</p>
        </router-link>

        <router-link to="/work/timesheet" class="quick-action-card">
          <div class="quick-action-icon">
            <i class="fas fa-clock"></i>
          </div>
          <h4>Log Time</h4>
          <p>Record work hours</p>
        </router-link>

        <router-link to="/money/invoice/create" class="quick-action-card">
          <div class="quick-action-icon">
            <i class="fas fa-file-invoice"></i>
          </div>
          <h4>Create Invoice</h4>
          <p>Generate a new invoice</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'
import { Chart, registerables } from 'chart.js'

// Register Chart.js components
Chart.register(...registerables)

export default {
  name: 'Dashboard',
  setup() {
    const authStore = useAuthStore()
    const notificationStore = useNotificationStore()
    
    // Refs
    const revenueChart = ref(null)
    const workChart = ref(null)
    const revenueChartPeriod = ref('30')
    const workChartPeriod = ref('30')
    
    // Data
    const stats = ref({
      companies: 0,
      tours: 0,
      workHours: 0,
      revenue: 0
    })
    
    const recentCompanies = ref([])
    const recentTours = ref([])
    
    // Computed
    const user = computed(() => authStore.user)
    
    // Methods
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }
    
    const formatDate = (date) => {
      return new Intl.DateTimeFormat('de-DE').format(new Date(date))
    }
    
    const fetchDashboardData = async () => {
      try {
        // Fetch stats
        const statsResponse = await fetch('/api/dashboard/stats')
        stats.value = await statsResponse.json()
        
        // Fetch recent companies
        const companiesResponse = await fetch('/api/companies/recent')
        recentCompanies.value = await companiesResponse.json()
        
        // Fetch recent tours
        const toursResponse = await fetch('/api/tours/recent')
        recentTours.value = await toursResponse.json()
        
        // Update charts
        updateCharts()
      } catch (error) {
        notificationStore.error('Failed to load dashboard data')
        console.error('Dashboard data error:', error)
      }
    }
    
    const updateCharts = () => {
      // Revenue Chart
      if (revenueChart.value) {
        const ctx = revenueChart.value.getContext('2d')
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
              label: 'Revenue',
              data: [12000, 19000, 15000, 25000, 22000, 30000],
              borderColor: '#2563eb',
              backgroundColor: 'rgba(37, 99, 235, 0.1)',
              tension: 0.4,
              fill: true
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return formatCurrency(value)
                  }
                }
              }
            }
          }
        })
      }
      
      // Work Hours Chart
      if (workChart.value) {
        const ctx = workChart.value.getContext('2d')
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
              label: 'Hours',
              data: [8, 7.5, 8.5, 6, 9, 4, 2],
              backgroundColor: '#f59e0b',
              borderRadius: 4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                max: 10
              }
            }
          }
        })
      }
    }
    
    const refreshData = async () => {
      await fetchDashboardData()
      notificationStore.success('Dashboard data refreshed')
    }
    
    // Watchers
    watch(revenueChartPeriod, () => {
      updateCharts()
    })
    
    watch(workChartPeriod, () => {
      updateCharts()
    })
    
    // Lifecycle
    onMounted(() => {
      fetchDashboardData()
    })
    
    return {
      // Refs
      revenueChart,
      workChart,
      revenueChartPeriod,
      workChartPeriod,
      
      // Data
      stats,
      recentCompanies,
      recentTours,
      
      // Computed
      user,
      
      // Methods
      formatCurrency,
      formatDate,
      refreshData
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  @include flex-between;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.page-header-content h1 {
  margin-bottom: 0.5rem;
}

.page-header-actions {
  @include flex-center;
  gap: 1rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  @include card;
  padding: 1.5rem;
  @include flex-center;
  gap: 1rem;
  transition: transform var(--transition);
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }
}

.stat-card-icon {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-lg);
  @include flex-center;
  font-size: 1.5rem;
  color: white;
  
  &--primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  }
  
  &--success {
    background: linear-gradient(135deg, var(--success-color), var(--success-dark));
  }
  
  &--warning {
    background: linear-gradient(135deg, var(--warning-color), var(--warning-dark));
  }
  
  &--info {
    background: linear-gradient(135deg, var(--info-color), var(--info-dark));
  }
}

.stat-card-content {
  flex: 1;
}

.stat-card-value {
  font-size: var(--font-size-3xl);
  font-weight: var(--font-weight-bold);
  margin-bottom: 0.25rem;
  color: var(--text-color);
}

.stat-card-label {
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.stat-card-change {
  @include flex-center;
  gap: 0.25rem;
  font-size: var(--font-size-sm);
  
  &--positive {
    color: var(--success-color);
  }
  
  &--negative {
    color: var(--danger-color);
  }
}

.charts-section {
  margin-bottom: 2rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 1.5rem;
}

.chart-card {
  @include card;
  padding: 1.5rem;
}

.chart-card-header {
  @include flex-between;
  margin-bottom: 1.5rem;
}

.chart-card-body {
  height: 300px;
  position: relative;
}

.activity-section {
  margin-bottom: 2rem;
}

.activity-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
}

.activity-card {
  @include card;
  padding: 1.5rem;
}

.activity-card-header {
  @include flex-between;
  margin-bottom: 1.5rem;
}

.activity-list {
  @include flex-column;
  gap: 1rem;
}

.activity-item {
  @include flex-center;
  gap: 1rem;
  padding: 1rem;
  border-radius: var(--radius-md);
  background: var(--background-color);
  transition: background var(--transition);
  
  &:hover {
    background: var(--border-light);
  }
}

.activity-item-avatar {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-full);
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  &--tour {
    background: var(--primary-color);
    @include flex-center;
    color: white;
    font-size: 1.25rem;
  }
}

.activity-item-content {
  flex: 1;
}

.activity-item-title {
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-medium);
  margin-bottom: 0.25rem;
}

.activity-item-subtitle {
  color: var(--text-muted);
  font-size: var(--font-size-sm);
  margin-bottom: 0.25rem;
}

.activity-item-time {
  color: var(--text-light);
  font-size: var(--font-size-xs);
}

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

.quick-actions {
  margin-bottom: 2rem;
}

.quick-actions h3 {
  margin-bottom: 1.5rem;
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.quick-action-card {
  @include card;
  padding: 1.5rem;
  text-decoration: none;
  color: var(--text-color);
  transition: all var(--transition);
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--text-color);
  }
}

.quick-action-icon {
  width: 48px;
  height: 48px;
  background: var(--primary-50);
  color: var(--primary-color);
  border-radius: var(--radius-lg);
  @include flex-center;
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.quick-action-card h4 {
  margin-bottom: 0.5rem;
}

.quick-action-card p {
  color: var(--text-muted);
  font-size: var(--font-size-sm);
  margin: 0;
}

@include responsive(sm) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .activity-grid {
    grid-template-columns: 1fr;
  }
  
  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
}
</style> 