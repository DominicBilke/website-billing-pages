<?php
$this->setPageTitle('Page Not Found');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="mb-4">404 - Page Not Found</h2>
                    
                    <p class="text-muted mb-4">
                        The page you are looking for might have been removed, 
                        had its name changed, or is temporarily unavailable.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="/" class="btn btn-primary">
                            <i class="fas fa-home"></i> Return to Dashboard
                        </a>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">Search the Site</h5>
                    
                    <form action="/search.php" method="GET" class="needs-validation" novalidate>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   placeholder="Enter your search terms..." 
                                   required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();
</script> 