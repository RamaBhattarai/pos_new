<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('supplier_model', 'supplier');
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(2)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->li_a = 'stock';
    }

    public function index()
    {

        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/clist');
        $this->load->view('fixed/footer');
    }

    public function create()
    {
        $data['customergrouplist'] = $this->supplier->group_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Create Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/create', $data);
        $this->load->view('fixed/footer');
    }

    public function view()
    {
        $custid = $this->input->get('id');
        $data['details'] = $this->supplier->details($custid);
        $data['customergroup'] = $this->supplier->group_info($data['details']['gid']);
        $data['money'] = $this->supplier->money_details($custid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier';
        $this->load->view('fixed/header', $head);
        if ($data['details']['id']) $this->load->view('supplier/view', $data);
        $this->load->view('fixed/footer');
    }

    public function load_list()
    {
        $list = $this->supplier->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;

            $row = array();
            $row[] = $no;
            $row[] = '<a href="supplier/view?id=' . $customers->id . '">' . $customers->name . '</a>';
            $row[] = $customers->address . ',' . $customers->city . ',' . $customers->country;
            $row[] = $customers->email;
            $row[] = $customers->phone;
            $row[] = '<a href="supplier/view?id=' . $customers->id . '" class="btn btn-info btn-sm"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> <a href="supplier/edit?id=' . $customers->id . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a> <a href="#" data-object-id="' . $customers->id . '" class="btn btn-danger btn-sm delete-object"><span class="fa fa-trash"></span></a>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier->count_all(),
            "recordsFiltered" => $this->supplier->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    //edit section
    public function edit()
    {
        $pid = $this->input->get('id');

        $data['customer'] = $this->supplier->details($pid);
        $data['customergroup'] = $this->supplier->group_info($pid);
        $data['customergrouplist'] = $this->supplier->group_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Edit Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/edit', $data);
        $this->load->view('fixed/footer');

    }

    public function addsupplier()
    {
        $name = $this->input->post('name', true);
        $company = $this->input->post('company', true);
        $phone = $this->input->post('phone', true);
        $email = $this->input->post('email', true);
        $address = $this->input->post('address', true);
        $city = $this->input->post('city', true);
        $region = $this->input->post('region', true);
        $country = $this->input->post('country', true);
        $postbox = $this->input->post('postbox', true);
        $taxid = $this->input->post('taxid', true);

        // Get party confirmation fields 
        $confirmation_threshold = $this->input->post('confirmation_threshold', true);
        $confirmation_method = $this->input->post('confirmation_method', true);
        $confirmation_contact = $this->input->post('confirmation_contact', true);

        $this->supplier->add($name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $taxid, $confirmation_threshold, $confirmation_method, $confirmation_contact);

    }

    public function editsupplier()
    {
        $id = $this->input->post('id', true);
        $name = $this->input->post('name', true);
        $company = $this->input->post('company', true);
        $phone = $this->input->post('phone', true);
        $email = $this->input->post('email', true);
        $address = $this->input->post('address', true);
        $city = $this->input->post('city', true);
        $region = $this->input->post('region', true);
        $country = $this->input->post('country', true);
        $postbox = $this->input->post('postbox', true);
        $taxid = $this->input->post('taxid', true);

        // Get party confirmation fields (optional - backward compatible)
        $confirmation_threshold = $this->input->post('confirmation_threshold', true);
        $confirmation_method = $this->input->post('confirmation_method', true);
        $confirmation_contact = $this->input->post('confirmation_contact', true);

        if ($id) {
            $this->supplier->edit($id, $name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $taxid, $confirmation_threshold, $confirmation_method, $confirmation_contact);
        }
    }


    public function delete_i()
    {
        $id = $this->input->post('deleteid');

        if ($this->supplier->delete($id)) {
            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
        }
    }

    public function displaypic()
    {
        $id = $this->input->get('id');
        $this->load->library("uploadhandler", array(
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i', 'upload_dir' => FCPATH . 'userfiles/customers/'
        ));
        $img = (string)$this->uploadhandler->filenaam();
        if ($img != '') {
            $this->supplier->editpicture($id, $img);
        }


    }


    public function translist()
    {
        $cid = $this->input->post('cid');
        $list = $this->supplier->trans_table($cid);
        $data = array();
        // $no = $_POST['start'];
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $pid = $prd->id;
            $row[] = $prd->date;
            $row[] = amountExchange($prd->debit, 0, $this->aauth->get_user()->loc);
            $row[] = amountExchange($prd->credit, 0, $this->aauth->get_user()->loc);
            $row[] = $prd->account;
            $row[] = $prd->payer;
            $row[] = $this->lang->line($prd->method);

            $row[] = '<a href="' . base_url() . 'transactions/view?id=' . $pid . '" class="btn btn-primary btn-xs"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-xs delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier->trans_count_all($cid),
            "recordsFiltered" => $this->supplier->trans_count_filtered($cid),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function inv_list()
    {
        $cid = $this->input->post('cid');
        $list = $this->supplier->inv_datatables($cid);
        $data = array();

        $no = $this->input->post('start');

        foreach ($list as $invoices) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $invoices->tid;

            $row[] = $invoices->invoicedate;
            $row[] = amountExchange($invoices->total, 0, $this->aauth->get_user()->loc);
            $row[] = '<span class="st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';
            $row[] = '<a href="' . base_url("purchase/view?id=$invoices->id") . '" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> ' . $this->lang->line('View') . '</a> &nbsp; <a href="' . base_url("purchase/printinvoice?id=$invoices->id") . '&d=1" class="btn btn-info btn-xs"  title="Download"><span class="fa fa-download"></span></a>&nbsp; &nbsp;<a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-xs delete-object"><span class="fa fa-trash"></span></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier->inv_count_all($cid),
            "recordsFiltered" => $this->supplier->inv_count_filtered($cid),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);

    }


    public function transactions()
    {
        $custid = $this->input->get('id');
        $data['details'] = $this->supplier->details($custid);
        $data['money'] = $this->supplier->money_details($custid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/transactions', $data);
        $this->load->view('fixed/footer');
    }

    public function invoices()
    {
        $custid = $this->input->get('id');
        $data['details'] = $this->supplier->details($custid);

        $data['money'] = $this->supplier->money_details($custid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier Invoices';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/invoices', $data);
        $this->load->view('fixed/footer');
    }

    public function bulkpayment()
    {
        if (!$this->aauth->premission(8)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $data['id'] = $this->input->get('id');
        $data['details'] = $this->supplier->details($data['id']);
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->model('accounts_model');
        $data['acclist'] = $this->accounts_model->accountslist((integer)$this->aauth->get_user()->loc);
        $this->session->set_userdata("cid", $data['id']);
        $head['title'] = 'Bulk Payment Invoices';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/bulkpayment', $data);
        $this->load->view('fixed/footer');
    }

    public function bulk_post()
    {
        if (!$this->aauth->premission(8)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $csd = $this->input->post('customer', true);
        $sdate = datefordatabase($this->input->post('sdate'));
        $edate = datefordatabase($this->input->post('edate'));
        $trans_type = $this->input->post('trans_type', true);
        $data['details'] = $this->supplier->sales_due($sdate, $edate, $csd, $trans_type);

        $due = $data['details']['total'] - $data['details']['pamnt'];
        echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Calculated') . ' ' . amountExchange($due), 'due' => amountExchange_s($due)));
    }

    public function bulk_post_payment()
    {
        if (!$this->aauth->premission(8)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $csd = $this->input->post('customer', true);
        $account = $this->input->post('account', true);
        $pay_method = $this->input->post('pmethod', true);
        $amount = numberClean($this->input->post('amount', true));
        $sdate = datefordatabase($this->input->post('sdate_2'));
        $edate = datefordatabase($this->input->post('edate_2'));

        $trans_type = $this->input->post('trans_type_2', true);
        $note = $this->input->post('note', true);
        $data['details'] = $this->supplier->sales_due($sdate, $edate, $csd, $trans_type, false, $amount, $account, $pay_method, $note);

        $due = 0;
        echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Paid') . ' ' . amountExchange($amount), 'due' => amountExchange_s($due)));
    }

    public function party_confirmation_alerts()
    {
        try {
            // Get suppliers needing confirmation based on purchase thresholds
            $alerts = $this->supplier->get_suppliers_needing_confirmation_alerts();
            
            header('Content-Type: application/json');
            echo json_encode($alerts);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // public function party_confirmation_alerts_test()
    // {
    //     // Simple test version that returns dummy data
    //     $test_data = [
    //         [
    //             'id' => 1,
    //             'name' => 'Test Supplier',
    //             'company' => 'Test Company Ltd',
    //             'confirmation_threshold' => 5000,
    //             'confirmation_method' => 'both',
    //             'confirmation_contact' => 'test@example.com',
    //             'total_purchases' => 7500,
    //             'period_start' => date('Y-m-01'),
    //             'period_end' => date('Y-m-d')
    //         ]
    //     ];
        
    //     header('Content-Type: application/json');
    //     echo json_encode($test_data);
    // }

    public function debug_confirmation()
    {
        // Simple debug method - no permission check for testing
        echo "<style>body{font-family:Arial;margin:20px;} h3{color:#007bff;} .alert{background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin:10px 0;} .success{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin:10px 0;}</style>";
        echo "<h2>🔧 Party Confirmation Debug</h2>";
        
        // Check if confirmation fields exist
        echo "<h3>1. Database Structure Check</h3>";
        $fields = $this->db->list_fields('pos_supplier');
        $has_threshold = in_array('confirmation_threshold', $fields);
        
        if ($has_threshold) {
            echo "<div class='success'>✅ confirmation_threshold field exists</div>";
        } else {
            echo "<div class='alert'>❌ confirmation_threshold field missing</div>";
        }
        
        // Check supplier data with thresholds
        echo "<h3>2. Suppliers with Thresholds</h3>";
        $suppliers = $this->db->select('id, name, confirmation_threshold')
                              ->from('pos_supplier')
                              ->where('confirmation_threshold >', 0)
                              ->get()->result_array();
        
        if (empty($suppliers)) {
            echo "<div class='alert'>❌ No suppliers have confirmation thresholds set</div>";
        } else {
            echo "<div class='success'>✅ Found " . count($suppliers) . " suppliers with thresholds:</div>";
            foreach ($suppliers as $supplier) {
                echo "• " . $supplier['name'] . " - Threshold: ₹" . number_format($supplier['confirmation_threshold']) . "<br>";
            }
        }
        
        // Check recent purchases
        echo "<h3>3. Recent Purchase Analysis</h3>";
        if (!empty($suppliers)) {
            foreach ($suppliers as $supplier) {
                // Get total purchases for this supplier
                $this->db->select('COALESCE(SUM(total), 0) as total_purchases, COUNT(*) as order_count');
                $this->db->from('pos_purchase_entries');
                $this->db->where('csd', $supplier['id']);
                $this->db->where('status !=', 'canceled');
                $purchase_data = $this->db->get()->row_array();
                
                $threshold_met = $purchase_data['total_purchases'] >= $supplier['confirmation_threshold'];
                $status_color = $threshold_met ? 'red' : 'green';
                $status_text = $threshold_met ? '🚨 EXCEEDS THRESHOLD' : '✅ Below threshold';
                
                echo "<div style='border:1px solid #ddd;padding:10px;margin:5px 0;'>";
                echo "<strong>" . $supplier['name'] . "</strong><br>";
                echo "Total Purchases: ₹" . number_format($purchase_data['total_purchases']) . " (" . $purchase_data['order_count'] . " orders)<br>";
                echo "Threshold: ₹" . number_format($supplier['confirmation_threshold']) . "<br>";
                echo "<span style='color:$status_color;font-weight:bold;'>$status_text</span>";
                echo "</div>";
            }
        }
        
        // Test API
        echo "<h3>4. API Test</h3>";
        try {
            $alerts = $this->supplier->get_suppliers_needing_confirmation_alerts();
            if (empty($alerts)) {
                echo "<div class='alert'>❌ API returned no alerts</div>";
            } else {
                echo "<div class='success'>✅ API returned " . count($alerts) . " alerts</div>";
                foreach ($alerts as $alert) {
                    echo "• " . $alert['name'] . " - ₹" . number_format($alert['total_purchases']) . "<br>";
                }
            }
        } catch (Exception $e) {
            echo "<div class='alert'>❌ API Error: " . $e->getMessage() . "</div>";
        }
        
        echo "<h3>5. Quick Actions</h3>";
        echo "<p><a href='" . base_url('supplier') . "' style='background:#007bff;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;'>→ Manage Suppliers</a></p>";
        echo "<p><a href='" . base_url('supplier/party_confirmation_alerts') . "' style='background:#28a745;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;' target='_blank'>→ Test Alerts API (JSON)</a></p>";
    }

    public function party_confirmations()
    {
        if (!$this->aauth->premission(2)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        
        try {
            $head['usernm'] = $this->aauth->get_user()->username;
            $head['title'] = 'Party Confirmations';
            
            // Initialize empty data to avoid errors
            $data['confirmations'] = array();
            $data['suppliers_list'] = array();
            
            // Try to get real data, but don't fail if it doesn't work
            try {
                if (method_exists($this->supplier, 'get_all_party_confirmations')) {
                    $confirmations = $this->supplier->get_all_party_confirmations();
                    if (is_array($confirmations)) {
                        $data['confirmations'] = $confirmations;
                    }
                }
                if (method_exists($this->supplier, 'supplier_list')) {
                    $suppliers = $this->supplier->supplier_list();
                    if (is_array($suppliers)) {
                        $data['suppliers_list'] = $suppliers;
                    }
                }
            } catch (Exception $e) {
                // Log error but continue with empty data
                log_message('error', 'Error loading party confirmation data: ' . $e->getMessage());
            }
            
            // Load views
            $this->load->view('fixed/header', $head);
            $this->load->view('supplier/party_confirmations', $data);
            $this->load->view('fixed/footer');
            
        } catch (Exception $e) {
            // Fallback error display
            echo '<h1>Party Confirmations</h1>';
            echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;">';
            echo '<h4>System Error</h4>';
            echo '<p>Unable to load the party confirmations page. Please ensure the database tables are set up correctly.</p>';
            echo '<p><a href="' . base_url('supplier') . '" class="btn btn-primary">Back to Suppliers</a></p>';
            echo '<p><a href="' . base_url('quick_db_setup.php') . '" class="btn btn-info">Run Database Setup</a></p>';
            echo '</div>';
        }
    }

    public function generate_confirmation_letter()
    {
        if (!$this->aauth->premission(2)) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient permissions']);
            return;
        }
        
        try {
            $supplier_id = $this->input->post('supplier_id', true);
            $amount = $this->input->post('amount', true);
            $period_start = $this->input->post('period_start', true);
            $period_end = $this->input->post('period_end', true);
            
            $result = $this->supplier->generate_confirmation_letter($supplier_id, $amount, $period_start, $period_end);
            
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error generating letter: ' . $e->getMessage()]);
        }
    }

    public function mark_confirmation_sent()
    {
        if (!$this->aauth->premission(2)) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient permissions']);
            return;
        }
        
        try {
            $confirmation_id = $this->input->post('confirmation_id', true);
            $result = $this->supplier->mark_confirmation_sent_by_id($confirmation_id);
            
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    public function delete_confirmation()
    {
        if (!$this->aauth->premission(2)) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient permissions']);
            return;
        }
        
        try {
            $confirmation_id = $this->input->post('confirmation_id', true);
            
            if (empty($confirmation_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid confirmation ID']);
                return;
            }
            
            $result = $this->supplier->delete_confirmation_by_id($confirmation_id);
            
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error deleting confirmation: ' . $e->getMessage()]);
        }
    }

    public function get_supplier_details($supplier_id = null)
    {
        if (!$this->aauth->premission(2)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Insufficient permissions']);
            return;
        }
        
        try {
            // Get supplier ID from URL parameter or POST data
            if (!$supplier_id) {
                $supplier_id = $this->input->post('supplier_id', true);
            }
            
            if (!$supplier_id) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Supplier ID is required']);
                return;
            }
            
            $supplier_details = $this->supplier->details($supplier_id);
            
            if ($supplier_details) {
                header('Content-Type: application/json');
                echo json_encode($supplier_details);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Supplier not found']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error fetching supplier details: ' . $e->getMessage()]);
        }
    }

    public function check_existing_confirmation($supplier_id = null, $period_start = null, $period_end = null)
    {
        if (!$this->aauth->premission(2)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Insufficient permissions']);
            return;
        }
        
        try {
            // Get parameters from URL or POST data
            if (!$supplier_id) {
                $supplier_id = $this->input->post('supplier_id', true);
            }
            if (!$period_start) {
                $period_start = $this->input->post('period_start', true);
            }
            if (!$period_end) {
                $period_end = $this->input->post('period_end', true);
            }
            
            if (!$supplier_id || !$period_start || !$period_end) {
                header('Content-Type: application/json');
                echo json_encode(['exists' => false, 'error' => 'Missing parameters']);
                return;
            }
            
            $existing = $this->supplier->check_existing_confirmation($supplier_id, $period_start, $period_end);
            
            header('Content-Type: application/json');
            echo json_encode(['exists' => !empty($existing), 'data' => $existing]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['exists' => false, 'error' => 'Error checking confirmation: ' . $e->getMessage()]);
        }
    }

    public function send_confirmation_email()
    {
        if (!$this->aauth->premission(2)) {
            echo json_encode(['status' => 'Error', 'message' => 'Insufficient permissions']);
            return;
        }
        
        try {
            $mailtoc = $this->input->post('mailtoc');
            $customername = $this->input->post('customername');
            $subject = $this->input->post('subject');
            $message = $this->input->post('message');
            $supplier_id = $this->input->post('supplier_id');
            $amount = $this->input->post('amount');
            $period_start = $this->input->post('period_start');
            $period_end = $this->input->post('period_end');
            
            // Validate required fields
            if (empty($mailtoc) || empty($subject) || empty($message)) {
                echo json_encode(['status' => 'Error', 'message' => 'Please fill all required fields']);
                return;
            }
            
            // Validate email format
            if (!filter_var($mailtoc, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'Error', 'message' => 'Please enter a valid email address']);
                return;
            }
            
            // Load communication model
            $this->load->model('communication_model');
            
            // Check if SMTP settings exist
            $this->db->select('*');
            $this->db->from('pos_smtp');
            $smtp_query = $this->db->get();
            
            if ($smtp_query->num_rows() == 0) {
                echo json_encode(['status' => 'Error', 'message' => 'Email settings not configured. Please contact administrator.']);
                return;
            }
            
            $smtp_config = $smtp_query->row_array();
            if (empty($smtp_config['host']) || empty($smtp_config['username'])) {
                echo json_encode(['status' => 'Error', 'message' => 'Email settings incomplete. Please contact administrator.']);
                return;
            }
            
            // Send email - the ultimatemailer library will handle the JSON response
            $this->communication_model->send_email($mailtoc, $customername, $subject, $message, false, '');
            
            // Update confirmation with email info after successful send (if supplier_id provided)
            // Note: We can't check if email was successful here because ultimatemailer echoes response directly
            if ($supplier_id) {
                $this->db->where('supplier_id', $supplier_id);
                $this->db->where('confirmation_period_start', $period_start);
                $this->db->where('confirmation_period_end', $period_end);
                $this->db->update('pos_party_confirmations', [
                    'email_sent_date' => date('Y-m-d H:i:s'),
                    'email_recipient' => $mailtoc,
                    'email_status' => 'sent'
                ]);
            }
            
            // Don't echo anything here - ultimatemailer handles the response
            
        } catch (Exception $e) {
            echo json_encode(['status' => 'Error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

}
