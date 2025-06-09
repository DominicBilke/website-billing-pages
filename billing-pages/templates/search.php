<?php
$this->setPageTitle('Search Results');
$this->addBreadcrumb('Search', '/search.php');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Search Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="/search.php" method="GET" class="needs-validation" novalidate>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   value="<?php echo htmlspecialchars($query ?? ''); ?>" 
                                   placeholder="Enter your search terms..." 
                                   required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-4">Search Results</h1>

                    <?php if (empty($query)): ?>
                        <p class="text-muted">Please enter a search term.</p>
                    <?php elseif (empty($results)): ?>
                        <p class="text-muted">No results found for "<?php echo htmlspecialchars($query); ?>".</p>
                    <?php else: ?>
                        <p class="text-muted mb-4">
                            Found <?php echo count($results); ?> results for "<?php echo htmlspecialchars($query); ?>"
                        </p>

                        <?php foreach ($results as $result): ?>
                            <div class="search-result mb-4">
                                <h2 class="h5 mb-2">
                                    <a href="<?php echo htmlspecialchars($result['url']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($result['title']); ?>
                                    </a>
                                </h2>
                                
                                <p class="text-muted small mb-2">
                                    <?php echo htmlspecialchars($result['type']); ?> • 
                                    Last updated: <?php echo date('M d, Y', strtotime($result['updated_at'])); ?>
                                </p>
                                
                                <p class="mb-2">
                                    <?php echo htmlspecialchars($result['excerpt']); ?>
                                </p>
                                
                                <a href="<?php echo htmlspecialchars($result['url']); ?>" class="btn btn-sm btn-outline-primary">
                                    Read More
                                </a>
                            </div>

                            <?php if (!$loop->last): ?>
                                <hr>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Search results pages" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>">
                                            Previous
                                        </a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?q=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?q=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
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