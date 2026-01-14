<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Dayend extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_invoices_model');
        $this->load->library('pagination');
        $this->load->database();
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
    }

    public function index()
    {
        // Get filter parameters
        $payment_method = $this->input->get('payment_method');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $warehouse = $this->input->get('warehouse_id');

        // Set default dates if not provided
        if (!$start_date) {
            $start_date = date('Y-m-d');
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }

        // Pagination setup
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $offset = ($page - 1) * $limit;

        // Get total count for pagination
        $total_records = $this->Pos_invoices_model->count_day_end_report($payment_method, $start_date, $end_date, $warehouse);

        // Get paginated data
        $data['report_data'] = $this->Pos_invoices_model->get_day_end_report($payment_method, $start_date, $end_date, $warehouse, $limit, $offset);
        
        // Load payment methods dynamically from database
        $this->load->model('Paymentmethods_model', 'paymentmethods');
        $payment_methods_data = $this->paymentmethods->get_with_balance();
        $data['payment_methods'] = array_column($payment_methods_data, 'name');
        
        // Debug: Check what payment methods we're getting
        error_log("Payment methods loaded: " . json_encode($data['payment_methods']));
        
        $data['selected_payment_method'] = $payment_method;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['warehouses'] = $this->Pos_invoices_model->warehouses(true);
        $data['selected_warehouse'] = $warehouse;

        // Pagination data
        $data['total_records'] = $total_records;
        $data['limit'] = $limit;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_records / $limit);

        // Calculate totals dynamically based on payment methods
        $data['totals'] = array('grand_total' => 0);
        
        // Initialize totals for each payment method
        foreach ($payment_methods_data as $pm) {
            $key = strtolower($pm['name']);
            $data['totals'][$key] = 0;
        }

        // Get all data for totals calculation (without pagination)
        $all_data = $this->Pos_invoices_model->get_day_end_report($payment_method, $start_date, $end_date, $warehouse);

        foreach ($all_data as $row) {
            $pmethod = strtolower($row['pmethod']);
            if (isset($data['totals'][$pmethod])) {
                $data['totals'][$pmethod] += $row['total'];
            }
            $data['totals']['grand_total'] += $row['total'];
        }

        $head['title'] = "Day End Report";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('pos/day_end_report', $data);
        $this->load->view('fixed/footer');
    }
}