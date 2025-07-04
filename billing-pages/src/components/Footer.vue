<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section">
        <h3>Billing Pages</h3>
        <p>Professional billing and invoicing portal for modern businesses.</p>
        <div class="social-links">
          <a href="https://www.dominic-bilke.de" target="_blank" rel="noopener noreferrer" class="social-link">
            <i class="fas fa-globe"></i>
          </a>
          <a href="mailto:info@dominic-bilke.de" class="social-link">
            <i class="fas fa-envelope"></i>
          </a>
          <a href="tel:+491748493008" class="social-link">
            <i class="fas fa-phone"></i>
          </a>
        </div>
      </div>
      
      <div class="footer-section">
        <h4>Services</h4>
        <ul class="footer-links">
          <li><a href="#" @click.prevent="navigateTo('dashboard')">Dashboard</a></li>
          <li><a href="#" @click.prevent="navigateTo('companies')">Companies</a></li>
          <li><a href="#" @click.prevent="navigateTo('tours')">Tours</a></li>
          <li><a href="#" @click.prevent="navigateTo('work')">Work Hours</a></li>
          <li><a href="#" @click.prevent="navigateTo('tasks')">Tasks</a></li>
          <li><a href="#" @click.prevent="navigateTo('invoices')">Invoices</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Support</h4>
        <ul class="footer-links">
          <li><a href="mailto:info@dominic-bilke.de">Contact Support</a></li>
          <li><a href="#" @click.prevent="showDocumentation">Documentation</a></li>
          <li><a href="#" @click.prevent="showHelp">Help Center</a></li>
          <li><a href="tel:+491748493008">+49 174 849 3008</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h4>Legal</h4>
        <ul class="footer-links">
          <li><a href="#" @click.prevent="showImprint">Imprint</a></li>
          <li><a href="#" @click.prevent="showPrivacyPolicy">Privacy Policy</a></li>
          <li><a href="#" @click.prevent="showTerms">Terms & Conditions</a></li>
          <li><a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer">EU Dispute Resolution</a></li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="footer-bottom-content">
        <div class="copyright">
          <p>&copy; 2025 Bilke Web- und Softwareentwicklung | DOT.COM | Eine Webseite von Dipl.-Ing. (FH) D. Bilke</p>
        </div>
        <div class="provider-info">
          <p>
            <strong>Bilke Web- und Softwareentwicklung</strong><br>
            Hanauer Landstrasse 291 B, 60314 Frankfurt am Main, Germany<br>
            VAT: DE350967159 | Phone: +49 174 849 3008 | Email: info@dominic-bilke.de
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const navigateTo = (route) => {
  router.push({ name: route })
}

const showImprint = () => {
  window.open('/imprint', '_blank')
}

const showPrivacyPolicy = () => {
  window.open('/privacy-policy', '_blank')
}

const showTerms = () => {
  window.open('/terms', '_blank')
}

const showDocumentation = () => {
  window.open('https://docs.billing-pages.com', '_blank')
}

const showHelp = () => {
  window.open('https://help.billing-pages.com', '_blank')
}
</script>

<style scoped lang="scss">
.footer {
  background: var(--color-background-secondary);
  border-top: 1px solid var(--color-border);
  margin-top: auto;
  
  .footer-content {
    max-width: var(--max-width);
    margin: 0 auto;
    padding: 3rem 2rem 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    
    @media (max-width: 768px) {
      grid-template-columns: 1fr;
      padding: 2rem 1rem 1rem;
    }
  }
  
  .footer-section {
    h3 {
      color: var(--color-primary);
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    h4 {
      color: var(--color-text-primary);
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    p {
      color: var(--color-text-secondary);
      line-height: 1.6;
      margin-bottom: 1rem;
    }
  }
  
  .social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    
    .social-link {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2.5rem;
      height: 2.5rem;
      background: var(--color-primary);
      color: white;
      border-radius: 50%;
      text-decoration: none;
      transition: all 0.3s ease;
      
      &:hover {
        background: var(--color-primary-dark);
        transform: translateY(-2px);
      }
      
      i {
        font-size: 1rem;
      }
    }
  }
  
  .footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
    
    li {
      margin-bottom: 0.5rem;
      
      a {
        color: var(--color-text-secondary);
        text-decoration: none;
        transition: color 0.3s ease;
        
        &:hover {
          color: var(--color-primary);
        }
      }
    }
  }
  
  .footer-bottom {
    background: var(--color-background-tertiary);
    border-top: 1px solid var(--color-border);
    
    .footer-bottom-content {
      max-width: var(--max-width);
      margin: 0 auto;
      padding: 1.5rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      
      @media (max-width: 768px) {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
      }
    }
    
    .copyright {
      p {
        color: var(--color-text-secondary);
        font-size: 0.875rem;
        margin: 0;
      }
    }
    
    .provider-info {
      p {
        color: var(--color-text-secondary);
        font-size: 0.75rem;
        line-height: 1.4;
        margin: 0;
        text-align: right;
        
        @media (max-width: 768px) {
          text-align: center;
        }
      }
    }
  }
}

// Dark theme adjustments
[data-theme="dark"] .footer {
  background: var(--color-background-secondary-dark);
  border-top-color: var(--color-border-dark);
  
  .footer-bottom {
    background: var(--color-background-tertiary-dark);
    border-top-color: var(--color-border-dark);
  }
}
</style> 