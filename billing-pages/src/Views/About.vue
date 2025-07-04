<template>
  <div class="about-page">
    <div class="container">
      <div class="hero-section">
        <h1>About Billing Pages</h1>
        <p class="hero-subtitle">
          Professional billing and invoicing portal designed for modern businesses
        </p>
      </div>

      <div class="content-grid">
        <div class="content-section">
          <h2>Our Mission</h2>
          <p>
            Billing Pages was created to simplify and streamline the complex process of business billing, 
            invoicing, and financial management. We believe that every business deserves access to 
            professional-grade tools that help them focus on what they do best - serving their customers.
          </p>
        </div>

        <div class="content-section">
          <h2>Key Features</h2>
          <div class="features-grid">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-building"></i>
              </div>
              <h3>Company Management</h3>
              <p>Comprehensive company profiles with employee management and organizational tools.</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-route"></i>
              </div>
              <h3>Tour Management</h3>
              <p>Plan and track tours with GPS integration and real-time location services.</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-clock"></i>
              </div>
              <h3>Work Time Tracking</h3>
              <p>Advanced timesheet management with detailed work hour tracking and reporting.</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-tasks"></i>
              </div>
              <h3>Task Management</h3>
              <p>Organize projects and tasks with progress tracking and team collaboration.</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-file-invoice-dollar"></i>
              </div>
              <h3>Invoice Generation</h3>
              <p>Professional invoice creation with automated billing and payment tracking.</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <h3>Analytics & Reports</h3>
              <p>Comprehensive reporting and analytics to drive business insights and decisions.</p>
            </div>
          </div>
        </div>

        <div class="content-section">
          <h2>Technology Stack</h2>
          <div class="tech-stack">
            <div class="tech-category">
              <h3>Frontend</h3>
              <ul>
                <li>Vue.js 3 - Progressive JavaScript framework</li>
                <li>Vite - Next generation build tool</li>
                <li>Pinia - State management</li>
                <li>Vue Router - Client-side routing</li>
                <li>SCSS - Advanced CSS preprocessing</li>
              </ul>
            </div>
            
            <div class="tech-category">
              <h3>Backend</h3>
              <ul>
                <li>PHP 8.2+ - Modern PHP with latest features</li>
                <li>Laravel - PHP web application framework</li>
                <li>MySQL 8.0+ - Reliable database system</li>
                <li>Redis - High-performance caching</li>
                <li>JWT - Secure authentication</li>
              </ul>
            </div>
            
            <div class="tech-category">
              <h3>Infrastructure</h3>
              <ul>
                <li>Ubuntu 24.04 - Latest LTS server OS</li>
                <li>Nginx - High-performance web server</li>
                <li>SSL/TLS - Secure connections</li>
                <li>Docker - Containerization</li>
                <li>Git - Version control</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="content-section">
          <h2>About Bilke Web- und Softwareentwicklung</h2>
          <div class="company-info">
            <div class="company-details">
              <h3>Professional Web and Software Development</h3>
              <p>
                Billing Pages is developed and maintained by Bilke Web- und Softwareentwicklung, 
                a professional software development company based in Frankfurt am Main, Germany.
              </p>
              
              <div class="company-stats">
                <div class="stat">
                  <div class="stat-number">10+</div>
                  <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat">
                  <div class="stat-number">50+</div>
                  <div class="stat-label">Projects Completed</div>
                </div>
                <div class="stat">
                  <div class="stat-number">100%</div>
                  <div class="stat-label">Client Satisfaction</div>
                </div>
              </div>
              
              <div class="expertise-areas">
                <h4>Areas of Expertise</h4>
                <ul>
                  <li>Web Development and Design</li>
                  <li>Internet Programming</li>
                  <li>Successful Web Development</li>
                  <li>Node.js Development</li>
                  <li>SaaS Creation</li>
                  <li>Software Engineering</li>
                  <li>Artificial Intelligence</li>
                  <li>IT Security</li>
                </ul>
              </div>
            </div>
            
            <div class="contact-info">
              <h4>Contact Information</h4>
              <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                  <strong>Address:</strong><br>
                  Hanauer Landstrasse 291 B<br>
                  60314 Frankfurt am Main<br>
                  Germany
                </div>
              </div>
              
              <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                  <strong>Phone:</strong><br>
                  +49 174 849 3008
                </div>
              </div>
              
              <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                  <strong>Email:</strong><br>
                  info@dominic-bilke.de
                </div>
              </div>
              
              <div class="contact-item">
                <i class="fas fa-globe"></i>
                <div>
                  <strong>Website:</strong><br>
                  <a href="https://www.dominic-bilke.de" target="_blank" rel="noopener noreferrer">
                    www.dominic-bilke.de
                  </a>
                </div>
              </div>
              
              <div class="legal-info">
                <p><strong>VAT Registration Number:</strong> DE350967159</p>
                <p><strong>Legal Title:</strong> Freiberuflicher Ingenieur</p>
                <p><strong>Professional Regulations:</strong> Sächsisches Ingenieurgesetz</p>
              </div>
            </div>
          </div>
        </div>

        <div class="content-section">
          <h2>Legal Information</h2>
          <div class="legal-links">
            <a href="#" @click.prevent="showImprint" class="legal-link">
              <i class="fas fa-file-contract"></i>
              <span>Imprint</span>
            </a>
            <a href="#" @click.prevent="showPrivacyPolicy" class="legal-link">
              <i class="fas fa-shield-alt"></i>
              <span>Privacy Policy</span>
            </a>
            <a href="#" @click.prevent="showTerms" class="legal-link">
              <i class="fas fa-gavel"></i>
              <span>Terms & Conditions</span>
            </a>
            <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer" class="legal-link">
              <i class="fas fa-balance-scale"></i>
              <span>EU Dispute Resolution</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const showImprint = () => {
  window.open('/imprint', '_blank')
}

const showPrivacyPolicy = () => {
  window.open('/privacy-policy', '_blank')
}

const showTerms = () => {
  window.open('/terms', '_blank')
}
</script>

<style scoped lang="scss">
.about-page {
  min-height: 100vh;
  background: var(--color-background);
  color: var(--color-text-primary);
}

.container {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 2rem;
}

.hero-section {
  text-align: center;
  margin-bottom: 4rem;
  
  h1 {
    font-size: 3rem;
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: 1rem;
    
    @media (max-width: 768px) {
      font-size: 2rem;
    }
  }
  
  .hero-subtitle {
    font-size: 1.25rem;
    color: var(--color-text-secondary);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
  }
}

.content-grid {
  display: flex;
  flex-direction: column;
  gap: 4rem;
}

.content-section {
  h2 {
    font-size: 2rem;
    font-weight: 600;
    color: var(--color-primary);
    margin-bottom: 2rem;
    text-align: center;
  }
  
  p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--color-text-secondary);
    margin-bottom: 1.5rem;
  }
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}

.feature-card {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }
  
  .feature-icon {
    width: 4rem;
    height: 4rem;
    background: var(--color-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    
    i {
      font-size: 1.5rem;
      color: white;
    }
  }
  
  h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 1rem;
  }
  
  p {
    color: var(--color-text-secondary);
    line-height: 1.6;
    margin: 0;
  }
}

.tech-stack {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}

.tech-category {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 2rem;
  
  h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-primary);
    margin-bottom: 1rem;
  }
  
  ul {
    list-style: none;
    padding: 0;
    margin: 0;
    
    li {
      color: var(--color-text-secondary);
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--color-border-light);
      
      &:last-child {
        border-bottom: none;
      }
      
      &:before {
        content: "✓";
        color: var(--color-success);
        font-weight: bold;
        margin-right: 0.5rem;
      }
    }
  }
}

.company-info {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}

.company-details {
  h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--color-primary);
    margin-bottom: 1rem;
  }
  
  p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--color-text-secondary);
    margin-bottom: 2rem;
  }
}

.company-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}

.stat {
  text-align: center;
  padding: 1.5rem;
  background: var(--color-background-tertiary);
  border-radius: 8px;
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: 0.5rem;
  }
  
  .stat-label {
    color: var(--color-text-secondary);
    font-size: 0.875rem;
  }
}

.expertise-areas {
  h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 1rem;
  }
  
  ul {
    list-style: none;
    padding: 0;
    margin: 0;
    
    li {
      color: var(--color-text-secondary);
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--color-border-light);
      
      &:last-child {
        border-bottom: none;
      }
      
      &:before {
        content: "•";
        color: var(--color-primary);
        font-weight: bold;
        margin-right: 0.5rem;
      }
    }
  }
}

.contact-info {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 2rem;
  
  h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-primary);
    margin-bottom: 1.5rem;
  }
}

.contact-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  
  i {
    color: var(--color-primary);
    font-size: 1.25rem;
    margin-right: 1rem;
    margin-top: 0.25rem;
  }
  
  div {
    color: var(--color-text-secondary);
    line-height: 1.6;
    
    strong {
      color: var(--color-text-primary);
    }
    
    a {
      color: var(--color-primary);
      text-decoration: none;
      
      &:hover {
        text-decoration: underline;
      }
    }
  }
}

.legal-info {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--color-border);
  
  p {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
    margin-bottom: 0.5rem;
    
    strong {
      color: var(--color-text-primary);
    }
  }
}

.legal-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  
  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}

.legal-link {
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  text-decoration: none;
  color: var(--color-text-primary);
  transition: all 0.3s ease;
  
  &:hover {
    background: var(--color-primary);
    color: white;
    transform: translateY(-2px);
  }
  
  i {
    font-size: 1.25rem;
    margin-right: 1rem;
  }
  
  span {
    font-weight: 500;
  }
}

// Dark theme adjustments
[data-theme="dark"] {
  .feature-card,
  .tech-category,
  .contact-info {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .stat {
    background: var(--color-background-tertiary-dark);
  }
  
  .legal-link {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
}
</style> 