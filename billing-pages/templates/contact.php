<?php
$this->setPageTitle('Contact Us');
$this->addBreadcrumb('Contact', '/contact.php');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Contact Us</h1>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Contact Form -->
                        <div class="col-md-7">
                            <form action="/contact.php" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter your name.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="subject" 
                                           name="subject" 
                                           value="<?php echo htmlspecialchars($subject ?? ''); ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter a subject.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" 
                                              id="message" 
                                              name="message" 
                                              rows="5" 
                                              required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                    <div class="invalid-feedback">
                                        Please enter your message.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="privacy" 
                                               name="privacy" 
                                               required>
                                        <label class="form-check-label" for="privacy">
                                            I agree to the <a href="/privacy.php">privacy policy</a>
                                        </label>
                                        <div class="invalid-feedback">
                                            You must agree to the privacy policy.
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </form>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-5">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Contact Information</h5>

                                    <div class="mb-4">
                                        <h6 class="mb-2">Address</h6>
                                        <p class="mb-0">
                                            Example Street 123<br>
                                            12345 Example City<br>
                                            Germany
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="mb-2">Phone</h6>
                                        <p class="mb-0">
                                            <a href="tel:+49123456789" class="text-decoration-none">
                                                +49 123 456789
                                            </a>
                                        </p>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="mb-2">Email</h6>
                                        <p class="mb-0">
                                            <a href="mailto:info@example.com" class="text-decoration-none">
                                                info@example.com
                                            </a>
                                        </p>
                                    </div>

                                    <div>
                                        <h6 class="mb-2">Business Hours</h6>
                                        <p class="mb-0">
                                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                                            Saturday: 10:00 AM - 2:00 PM<br>
                                            Sunday: Closed
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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