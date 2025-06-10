<?php
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

class InvoicePDF extends TCPDF {
    private $invoice;
    private $invoice_items;
    private $user;
    private $client;

    public function __construct($invoice, $invoice_items, $client, $settings) {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->invoice = $invoice;
	$this->invoice_items = $invoice_items;
        $this->client = $client;
        $this->user = $settings;


        // Set document information
        $this->SetCreator('Billing Portal');
        $this->SetAuthor('Billing Portal');
        $this->SetTitle('Invoice #' . $invoice['id']);

        // Set default header data
        $this->SetHeaderData('', 0, 'INVOICE', 'Invoice ' . $invoice['invoice_number']);

        // Set margins
        $this->SetMargins(15, 15, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);

        // Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 15);

        // Set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set font
        $this->SetFont('helvetica', '', 10);
    }

    public function generate() {
        // Add a page
        $this->AddPage();

        // Company Information
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, $this->user['company_name'], 0, 1, 'R');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, $this->user['company_address'], 0, 1, 'R');
        $this->Cell(0, 5, $this->user['company_postal'].' '.$this->user['company_city'], 0, 1, 'R');
        $this->Cell(0, 5, $this->user['company_email'], 0, 1, 'R');
        $this->Ln(10);

        // Client Information
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Bill To:', 0, 1);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, $this->client['name'], 0, 1);
        $this->MultiCell(0, 5, $this->client['address'], 0, 'L');
        $this->Cell(0, 5, $this->client['email'], 0, 1);
        $this->Cell(0, 5, $this->client['phone'], 0, 1);
        $this->Ln(10);

        // Invoice Details
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Invoice Details', 0, 1);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(40, 5, 'Invoice Date:', 0, 0);
        $this->Cell(0, 5, date('F j, Y', strtotime($this->invoice['date'])), 0, 1);
        $this->Cell(40, 5, 'Due Date:', 0, 0);
        $this->Cell(0, 5, date('F j, Y', strtotime($this->invoice['due_date'])), 0, 1);
        $this->Ln(10);

        // Invoice Items Table Header
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(120, 7, 'Description', 1, 0, 'C');
        $this->Cell(30, 7, 'Amount', 1, 1, 'C');

        // Invoice Total
foreach($this->invoice_items as $item) {
        $this->SetFont('helvetica', '', 10);
        $this->Cell(120, 7, $item['description']. '  |  '.$item['quantity'].' x '.number_format($item['unit_price'], 2).' '.$this->user['currency'].'  |  '.$this->user['tax_rate'].' % VAT', 1, 0, 'L');
        $this->Cell(30, 7, number_format($item['amount'], 2).' '.$this->user['currency'], 1, 1, 'C');
}
        // Invoice Total
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(120, 7, 'Total', 1, 0, 'R');
        $this->Cell(30, 7, number_format($this->invoice['total_amount'], 2).' '.$this->user['currency'], 1, 1, 'C');

        // Footer
        $this->Ln(20);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 5, 'Thank you for your business!', 0, 1, 'C');
        $this->Cell(0, 5, 'This is a computer-generated invoice, no signature required.', 0, 1, 'C');
    }
} 