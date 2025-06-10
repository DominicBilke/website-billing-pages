<?php
$this->setPageTitle('Maintenance Mode');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-tools text-primary" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="mb-4">We'll be back soon!</h2>
                    
                    <p class="text-muted mb-4">
                        We're currently performing scheduled maintenance to improve our services. 
                        Please check back later.
                    </p>
                    
                    <?php if (isset($estimated_time)): ?>
                        <p class="text-muted mb-4">
                            Estimated completion time: 
                            <strong><?php echo date('F j, Y g:i A', strtotime($estimated_time)); ?></strong>
                        </p>
                    <?php endif; ?>
                    
                    <div class="progress mb-4" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: 75%" 
                             aria-valuenow="75" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Check Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Information -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4">Need Help?</h5>
                    
                    <p class="text-muted mb-4">
                        If you need immediate assistance, please contact our support team:
                    </p>
                    
                    <div class="d-flex justify-content-center gap-4">
                        <a href="mailto:support@example.com" class="text-decoration-none">
                            <i class="fas fa-envelope text-primary"></i>
                            <span class="ms-2">support@example.com</span>
                        </a>
                        
                        <a href="tel:+1234567890" class="text-decoration-none">
                            <i class="fas fa-phone text-primary"></i>
                            <span class="ms-2">+1 (234) 567-890</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto refresh after 5 minutes
setTimeout(function() {
    window.location.reload();
}, 300000);
</script> 