<?php
$this->setPageTitle('Error');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="mb-4">Oops! Something went wrong</h2>
                    
                    <p class="text-muted mb-4">
                        <?php if ($config['debug']): ?>
                            <?php echo nl2br(htmlspecialchars($error)); ?>
                        <?php else: ?>
                            An error occurred while processing your request. 
                            Please try again later or contact support if the problem persists.
                        <?php endif; ?>
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

<?php if ($config['debug'] && isset($trace)): ?>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Stack Trace</h5>
            </div>
            <div class="card-body">
                <pre class="mb-0"><code><?php echo htmlspecialchars($trace); ?></code></pre>
            </div>
        </div>
    </div>
<?php endif; ?> 