<template>
  <div class="work-page">
    <div class="page-header">
      <h1>Work Hours Management</h1>
      <p>Track and manage employee work hours and timesheets</p>
    </div>

    <div class="work-content">
      <div class="work-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-info">
            <h3>Total Hours</h3>
            <p class="stat-value">1,247</p>
            <p class="stat-change positive">+12% this week</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-info">
            <h3>Active Employees</h3>
            <p class="stat-value">24</p>
            <p class="stat-change positive">+2 this month</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-info">
            <h3>Projects</h3>
            <p class="stat-value">8</p>
            <p class="stat-change neutral">No change</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-euro-sign"></i>
          </div>
          <div class="stat-info">
            <h3>Total Cost</h3>
            <p class="stat-value">€45,230</p>
            <p class="stat-change negative">-5% this week</p>
          </div>
        </div>
      </div>

      <div class="work-actions">
        <button class="btn btn-primary">
          <i class="fas fa-plus"></i>
          Add Work Entry
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-download"></i>
          Export Report
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-filter"></i>
          Filter
        </button>
      </div>

      <div class="work-table">
        <div class="table-header">
          <h2>Recent Work Entries</h2>
          <div class="table-actions">
            <input type="text" placeholder="Search entries..." class="search-input">
          </div>
        </div>

        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Project</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Hours</th>
                <th>Rate</th>
                <th>Total</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="entry in workEntries" :key="entry.id">
                <td>
                  <div class="employee-info">
                    <div class="employee-avatar">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <div class="employee-name">{{ entry.employee }}</div>
                      <div class="employee-role">{{ entry.role }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ entry.project }}</td>
                <td>{{ formatDate(entry.date) }}</td>
                <td>{{ entry.startTime }}</td>
                <td>{{ entry.endTime }}</td>
                <td>{{ entry.hours }}</td>
                <td>{{ formatCurrency(entry.rate) }}</td>
                <td>{{ formatCurrency(entry.total) }}</td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-icon" title="Edit">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
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

const workEntries = ref([
  {
    id: 1,
    employee: 'John Doe',
    role: 'Developer',
    project: 'Website Redesign',
    date: '2024-01-15',
    startTime: '09:00',
    endTime: '17:00',
    hours: 8,
    rate: 45,
    total: 360
  },
  {
    id: 2,
    employee: 'Jane Smith',
    role: 'Designer',
    project: 'Mobile App',
    date: '2024-01-15',
    startTime: '08:30',
    endTime: '16:30',
    hours: 8,
    rate: 40,
    total: 320
  },
  {
    id: 3,
    employee: 'Mike Johnson',
    role: 'Project Manager',
    project: 'E-commerce Platform',
    date: '2024-01-15',
    startTime: '09:00',
    endTime: '18:00',
    hours: 9,
    rate: 50,
    total: 450
  }
])

const formatDate = (date) => {
  return new Intl.DateTimeFormat('de-DE').format(new Date(date))
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}
</script>

<style scoped lang="scss">
.work-page {
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

.work-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  
  .stat-icon {
    width: 48px;
    height: 48px;
    background: var(--color-primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
  }
  
  .stat-info {
    flex: 1;
    
    h3 {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--color-text-secondary);
      margin-bottom: 0.5rem;
    }
    
    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--color-text-primary);
      margin-bottom: 0.25rem;
    }
    
    .stat-change {
      font-size: 0.75rem;
      font-weight: 500;
      
      &.positive {
        color: var(--color-success);
      }
      
      &.negative {
        color: var(--color-danger);
      }
      
      &.neutral {
        color: var(--color-text-secondary);
      }
    }
  }
}

.work-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
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

.work-table {
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

.search-input {
  padding: 0.5rem 1rem;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-background);
  color: var(--color-text-primary);
  
  &::placeholder {
    color: var(--color-text-secondary);
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

.employee-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.employee-avatar {
  width: 32px;
  height: 32px;
  background: var(--color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.875rem;
}

.employee-name {
  font-weight: 500;
  color: var(--color-text-primary);
}

.employee-role {
  font-size: 0.75rem;
  color: var(--color-text-secondary);
}

.action-buttons {
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

// Dark theme adjustments
[data-theme="dark"] {
  .stat-card {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .work-table {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .table-header {
    border-bottom-color: var(--color-border-dark);
  }
  
  .data-table th {
    background: var(--color-background-tertiary-dark);
  }
  
  .data-table th,
  .data-table td {
    border-bottom-color: var(--color-border-dark);
  }
  
  .btn-secondary {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
  
  .search-input {
    background: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
  
  .btn-icon {
    background: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
}
</style> 