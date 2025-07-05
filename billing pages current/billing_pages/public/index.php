<?php
require_once __DIR__ . '/../inc/config.php';

// Redirect to dashboard if user is logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$pageTitle = 'Billing Pages - Professional Invoice Management';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <meta name="description" content="Professional billing and invoice management system. Create, send, and track invoices with ease.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= getAssetUrl('images/favicon.ico') ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= getAssetUrl('css/style.css') ?>" rel="stylesheet">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="<?= getAssetUrl('images/hero-bg.jpg') ?>" as="image">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= getBaseUrl() ?>">
                <i class="fas fa-file-invoice me-2"></i>
                Billing Pages
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2" href="auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light btn-sm ms-2" href="auth/register.php">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Professional Invoice Management Made Simple
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Create, send, and track invoices with ease. Get paid faster with our comprehensive billing solution designed for modern businesses.
                    </p>
                    <div class="hero-buttons">
                        <a href="auth/register.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>
                            Start Free Trial
                        </a>
                        <a href="#demo" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play me-2"></i>
                            Watch Demo
                        </a>
                    </div>
                    <div class="mt-4">
                        <p class="text-white-50 mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            No credit card required
                        </p>
                        <p class="text-white-50 mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            14-day free trial
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="<?= getAssetUrl('images/dashboard-preview.png') ?>" alt="Dashboard Preview" class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Everything You Need to Manage Your Billing</h2>
                    <p class="lead text-muted">Powerful features designed to streamline your invoicing process</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h4>Invoice Creation</h4>
                        <p>Create professional invoices in minutes with our intuitive editor and customizable templates.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4>Online Payments</h4>
                        <p>Accept payments online with Stripe integration. Get paid faster with secure payment processing.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Analytics & Reports</h4>
                        <p>Track your business performance with detailed reports and real-time analytics dashboard.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Client Management</h4>
                        <p>Organize your clients with detailed profiles, contact information, and payment history.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Automated Reminders</h4>
                        <p>Set up automatic payment reminders to reduce late payments and improve cash flow.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile Responsive</h4>
                        <p>Access your billing system from anywhere with our fully responsive mobile-friendly design.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Simple, Transparent Pricing</h2>
                    <p class="lead text-muted">Choose the plan that fits your business needs</p>
                </div>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h4>Starter</h4>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">9</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="pricing-features">
                            <ul>
                                <li><i class="fas fa-check text-success me-2"></i>Up to 50 invoices/month</li>
                                <li><i class="fas fa-check text-success me-2"></i>Basic templates</li>
                                <li><i class="fas fa-check text-success me-2"></i>Email support</li>
                                <li><i class="fas fa-check text-success me-2"></i>PDF generation</li>
                                <li><i class="fas fa-check text-success me-2"></i>Client management</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <a href="auth/register.php?plan=starter" class="btn btn-outline-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">Most Popular</div>
                        <div class="pricing-header">
                            <h4>Professional</h4>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">29</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="pricing-features">
                            <ul>
                                <li><i class="fas fa-check text-success me-2"></i>Unlimited invoices</li>
                                <li><i class="fas fa-check text-success me-2"></i>Custom templates</li>
                                <li><i class="fas fa-check text-success me-2"></i>Priority support</li>
                                <li><i class="fas fa-check text-success me-2"></i>Online payments</li>
                                <li><i class="fas fa-check text-success me-2"></i>Advanced reports</li>
                                <li><i class="fas fa-check text-success me-2"></i>Automated reminders</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <a href="auth/register.php?plan=professional" class="btn btn-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h4>Enterprise</h4>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">99</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="pricing-features">
                            <ul>
                                <li><i class="fas fa-check text-success me-2"></i>Everything in Professional</li>
                                <li><i class="fas fa-check text-success me-2"></i>API access</li>
                                <li><i class="fas fa-check text-success me-2"></i>White-label options</li>
                                <li><i class="fas fa-check text-success me-2"></i>Dedicated support</li>
                                <li><i class="fas fa-check text-success me-2"></i>Custom integrations</li>
                                <li><i class="fas fa-check text-success me-2"></i>SLA guarantee</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <a href="auth/register.php?plan=enterprise" class="btn btn-outline-primary w-100">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-3">Ready to Get Started?</h3>
                    <p class="lead mb-0">Join thousands of businesses that trust Billing Pages for their invoicing needs.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="auth/register.php" class="btn btn-light btn-lg">
                        Start Your Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-file-invoice me-2"></i>
                        Billing Pages
                    </h5>
                    <p class="text-muted">Professional invoice management made simple. Create, send, and track invoices with ease.</p>
                    <div class="social-links">
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-muted text-decoration-none">Features</a></li>
                        <li><a href="#pricing" class="text-muted text-decoration-none">Pricing</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">API</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Integrations</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Documentation</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Status</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">About</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Careers</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Press</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="privacy.php" class="text-muted text-decoration-none">Privacy</a></li>
                        <li><a href="terms.php" class="text-muted text-decoration-none">Terms</a></li>
                        <li><a href="security.php" class="text-muted text-decoration-none">Security</a></li>
                        <li><a href="compliance.php" class="text-muted text-decoration-none">Compliance</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?= date('Y') ?> Billing Pages. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">Made with <i class="fas fa-heart text-danger"></i> for modern businesses</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= getAssetUrl('js/main.js') ?>"></script>
</body>
</html> 