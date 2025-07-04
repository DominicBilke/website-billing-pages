<template>
  <div class="invoices-page">
    <div class="page-header">
      <h1>Invoice Management</h1>
      <p>Create, manage, and track invoices and payments</p>
    </div>

    <div class="invoices-content">
      <div class="invoices-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="stat-info">
            <h3>Total Invoices</h3>
            <p class="stat-value">247</p>
            <p class="stat-change positive">+15 this month</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-euro-sign"></i>
          </div>
          <div class="stat-info">
            <h3>Total Revenue</h3>
            <p class="stat-value">€125,430</p>
            <p class="stat-change positive">+8% this month</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-info">
            <h3>Pending</h3>
            <p class="stat-value">€23,450</p>
            <p class="stat-change negative">+5 this week</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-info">
            <h3>Paid</h3>
            <p class="stat-value">€101,980</p>
            <p class="stat-change positive">+12 this week</p>
          </div>
        </div>
      </div>

      <div class="invoices-actions">
        <button class="btn btn-primary">
          <i class="fas fa-plus"></i>
          Create Invoice
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-download"></i>
          Export
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-filter"></i>
          Filter
        </button>
      </div>

      <div class="invoices-table">
        <div class="table-header">
          <h2>Recent Invoices</h2>
          <div class="table-actions">
            <input type="text" placeholder="Search invoices..." class="search-input">
          </div>
        </div>

        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="invoice in invoices" :key="invoice.id">
                <td>
                  <div class="invoice-number">
                    <strong>{{ invoice.number }}</strong>
                  </div>
                </td>
                <td>
                  <div class="client-info">
                    <div class="client-name">{{ invoice.client }}</div>
                    <div class="client-email">{{ invoice.email }}</div>
                  </div>
                </td>
                <td>{{ formatDate(invoice.date) }}</td>
                <td>{{ formatDate(invoice.dueDate) }}</td>
                <td>{{ formatCurrency(invoice.amount) }}</td>
                <td>
                  <span class="status-badge" :class="invoice.status">
                    {{ invoice.status }}
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-icon" title="View">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" title="Edit">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" title="Download">
                      <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-icon" title="Send">
                      <i class="fas fa-paper-plane"></i>
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

const invoices = ref([
  {
    id: 1,
    number: 'INV-2024-001',
    client: 'Tech Solutions GmbH',
    email: 'contact@techsolutions.de',
    date: '2024-01-15',
    dueDate: '2024-02-15',
    amount: 2500,
    status: 'paid'
  },
  {
    id: 2,
    number: 'INV-2024-002',
    client: 'Digital Marketing Agency',
    email: 'billing@digitalmarketing.de',
    date: '2024-01-16',
    dueDate: '2024-02-16',
    amount: 1800,
    status: 'pending'
  },
  {
    id: 3,
    number: 'INV-2024-003',
    client: 'Web Development Studio',
    email: 'info@webdevstudio.de',
    date: '2024-01-17',
    dueDate: '2024-02-17',
    amount: 3200,
    status: 'overdue'
  },
  {
    id: 4,
    number: 'INV-2024-004',
    client: 'E-commerce Solutions',
    email: 'accounts@ecommerce.de',
    date: '2024-01-18',
    dueDate: '2024-02-18',
    amount: 4200,
    status: 'draft'
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
.invoices-page {
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

.invoices-stats {
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

.invoices-actions {
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

.invoices-table {
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

.invoice-number {
  font-family: 'Courier New', monospace;
}

.client-info {
  .client-name {
    font-weight: 500;
    color: var(--color-text-primary);
    margin-bottom: 0.25rem;
  }
  
  .client-email {
    font-size: 0.75rem;
    color: var(--color-text-secondary);
  }
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  
  &.paid {
    background: var(--color-success-light);
    color: var(--color-success);
  }
  
  &.pending {
    background: var(--color-warning-light);
    color: var(--color-warning);
  }
  
  &.overdue {
    background: var(--color-danger-light);
    color: var(--color-danger);
  }
  
  &.draft {
    background: var(--color-text-secondary-light);
    color: var(--color-text-secondary);
  }
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
  .stat-card,
  .invoices-table {
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