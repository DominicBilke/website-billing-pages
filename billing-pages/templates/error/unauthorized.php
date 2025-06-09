<?php
$this->setPageTitle('Unauthorized Access');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="mb-4">Access Denied</h2>
                    
                    <p class="text-muted mb-4">
                        You do not have permission to access this page. 
                        Please contact your administrator if you believe this is an error.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="/" class="btn btn-primary">
                            <i class="fas fa-home"></i> Return to Dashboard
                        </a>
                        
                        <a href="/login.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-in-alt"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 