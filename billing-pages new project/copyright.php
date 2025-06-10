<?php
require_once 'script/config.php';
require_once 'script/auth.php';
require_once 'script/template.php';

// Set page title
$template->setPageTitle('Copyright');

// Add breadcrumbs
$template->addBreadcrumb('Copyright');

// Start output buffering
ob_start();
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4">Copyright Notice</h1>

                    <h2 class="h5 mb-3">Copyright © 2024 Billing System Portal</h2>
                    <p>
                        All rights reserved. This website and its contents are protected by copyright law. The content and works created by the site operators on these pages are subject to German copyright law.
                    </p>

                    <h2 class="h5 mb-3">Protected Content</h2>
                    <p>
                        The following elements are protected by copyright:
                    </p>
                    <ul>
                        <li>All text content</li>
                        <li>Images and graphics</li>
                        <li>Logos and trademarks</li>
                        <li>Software and code</li>
                        <li>Layout and design</li>
                    </ul>

                    <h2 class="h5 mb-3">Usage Rights</h2>
                    <p>
                        The following uses are permitted:
                    </p>
                    <ul>
                        <li>Private, non-commercial use</li>
                        <li>Viewing and printing for personal use</li>
                        <li>Creating links to our website</li>
                    </ul>

                    <h2 class="h5 mb-3">Restricted Uses</h2>
                    <p>
                        The following uses require written permission:
                    </p>
                    <ul>
                        <li>Commercial use of any kind</li>
                        <li>Modification or adaptation of content</li>
                        <li>Distribution or publication of content</li>
                        <li>Use in other websites or applications</li>
                    </ul>

                    <h2 class="h5 mb-3">Third-Party Content</h2>
                    <p>
                        Some content on this website may be subject to third-party copyrights. Such content is marked accordingly. The copyrights of third parties are respected. If you notice any copyright violations, please inform us.
                    </p>

                    <h2 class="h5 mb-3">Trademarks</h2>
                    <p>
                        All trademarks, service marks, and logos displayed on this website are the property of their respective owners. Use of these marks without permission is prohibited.
                    </p>

                    <h2 class="h5 mb-3">Contact</h2>
                    <p>
                        For copyright-related inquiries, please contact:<br>
                        Email: copyright@example.com<br>
                        Phone: +49 (0) 123 456789
                    </p>

                    <h2 class="h5 mb-3">Legal Notice</h2>
                    <p>
                        This copyright notice is subject to change without notice. The current version is always available on this page.
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