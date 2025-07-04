/**
 * Billing Pages - Main JavaScript File
 * 
 * This file contains all the JavaScript functionality for the billing pages application.
 */

// Global variables
let currentMap = null;
let fileUploads = {};

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize the application
 */
function initializeApp() {
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize file uploads
    initializeFileUploads();
    
    // Initialize maps
    initializeMaps();
    
    // Initialize charts
    initializeCharts();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize data tables
    initializeDataTables();
    
    // Initialize modals
    initializeModals();
    
    // Initialize notifications
    initializeNotifications();
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize file upload functionality
 */
function initializeFileUploads() {
    const fileUploadAreas = document.querySelectorAll('.file-upload');
    
    fileUploadAreas.forEach(area => {
        const input = area.querySelector('input[type="file"]');
        const preview = area.querySelector('.file-preview');
        const dropZone = area.querySelector('.drop-zone');
        
        if (input) {
            input.addEventListener('change', handleFileSelect);
        }
        
        if (dropZone) {
            dropZone.addEventListener('dragover', handleDragOver);
            dropZone.addEventListener('drop', handleDrop);
            dropZone.addEventListener('dragleave', handleDragLeave);
        }
    });
}

/**
 * Handle file selection
 */
function handleFileSelect(event) {
    const files = event.target.files;
    const preview = event.target.closest('.file-upload').querySelector('.file-preview');
    
    if (files.length > 0) {
        displayFilePreview(files[0], preview);
    }
}

/**
 * Handle drag over
 */
function handleDragOver(event) {
    event.preventDefault();
    event.currentTarget.classList.add('dragover');
}

/**
 * Handle drop
 */
function handleDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('dragover');
    
    const files = event.dataTransfer.files;
    const input = event.currentTarget.querySelector('input[type="file"]');
    const preview = event.currentTarget.querySelector('.file-preview');
    
    if (files.length > 0) {
        input.files = files;
        displayFilePreview(files[0], preview);
    }
}

/**
 * Handle drag leave
 */
function handleDragLeave(event) {
    event.currentTarget.classList.remove('dragover');
}

/**
 * Display file preview
 */
function displayFilePreview(file, previewElement) {
    if (!previewElement) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        if (file.type.startsWith('image/')) {
            previewElement.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">
                <p class="mt-2 mb-0"><strong>${file.name}</strong> (${formatFileSize(file.size)})</p>
            `;
        } else {
            previewElement.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-file me-2"></i>
                    <strong>${file.name}</strong> (${formatFileSize(file.size)})
                </div>
            `;
        }
    };
    reader.readAsDataURL(file);
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Initialize maps
 */
function initializeMaps() {
    const mapContainers = document.querySelectorAll('.map-container');
    
    mapContainers.forEach(container => {
        const mapId = container.id;
        if (mapId && !currentMap) {
            currentMap = L.map(mapId).setView([52.5200, 13.4050], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(currentMap);
        }
    });
}

/**
 * Initialize charts
 */
function initializeCharts() {
    const chartContainers = document.querySelectorAll('[data-chart]');
    
    chartContainers.forEach(container => {
        const chartType = container.dataset.chart;
        const chartData = JSON.parse(container.dataset.chartData || '{}');
        
        if (chartType === 'bar') {
            createBarChart(container, chartData);
        } else if (chartType === 'line') {
            createLineChart(container, chartData);
        } else if (chartType === 'pie') {
            createPieChart(container, chartData);
        }
    });
}

/**
 * Create bar chart
 */
function createBarChart(container, data) {
    // Implementation for bar chart
    console.log('Creating bar chart:', data);
}

/**
 * Create line chart
 */
function createLineChart(container, data) {
    // Implementation for line chart
    console.log('Creating line chart:', data);
}

/**
 * Create pie chart
 */
function createPieChart(container, data) {
    // Implementation for pie chart
    console.log('Creating pie chart:', data);
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

/**
 * Initialize data tables
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.addEventListener('click', function() {
                sortTable(table, this.dataset.sort);
            });
        });
    });
}

/**
 * Sort table
 */
function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isAscending = table.dataset.sortDirection !== 'asc';
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`td[data-${column}]`).textContent;
        const bValue = b.querySelector(`td[data-${column}]`).textContent;
        
        if (isAscending) {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
    table.dataset.sortDirection = isAscending ? 'asc' : 'desc';
}

/**
 * Initialize modals
 */
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        const modalInstance = new bootstrap.Modal(modal);
        
        // Handle modal events
        modal.addEventListener('shown.bs.modal', function() {
            // Initialize any components inside the modal
            const mapContainer = modal.querySelector('.map-container');
            if (mapContainer && !mapContainer.dataset.initialized) {
                initializeMapInModal(mapContainer);
                mapContainer.dataset.initialized = 'true';
            }
        });
    });
}

/**
 * Initialize map in modal
 */
function initializeMapInModal(container) {
    const mapId = container.id;
    if (mapId) {
        const map = L.map(mapId).setView([52.5200, 13.4050], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Store map instance
        fileUploads[mapId] = map;
    }
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info', duration = 5000) {
    const alertContainer = document.getElementById('alert-container') || document.body;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alert);
    
    if (duration > 0) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, duration);
    }
}

/**
 * AJAX request helper
 */
function ajaxRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = { ...defaultOptions, ...options };
    
    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('AJAX request failed:', error);
            showNotification('An error occurred while processing your request.', 'danger');
            throw error;
        });
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = 'EUR') {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(date, format = 'dd.mm.yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year);
}

/**
 * Format time
 */
function formatTime(time, format = 'hh:mm') {
    const [hours, minutes] = time.split(':');
    return format
        .replace('hh', hours)
        .replace('mm', minutes);
}

/**
 * Calculate time difference
 */
function calculateTimeDifference(startTime, endTime) {
    const start = new Date(`2000-01-01T${startTime}`);
    const end = new Date(`2000-01-01T${endTime}`);
    const diff = end - start;
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            rowData.push(`"${col.textContent.trim()}"`);
        });
        
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

/**
 * Print element
 */
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Print</title>
                <link href="/assets/css/style.css" rel="stylesheet">
            </head>
            <body>
                ${element.outerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Export functions for global use
window.BillingPages = {
    showNotification,
    ajaxRequest,
    formatCurrency,
    formatDate,
    formatTime,
    calculateTimeDifference,
    exportTableToCSV,
    printElement
}; 