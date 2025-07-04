<template>
  <component
    :is="tag"
    :class="buttonClasses"
    :disabled="disabled || loading"
    v-bind="$attrs"
    @click="handleClick"
  >
    <i v-if="loading" class="fas fa-spinner fa-spin"></i>
    <i v-else-if="icon && !iconRight" :class="icon"></i>
    <span v-if="$slots.default" class="button-content">
      <slot />
    </span>
    <i v-if="icon && iconRight" :class="icon"></i>
  </component>
</template>

<script>
export default {
  name: 'BaseButton',
  props: {
    variant: {
      type: String,
      default: 'primary',
      validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'outline', 'ghost'].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    disabled: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    icon: {
      type: String,
      default: ''
    },
    iconRight: {
      type: Boolean,
      default: false
    },
    fullWidth: {
      type: Boolean,
      default: false
    },
    tag: {
      type: String,
      default: 'button',
      validator: (value) => ['button', 'a', 'router-link'].includes(value)
    }
  },
  emits: ['click'],
  computed: {
    buttonClasses() {
      return [
        'base-button',
        `base-button--${this.variant}`,
        `base-button--${this.size}`,
        {
          'base-button--full': this.fullWidth,
          'base-button--loading': this.loading
        }
      ]
    }
  },
  methods: {
    handleClick(event) {
      if (this.disabled || this.loading) {
        event.preventDefault()
        return
      }
      this.$emit('click', event)
    }
  }
}
</script>

<style lang="scss" scoped>
.base-button {
  @include button-base;
  position: relative;
  overflow: hidden;
  
  // Variants
  &--primary {
    @include button-primary;
  }
  
  &--secondary {
    @include button-secondary;
  }
  
  &--success {
    @include button-base;
    background: var(--success-color);
    color: white;
    
    &:hover:not(:disabled) {
      background: var(--success-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }
  }
  
  &--danger {
    @include button-base;
    background: var(--danger-color);
    color: white;
    
    &:hover:not(:disabled) {
      background: var(--danger-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }
  }
  
  &--warning {
    @include button-base;
    background: var(--warning-color);
    color: white;
    
    &:hover:not(:disabled) {
      background: var(--warning-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }
  }
  
  &--info {
    @include button-base;
    background: var(--info-color);
    color: white;
    
    &:hover:not(:disabled) {
      background: var(--info-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }
  }
  
  &--outline {
    @include button-outline;
  }
  
  &--ghost {
    @include button-base;
    background: transparent;
    color: var(--primary-color);
    
    &:hover:not(:disabled) {
      background: var(--primary-50);
      color: var(--primary-dark);
    }
  }
  
  // Sizes
  &--sm {
    padding: 0.5rem 1rem;
    font-size: var(--font-size-xs);
    border-radius: var(--radius);
  }
  
  &--md {
    padding: 0.75rem 1.5rem;
    font-size: var(--font-size-sm);
    border-radius: var(--radius-md);
  }
  
  &--lg {
    padding: 1rem 2rem;
    font-size: var(--font-size-base);
    border-radius: var(--radius-lg);
  }
  
  &--xl {
    padding: 1.25rem 2.5rem;
    font-size: var(--font-size-lg);
    border-radius: var(--radius-xl);
  }
  
  // States
  &--full {
    width: 100%;
  }
  
  &--loading {
    pointer-events: none;
    
    .button-content {
      opacity: 0.7;
    }
  }
  
  // Disabled state
  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
  }
  
  // Focus state
  &:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }
  
  // Active state
  &:active:not(:disabled) {
    transform: translateY(0);
  }
  
  // Icon spacing
  i {
    margin: 0 0.25rem;
    
    &:first-child {
      margin-left: 0;
    }
    
    &:last-child {
      margin-right: 0;
    }
  }
  
  // Loading spinner
  .fa-spinner {
    margin-right: 0.5rem;
  }
}

// Link button styles
a.base-button {
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
</style> 