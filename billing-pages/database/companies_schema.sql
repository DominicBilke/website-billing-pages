-- Companies Database Schema for Billing Pages
-- Database: billing_pages_companies

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Companies table - stores company information
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT 'Germany',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  `account_holder` varchar(255) DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `vat_number` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employees table - stores employee information
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_number` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `tax_class` int(11) DEFAULT 1,
  `social_security_number` varchar(50) DEFAULT NULL,
  `bank_account` varchar(34) DEFAULT NULL,
  `bank_code` varchar(11) DEFAULT NULL,
  `account_holder` varchar(255) DEFAULT NULL,
  `employment_start_date` date DEFAULT NULL,
  `employment_end_date` date DEFAULT NULL,
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_employee_number` (`company_id`, `employee_number`),
  KEY `company_id` (`company_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects table - stores project information
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_number` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('active','completed','cancelled','on_hold') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Work records table - stores employee work time records
CREATE TABLE `work_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `total_hours` decimal(5,2) DEFAULT NULL,
  `break_time` decimal(4,2) DEFAULT 0.00,
  `net_hours` decimal(5,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `work_type` enum('regular','overtime','weekend','holiday','sick','vacation') DEFAULT 'regular',
  `status` enum('draft','submitted','approved','rejected','paid') DEFAULT 'draft',
  `proof_file` varchar(255) DEFAULT NULL,
  `gpx_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `company_id` (`company_id`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Monthly summaries table - for monthly billing summaries
CREATE TABLE `monthly_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) NOT NULL,
  `total_hours` decimal(8,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `overtime_hours` decimal(8,2) DEFAULT 0.00,
  `overtime_amount` decimal(12,2) DEFAULT 0.00,
  `weekend_hours` decimal(8,2) DEFAULT 0.00,
  `weekend_amount` decimal(12,2) DEFAULT 0.00,
  `holiday_hours` decimal(8,2) DEFAULT 0.00,
  `holiday_amount` decimal(12,2) DEFAULT 0.00,
  `sick_hours` decimal(8,2) DEFAULT 0.00,
  `vacation_hours` decimal(8,2) DEFAULT 0.00,
  `status` enum('draft','calculated','billed','paid') DEFAULT 'draft',
  `billing_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_month_year` (`employee_id`, `year`, `month`),
  KEY `company_id` (`company_id`),
  KEY `employee_id` (`employee_id`),
  KEY `year_month` (`year`, `month`),
  KEY `status` (`status`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoices table - for billing invoices
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT 19.00,
  `tax_amount` decimal(12,2) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'EUR',
  `status` enum('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `company_id` (`company_id`),
  KEY `invoice_date` (`invoice_date`),
  KEY `status` (`status`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoice items table - for invoice line items
CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit` varchar(20) DEFAULT 'hours',
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `employee_id` (`employee_id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table - for company-specific settings
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_key` (`company_id`, `key`),
  KEY `company_id` (`company_id`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default company settings
INSERT INTO `settings` (`key`, `value`, `type`, `description`) VALUES
('default_hourly_rate', '15.00', 'string', 'Default hourly rate for employees'),
('default_tax_rate', '19.00', 'string', 'Default tax rate for invoices'),
('invoice_prefix', 'INV', 'string', 'Prefix for invoice numbers'),
('invoice_start_number', '1000', 'string', 'Starting number for invoices'),
('auto_calculate_tax', 'true', 'boolean', 'Automatically calculate tax on invoices'),
('require_proof_files', 'true', 'boolean', 'Require proof files for work records'),
('gpx_tracking_enabled', 'false', 'boolean', 'Enable GPX tracking for work records'),
('overtime_threshold', '8', 'string', 'Hours per day before overtime applies'),
('weekend_rate_multiplier', '1.5', 'string', 'Rate multiplier for weekend work'),
('holiday_rate_multiplier', '2.0', 'string', 'Rate multiplier for holiday work');

COMMIT; 