<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';

// Set page title
$template->setPageTitle('Privacy Policy');

// Add breadcrumbs
$template->addBreadcrumb('Privacy Policy');

// Start output buffering
ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4">Privacy Policy</h1>

                    <h2 class="h5 mb-3">1. Data Protection at a Glance</h2>
                    <h3 class="h6 mb-2">General Notes</h3>
                    <p>
                        The following notes provide a simple overview of what happens to your personal data when you visit this website. Personal data is any data that can personally identify you. Detailed information on the subject of data protection can be found in our privacy policy listed below this text.
                    </p>

                    <h3 class="h6 mb-2">Data Collection on this Website</h3>
                    <p>
                        <strong>Who is responsible for data collection on this website?</strong><br>
                        Data processing on this website is carried out by the website operator. You can find their contact details in the imprint of this website.
                    </p>

                    <h2 class="h5 mb-3">2. General Notes and Mandatory Information</h2>
                    <h3 class="h6 mb-2">Data Protection</h3>
                    <p>
                        The operators of these pages take the protection of your personal data very seriously. We treat your personal data confidentially and in accordance with the statutory data protection regulations and this privacy policy.
                    </p>

                    <h3 class="h6 mb-2">Note on the Responsible Party</h3>
                    <p>
                        The responsible party for data processing on this website is:<br>
                        Billing System Portal<br>
                        Example Street 123<br>
                        12345 Example City<br>
                        Germany
                    </p>

                    <h2 class="h5 mb-3">3. Data Collection on our Website</h2>
                    <h3 class="h6 mb-2">Cookies</h3>
                    <p>
                        Our website uses cookies. These are small text files that your web browser stores on your device. Cookies help us make our offer more user-friendly, effective, and secure.
                    </p>

                    <h3 class="h6 mb-2">Server Log Files</h3>
                    <p>
                        The provider of the pages automatically collects and stores information in so-called server log files, which your browser automatically transmits to us. These are:
                    </p>
                    <ul>
                        <li>Browser type and version</li>
                        <li>Operating system used</li>
                        <li>Referrer URL</li>
                        <li>Host name of the accessing computer</li>
                        <li>Time of the server request</li>
                        <li>IP address</li>
                    </ul>

                    <h2 class="h5 mb-3">4. Your Rights</h2>
                    <p>
                        You have the right to:
                    </p>
                    <ul>
                        <li>Request information about your stored personal data</li>
                        <li>Request correction of incorrect personal data</li>
                        <li>Request deletion of your personal data</li>
                        <li>Request restriction of processing of your personal data</li>
                        <li>Object to processing of your personal data</li>
                        <li>Request data portability</li>
                        <li>Withdraw consent at any time</li>
                    </ul>

                    <h2 class="h5 mb-3">5. Data Security</h2>
                    <p>
                        This website uses SSL or TLS encryption for security reasons and to protect the transmission of confidential content. You can recognize an encrypted connection by the "https://" address line of your browser and the lock symbol in your browser line.
                    </p>

                    <h2 class="h5 mb-3">6. Changes to this Privacy Policy</h2>
                    <p>
                        We reserve the right to update this privacy policy to comply with current legal requirements or to implement changes to our services in the privacy policy. The new privacy policy will then apply to your next visit.
                    </p>

                    <h2 class="h5 mb-3">7. Contact</h2>
                    <p>
                        For questions about data protection, please contact us at:<br>
                        Email: privacy@example.com<br>
                        Phone: +49 (0) 123 456789
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the content and render the template
$content = ob_get_clean();
$template->setContent($content);
echo $template->render(); 