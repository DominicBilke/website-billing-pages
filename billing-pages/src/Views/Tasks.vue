<template>
  <div class="tasks-page">
    <div class="page-header">
      <h1>Task Management</h1>
      <p>Organize and track project tasks and progress</p>
    </div>

    <div class="tasks-content">
      <div class="tasks-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-tasks"></i>
          </div>
          <div class="stat-info">
            <h3>Total Tasks</h3>
            <p class="stat-value">156</p>
            <p class="stat-change positive">+8 this week</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-info">
            <h3>Completed</h3>
            <p class="stat-value">89</p>
            <p class="stat-change positive">+12 this week</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-info">
            <h3>In Progress</h3>
            <p class="stat-value">45</p>
            <p class="stat-change neutral">No change</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="stat-info">
            <h3>Overdue</h3>
            <p class="stat-value">3</p>
            <p class="stat-change negative">+1 this week</p>
          </div>
        </div>
      </div>

      <div class="tasks-actions">
        <button class="btn btn-primary">
          <i class="fas fa-plus"></i>
          Add Task
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-filter"></i>
          Filter
        </button>
        <button class="btn btn-secondary">
          <i class="fas fa-download"></i>
          Export
        </button>
      </div>

      <div class="tasks-grid">
        <div class="task-column">
          <div class="column-header">
            <h3>To Do</h3>
            <span class="task-count">12</span>
          </div>
          <div class="task-list">
            <div v-for="task in todoTasks" :key="task.id" class="task-card">
              <div class="task-header">
                <h4>{{ task.title }}</h4>
                <div class="task-priority" :class="task.priority">
                  {{ task.priority }}
                </div>
              </div>
              <p class="task-description">{{ task.description }}</p>
              <div class="task-meta">
                <div class="task-assignee">
                  <i class="fas fa-user"></i>
                  {{ task.assignee }}
                </div>
                <div class="task-due-date">
                  <i class="fas fa-calendar"></i>
                  {{ formatDate(task.dueDate) }}
                </div>
              </div>
              <div class="task-actions">
                <button class="btn-icon" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon" title="Move to In Progress">
                  <i class="fas fa-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="task-column">
          <div class="column-header">
            <h3>In Progress</h3>
            <span class="task-count">8</span>
          </div>
          <div class="task-list">
            <div v-for="task in inProgressTasks" :key="task.id" class="task-card">
              <div class="task-header">
                <h4>{{ task.title }}</h4>
                <div class="task-priority" :class="task.priority">
                  {{ task.priority }}
                </div>
              </div>
              <p class="task-description">{{ task.description }}</p>
              <div class="task-progress">
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: task.progress + '%' }"></div>
                </div>
                <span class="progress-text">{{ task.progress }}%</span>
              </div>
              <div class="task-meta">
                <div class="task-assignee">
                  <i class="fas fa-user"></i>
                  {{ task.assignee }}
                </div>
                <div class="task-due-date">
                  <i class="fas fa-calendar"></i>
                  {{ formatDate(task.dueDate) }}
                </div>
              </div>
              <div class="task-actions">
                <button class="btn-icon" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon" title="Move to Done">
                  <i class="fas fa-check"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="task-column">
          <div class="column-header">
            <h3>Done</h3>
            <span class="task-count">15</span>
          </div>
          <div class="task-list">
            <div v-for="task in doneTasks" :key="task.id" class="task-card completed">
              <div class="task-header">
                <h4>{{ task.title }}</h4>
                <div class="task-status">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
              <p class="task-description">{{ task.description }}</p>
              <div class="task-meta">
                <div class="task-assignee">
                  <i class="fas fa-user"></i>
                  {{ task.assignee }}
                </div>
                <div class="task-completed-date">
                  <i class="fas fa-calendar-check"></i>
                  {{ formatDate(task.completedDate) }}
                </div>
              </div>
              <div class="task-actions">
                <button class="btn-icon" title="View Details">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn-icon" title="Archive">
                  <i class="fas fa-archive"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const todoTasks = ref([
  {
    id: 1,
    title: 'Design Homepage Layout',
    description: 'Create wireframes and mockups for the new homepage design',
    assignee: 'Jane Smith',
    dueDate: '2024-01-20',
    priority: 'high'
  },
  {
    id: 2,
    title: 'Setup Database Schema',
    description: 'Design and implement the database structure for user management',
    assignee: 'John Doe',
    dueDate: '2024-01-18',
    priority: 'medium'
  }
])

const inProgressTasks = ref([
  {
    id: 3,
    title: 'Implement User Authentication',
    description: 'Build login and registration system with JWT tokens',
    assignee: 'Mike Johnson',
    dueDate: '2024-01-25',
    priority: 'high',
    progress: 75
  },
  {
    id: 4,
    title: 'Create API Documentation',
    description: 'Write comprehensive API documentation using Swagger',
    assignee: 'Sarah Wilson',
    dueDate: '2024-01-22',
    priority: 'medium',
    progress: 45
  }
])

const doneTasks = ref([
  {
    id: 5,
    title: 'Setup Development Environment',
    description: 'Configure development tools and project structure',
    assignee: 'John Doe',
    completedDate: '2024-01-15',
    priority: 'low'
  },
  {
    id: 6,
    title: 'Create Project Repository',
    description: 'Initialize Git repository and setup CI/CD pipeline',
    assignee: 'Mike Johnson',
    completedDate: '2024-01-14',
    priority: 'medium'
  }
])

const formatDate = (date) => {
  return new Intl.DateTimeFormat('de-DE').format(new Date(date))
}
</script>

<style scoped lang="scss">
.tasks-page {
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

.tasks-stats {
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

.tasks-actions {
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

.tasks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.task-column {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  overflow: hidden;
}

.column-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  background: var(--color-background-tertiary);
  border-bottom: 1px solid var(--color-border);
  
  h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-text-primary);
  }
  
  .task-count {
    background: var(--color-primary);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
  }
}

.task-list {
  padding: 1rem;
  max-height: 600px;
  overflow-y: auto;
}

.task-card {
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  
  &:last-child {
    margin-bottom: 0;
  }
  
  &.completed {
    opacity: 0.7;
  }
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
  
  h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text-primary);
    flex: 1;
    margin-right: 0.5rem;
  }
}

.task-priority {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  text-transform: uppercase;
  
  &.high {
    background: var(--color-danger-light);
    color: var(--color-danger);
  }
  
  &.medium {
    background: var(--color-warning-light);
    color: var(--color-warning);
  }
  
  &.low {
    background: var(--color-success-light);
    color: var(--color-success);
  }
}

.task-status {
  color: var(--color-success);
  font-size: 1rem;
}

.task-description {
  font-size: 0.875rem;
  color: var(--color-text-secondary);
  margin-bottom: 1rem;
  line-height: 1.4;
}

.task-progress {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: var(--color-border);
  border-radius: 3px;
  overflow: hidden;
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

.task-meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  font-size: 0.75rem;
  color: var(--color-text-secondary);
  
  > div {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
}

.task-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

.btn-icon {
  width: 28px;
  height: 28px;
  border: 1px solid var(--color-border);
  border-radius: 4px;
  background: var(--color-background);
  color: var(--color-text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  
  &:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
  }
  
  i {
    font-size: 0.75rem;
  }
}

// Dark theme adjustments
[data-theme="dark"] {
  .stat-card,
  .task-column {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .column-header {
    background: var(--color-background-tertiary-dark);
    border-bottom-color: var(--color-border-dark);
  }
  
  .task-card {
    background: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
  
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