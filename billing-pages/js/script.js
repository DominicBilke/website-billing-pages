// Document Ready
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Form validation
    $('form').on('submit', function(e) {
        var requiredFields = $(this).find('[required]');
        var isValid = true;

        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });

    // Numeric input validation
    $('input[type="number"]').on('input', function() {
        var value = $(this).val();
        if (value < 0) {
            $(this).val(0);
        }
    });

    // Date input validation
    $('input[type="date"]').on('change', function() {
        var selectedDate = new Date($(this).val());
        var today = new Date();
        
        if (selectedDate > today) {
            alert('Date cannot be in the future.');
            $(this).val('');
        }
    });

    // Calculate total on input change
    $('.calculate-total').on('input', function() {
        var hours = parseFloat($('#hours').val()) || 0;
        var rate = parseFloat($('#rate').val()) || 0;
        var total = hours * rate;
        $('#total').val(total.toFixed(2));
    });

    // Confirm delete
    $('.delete-confirm').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this entry?')) {
            e.preventDefault();
        }
    });

    // Export to PDF
    $('.export-pdf').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        window.open(url, '_blank');
    });

    // Search functionality
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val().trim();
        if (searchTerm) {
            window.location.href = $(this).attr('action') + '?search=' + encodeURIComponent(searchTerm);
        }
    });

    // Clear search
    $('#clearSearch').on('click', function(e) {
        e.preventDefault();
        window.location.href = window.location.pathname;
    });

    // Toggle advanced search
    $('#toggleAdvancedSearch').on('click', function(e) {
        e.preventDefault();
        $('#advancedSearch').toggleClass('d-none');
    });

    // Date range picker
    if ($.fn.daterangepicker) {
        $('.date-range').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }

    // Data table initialization
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries to show",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
        });
    }

    // Chart initialization
    function initializeCharts() {
        // Bar chart
        if ($('#barChart').length) {
            var ctx = document.getElementById('barChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Line chart
        if ($('#lineChart').length) {
            var ctx = document.getElementById('lineChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: lineChartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Pie chart
        if ($('#pieChart').length) {
            var ctx = document.getElementById('pieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: pieChartData,
                options: {
                    responsive: true
                }
            });
        }
    }

    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }
}); 