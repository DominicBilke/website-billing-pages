<?php
require_once __DIR__ . '/../inc/header.php';

// Redirect to dashboard if user is logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>Professional Billing Management</h1>
            <p class="lead">Streamline your company's billing process with our comprehensive solution</p>
            <div class="hero-buttons">
                <a href="auth/login.php" class="btn btn-primary">Login</a>
                <a href="auth/register.php" class="btn btn-outline">Register</a>
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <h2 class="section-title">Key Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-file-invoice"></i>
                <h3>Invoice Management</h3>
                <p>Create, manage, and track invoices with ease</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Client Management</h3>
                <p>Organize and maintain client information efficiently</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Payment Tracking</h3>
                <p>Monitor payments and generate detailed reports</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Platform</h3>
                <p>Enterprise-grade security for your sensitive data</p>
            </div>
        </div>
    </div>
</div>

<div class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of businesses that trust our billing solution</p>
            <a href="auth/register.php" class="btn btn-primary">Start Free Trial</a>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?> 