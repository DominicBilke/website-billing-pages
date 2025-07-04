<template>
  <div class="imprint-page">
    <div class="container">
      <div class="imprint-content">
        <h1>Imprint</h1>
        
        <div class="section">
          <h2>Provider</h2>
          <div class="provider-info">
            <p><strong>Bilke Web- und Softwareentwicklung</strong></p>
            <p>Hanauer Landstrasse 291 B</p>
            <p>60314 Frankfurt am Main</p>
            <p>Germany</p>
            <br>
            <p><strong>VAT registration number:</strong> DE350967159</p>
            <p><strong>Telephone:</strong> +49 174 849 3008</p>
            <p><strong>E-mail:</strong> info@dominic-bilke.de</p>
          </div>
        </div>

        <div class="section">
          <h2>EU Dispute Resolution (ODR Platform)</h2>
          <p>
            The EU Commission has set up the European-Online-Dispute-Resolution (ODR) platform 
            for the extrajudicial online settlement of disputes between consumers and businesses. 
            You can reach the platform by clicking this link:
          </p>
          <div class="link-container">
            <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer" class="external-link">
              https://ec.europa.eu/consumers/odr
              <i class="fas fa-external-link-alt"></i>
            </a>
          </div>
          <p>
            We participate in this dispute resolution procedure. Our email address is 
            <a href="mailto:freelancer@dominic-bilke.de">freelancer@dominic-bilke.de</a>.
          </p>
        </div>

        <div class="section">
          <h2>Professional Law</h2>
          <div class="professional-info">
            <p><strong>Legal occupational title:</strong> Freiberuflicher Ingenieur</p>
            <p><strong>Awarding State:</strong> Deutschland, Sachsen</p>
            <p><strong>Professional regulations:</strong> Sächsisches Ingenieurgesetz</p>
          </div>
        </div>

        <div class="section">
          <h2>Contact</h2>
          <div class="contact-info">
            <p><strong>Bilke Web- und Softwareentwicklung</strong></p>
            <p>Hanauer Landstrasse 291 B</p>
            <p>60314 Frankfurt am Main</p>
            <br>
            <p><strong>Telephone:</strong> +49 174 849 3008</p>
            <p><strong>Email:</strong> info@dominic-bilke.de</p>
          </div>
        </div>

        <div class="section">
          <h2>IT-Knowledge</h2>
          <p>Information Technology and Internet</p>
          <ul class="expertise-list">
            <li>Web development and design</li>
            <li>Internet programming</li>
            <li>Successful web development</li>
            <li>Node.js: A comprehensive introduction</li>
            <li>The art of SaaS creation</li>
            <li>Software engineering</li>
            <li>Everyday work</li>
            <li>Artificial intelligence</li>
            <li>IT Security</li>
          </ul>
        </div>

        <div class="section">
          <h2>Legal</h2>
          <div class="legal-links">
            <router-link to="/imprint" class="legal-link active">
              <i class="fas fa-file-contract"></i>
              Imprint
            </router-link>
            <router-link to="/privacy-policy" class="legal-link">
              <i class="fas fa-shield-alt"></i>
              Privacy Policy
            </router-link>
            <router-link to="/terms" class="legal-link">
              <i class="fas fa-gavel"></i>
              Terms and Conditions
            </router-link>
          </div>
        </div>

        <div class="footer-note">
          <p>&copy; 2025 Bilke Web- und Softwareentwicklung | DOT.COM | Eine Webseite von Dipl.-Ing. (FH) D. Bilke</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No additional logic needed for this static page
</script>

<style scoped lang="scss">
.imprint-page {
  min-height: 100vh;
  background: var(--color-background);
  color: var(--color-text-primary);
  padding: 2rem 0;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
}

.imprint-content {
  background: var(--color-background-secondary);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 3rem;
  
  @media (max-width: 768px) {
    padding: 2rem 1.5rem;
  }
  
  h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: 2rem;
    text-align: center;
    
    @media (max-width: 768px) {
      font-size: 2rem;
    }
  }
}

.section {
  margin-bottom: 3rem;
  
  h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--color-primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-primary);
  }
  
  p {
    font-size: 1rem;
    line-height: 1.6;
    color: var(--color-text-secondary);
    margin-bottom: 1rem;
    
    strong {
      color: var(--color-text-primary);
    }
  }
}

.provider-info,
.contact-info,
.professional-info {
  background: var(--color-background-tertiary);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 1.5rem;
  margin-top: 1rem;
  
  p {
    margin-bottom: 0.5rem;
    
    &:last-child {
      margin-bottom: 0;
    }
  }
}

.link-container {
  margin: 1rem 0;
}

.external-link {
  display: inline-flex;
  align-items: center;
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  padding: 0.75rem 1rem;
  background: var(--color-background-tertiary);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  transition: all 0.3s ease;
  
  &:hover {
    background: var(--color-primary);
    color: white;
    transform: translateY(-2px);
  }
  
  i {
    margin-left: 0.5rem;
    font-size: 0.875rem;
  }
}

.expertise-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0;
  
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
      margin-right: 0.75rem;
    }
  }
}

.legal-links {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  
  @media (max-width: 768px) {
    flex-direction: column;
  }
}

.legal-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  background: var(--color-background-tertiary);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  text-decoration: none;
  color: var(--color-text-secondary);
  transition: all 0.3s ease;
  
  &:hover,
  &.active {
    background: var(--color-primary);
    color: white;
  }
  
  i {
    margin-right: 0.5rem;
    font-size: 0.875rem;
  }
}

.footer-note {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid var(--color-border);
  text-align: center;
  
  p {
    color: var(--color-text-secondary);
    font-size: 0.875rem;
    margin: 0;
  }
}

// Dark theme adjustments
[data-theme="dark"] {
  .imprint-content {
    background: var(--color-background-secondary-dark);
    border-color: var(--color-border-dark);
  }
  
  .provider-info,
  .contact-info,
  .professional-info {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
  
  .external-link,
  .legal-link {
    background: var(--color-background-tertiary-dark);
    border-color: var(--color-border-dark);
  }
}
</style> 