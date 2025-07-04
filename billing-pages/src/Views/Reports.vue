<template>
  <div class="reports-page">
    <div class="page-header">
      <h1>Reports & Analytics</h1>
      <p>Comprehensive insights and analytics for your business</p>
    </div>

    <div class="reports-content">
      <div class="reports-filters">
        <div class="filter-group">
          <label>Date Range</label>
          <select class="filter-select">
            <option>Last 7 days</option>
            <option>Last 30 days</option>
            <option>Last 3 months</option>
            <option>Last year</option>
            <option>Custom range</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Report Type</label>
          <select class="filter-select">
            <option>Financial Summary</option>
            <option>Work Hours</option>
            <option>Project Performance</option>
            <option>Client Analysis</option>
          </select>
        </div>
        
        <button class="btn btn-primary">
          <i class="fas fa-chart-line"></i>
          Generate Report
        </button>
      </div>

      <div class="reports-grid">
        <div class="report-card">
          <div class="report-header">
            <h3>Revenue Overview</h3>
            <div class="report-actions">
              <button class="btn-icon" title="Export">
                <i class="fas fa-download"></i>
              </button>
              <button class="btn-icon" title="Share">
                <i class="fas fa-share"></i>
              </button>
            </div>
          </div>
          <div class="report-content">
            <div class="chart-placeholder">
              <i class="fas fa-chart-line"></i>
              <p>Revenue Chart</p>
            </div>
            <div class="report-stats">
              <div class="stat-item">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-value">€125,430</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Growth</span>
                <span class="stat-value positive">+12.5%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="report-card">
          <div class="report-header">
            <h3>Work Hours Analysis</h3>
            <div class="report-actions">
              <button class="btn-icon" title="Export">
                <i class="fas fa-download"></i>
              </button>
              <button class="btn-icon" title="Share">
                <i class="fas fa-share"></i>
              </button>
            </div>
          </div>
          <div class="report-content">
            <div class="chart-placeholder">
              <i class="fas fa-chart-bar"></i>
              <p>Hours Chart</p>
            </div>
            <div class="report-stats">
              <div class="stat-item">
                <span class="stat-label">Total Hours</span>
                <span class="stat-value">1,247</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Avg per Day</span>
                <span class="stat-value">8.3h</span>
              </div>
            </div>
          </div>
        </div>

        <div class="report-card">
          <div class="report-header">
            <h3>Project Performance</h3>
            <div class="report-actions">
              <button class="btn-icon" title="Export">
                <i class="fas fa-download"></i>
              </button>
              <button class="btn-icon" title="Share">
                <i class="fas fa-share"></i>
              </button>
            </div>
          </div>
          <div class="report-content">
            <div class="chart-placeholder">
              <i class="fas fa-chart-pie"></i>
              <p>Projects Chart</p>
            </div>
            <div class="report-stats">
              <div class="stat-item">
                <span class="stat-label">Active Projects</span>
                <span class="stat-value">8</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Completion Rate</span>
                <span class="stat-value">87%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="report-card">
          <div class="report-header">
            <h3>Client Analysis</h3>
            <div class="report-actions">
              <button class="btn-icon" title="Export">
                <i class="fas fa-download"></i>
              </button>
              <button class="btn-icon" title="Share">
                <i class="fas fa-share"></i>
              </button>
            </div>
          </div>
          <div class="report-content">
            <div class="chart-placeholder">
              <i class="fas fa-chart-area"></i>
              <p>Clients Chart</p>
            </div>
            <div class="report-stats">
              <div class="stat-item">
                <span class="stat-label">Total Clients</span>
                <span class="stat-value">24</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">New This Month</span>
                <span class="stat-value">3</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="reports-table">
        <div class="table-header">
          <h2>Top Performing Projects</h2>
          <button class="btn btn-secondary">
            <i class="fas fa-download"></i>
            Export Data
          </button>
        </div>

        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Project</th>
                <th>Client</th>
                <th>Revenue</th>
                <th>Hours</th>
                <th>Progress</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="project in topProjects" :key="project.id">
                <td>
                  <div class="project-info">
                    <div class="project-name">{{ project.name }}</div>
                    <div class="project-category">{{ project.category }}</div>
                  </div>
                </td>
                <td>{{ project.client }}</td>
                <td>{{ formatCurrency(project.revenue) }}</td>
                <td>{{ project.hours }}h</td>
                <td>
                  <div class="progress-bar">
                    <div class="progress-fill" :style="{ width: project.progress + '%' }"></div>
                  </div>
                  <span class="progress-text">{{ project.progress }}%</span>
                </td>
                <td>
                  <span class="status-badge" :class="project.status">
                    {{ project.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const topProjects = ref([
  {
    id: 1,
    name: 'E-commerce Platform',
    category: 'Web Development',
    client: 'Tech Solutions GmbH',
    revenue: 25000,
    hours: 320,
    progress: 85,
    status: 'active'
  },
  {
    id: 2,
    name: 'Mobile App Development',
    category: 'Mobile Development',
    client: 'Digital Marketing Agency',
    revenue: 18000,
    hours: 240,
    progress: 65,
    status: 'active'
  },
  {
    id: 3,
    name: 'Brand Identity Design',
    category: 'Design',
    client: 'Startup Inc.',
    revenue: 12000,
    hours: 160,
    progress: 100,
    status: 'completed'
  },
  {
    id: 4,
    name: 'SEO Optimization',
    category: 'Marketing',
    client: 'E-commerce Solutions',
    revenue: 8000,
    hours: 120,
    progress: 45,
    status: 'active'
  }
])

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}
</script>

<style scoped lang="scss">
.reports-page {
  padding: 2rem;
  max-width: 1400px;
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

.reports-filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: end;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  
  label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--color-text-primary);
  }
}

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-background);
  color: var(--color-text-primary);
  min-width: 150px;
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
}

.reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.report-card {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  overflow: hidden;
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--color-border);
  
  h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-text-primary);
  }
}

.report-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  width: 32px;
  height: 32px;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-background);
  color: var(--color-text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  
  &:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
  }
}

.report-content {
  padding: 1.5rem;
}

.chart-placeholder {
  height: 200px;
  background: var(--color-background-tertiary);
  border: 2px dashed var(--color-border);
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: var(--color-text-secondary);
  margin-bottom: 1.5rem;
  
  i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
  }
  
  p {
    font-size: 0.875rem;
    font-weight: 500;
  }
}

.report-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--color-text-secondary);
  font-weight: 500;
}

.stat-value {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--color-text-primary);
  
  &.positive {
    color: var(--color-success);
  }
  
  &.negative {
    color: var(--color-danger);
  }
}

.reports-table {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  overflow: hidden;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--color-border);
  
  h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text-primary);
  }
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  
  th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--color-border);
  }
  
  th {
    background: var(--color-background-tertiary);
    font-weight: 600;
    color: var(--color-text-primary);
    font-size: 0.875rem;
  }
  
  td {
    color: var(--color-text-secondary);
  }
}

.project-info {
  .project-name {
    font-weight: 500;
    color: var(--color-text-primary);
    margin-bottom: 0.25rem;
  }
  
  .project-category {
    font-size: 0.75rem;
    color: var(--color-text-secondary);
  }
}

.progress-bar {
  width: 100px;
  height: 6px;
  background: var(--color-border);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 0.25rem;
}

.progress-fill {
  height: 100%;
  background: var(--color-primary);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.75rem;
  color: var(--color-text-secondary);
  font-weight: 500;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  
  &.active {
    background: var(--color-success-light);
    color: var(--color-success);
  }
  
  &.completed {
    background: var(--color-primary-light);
    color: var(--color-primary);
  }
  
  &.pending {
    background: var(--color-warning-light);
    color: var(--color-warning);
  }
}

// Dark theme adjustments
[data-theme="dark"] {
  .report-card,
  .reports-table {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .report-header,
  .table-header {
    border-bottom-color: var(--color-border-dark);
  }
  
  .chart-placeholder {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
  
  .data-table th {
    background: var(--color-background-tertiary-dark);
  }
  
  .data-table th,
  .data-table td {
    border-bottom-color: var(--color-border-dark);
  }
  
  .filter-select,
  .btn-secondary {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
  
  .btn-icon {
    background: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
}
</style> 