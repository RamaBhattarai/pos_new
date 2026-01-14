<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Purchase extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model', 'purchase');
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        if (!$this->aauth->premission(2)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->li_a = 'stock';
        //exit('Under Dev Mode');


    }

    //create invoice
    public function create()
    {
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist($this->config->item('tax'));
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $data['currency'] = $this->purchase->currencies();
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
        $data['lastinvoice'] = $this->purchase->lastpurchase();
        $data['terms'] = $this->purchase->billingterms();
        $head['title'] = "New Purchase";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->purchase->warehouses();
        $data['taxdetails'] = $this->common->taxdetail();
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase/newinvoice', $data);
        $this->load->view('fixed/footer');
    }

    //create invoice
    /* ===================================================================
     * PURCHASE ENTRY METHODS  
     * Uses pos_purchase_entries and pos_purchase_invoice_items tables
     * Separate from purchase orders (pos_purchase and pos_purchase_items)
     * ================================================================= */

    public function create_entry()
    {
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist(-1); // Default to tax "On"
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $data['currency'] = $this->purchase->currencies();
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
        $data['lastinvoice'] = $this->purchase->lastpurchase_entry();
        $data['terms'] = $this->purchase->billingterms();
        $head['title'] = "New Purchase Entry";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->purchase->warehouses();
        $data['taxdetails'] = $this->common->taxdetail();
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase_entry/new_entry', $data);
        $this->load->view('fixed/footer');
    }

    // Create purchase entry - saves to pos_purchase_entries and pos_purchase_invoice_items
    public function action_entry()
    {
        // Get input data with defaults
        $currency = $this->input->post('mcurrency') ?: 0;
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('invocieno');
        $invoicedate = $this->input->post('invoicedate');
        $invocieduedate = $this->input->post('invocieduedate');
        $notes = $this->input->post('notes', true) ?: '';
        $tax = $this->input->post('tax_handle') ?: 'yes';
        $subtotal = rev_amountExchange_s($this->input->post('subtotal') ?: 0, $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping') ?: 0, $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax') ?: 0, $currency, $this->aauth->get_user()->loc);
        $ship_taxtype = $this->input->post('ship_taxtype') ?: 'incl';
        if ($ship_taxtype == 'incl') $shipping -= $shipping_tax;
        $refer = $this->input->post('refer', true) ?: '';
        $total = rev_amountExchange_s($this->input->post('total') ?: 0, $currency, $this->aauth->get_user()->loc);
        $discountFormat = $this->input->post('discountFormat') ?: '%';
        $pterms = $this->input->post('pterms') ?: 1;
        $discstatus = ($discountFormat == '0') ? 0 : 1;

        if ($customer_id == 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Please select a supplier.']);
            return;
        }

        $this->db->trans_start();

        // Insert main purchase entry record
        $entry_data = [
            'tid' => $invocieno,
            'invoicedate' => datefordatabase($invoicedate),
            'invoiceduedate' => datefordatabase($invocieduedate),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'ship_tax' => $shipping_tax,
            'ship_tax_type' => $ship_taxtype,
            'discount' => 0,
            'tax' => 0, 
            'total' => $total,
            'pmethod' => 'cash',
            'notes' => $notes,
            'status' => 'partial',
            'csd' => $customer_id,
            'eid' => $this->aauth->get_user()->id,
            'pamnt' => 0,
            'items' => 0,
            'taxstatus' => $tax,
            'discstatus' => $discstatus,
            'format_discount' => $discountFormat,
            'refer' => $refer,
            'term' => $pterms,
            'loc' => $this->aauth->get_user()->loc ?: 0,
            'multi' => $currency,
            'supplier_name' => '',
            'payment_status' => 'unpaid',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('pos_purchase_entries', $entry_data);
        $entry_insert_id = $this->db->insert_id();

        // Process product items
        $product_id = $this->input->post('pid') ?: [];
        $product_name = $this->input->post('product_name', true) ?: [];
        $batch_no = $this->input->post('batch_no', true) ?: [];
        $expiry_date = $this->input->post('expiry_date') ?: [];
        $product_qty = $this->input->post('product_qty') ?: [];
        $product_price = $this->input->post('product_price') ?: [];
        $selling_price = $this->input->post('selling_price') ?: [];
        $profit = $this->input->post('profit') ?: [];
        $profit_margin = $this->input->post('profit_margin') ?: [];
        $product_tax = $this->input->post('product_tax') ?: [];
        $product_discount = $this->input->post('product_discount') ?: [];
        $product_subtotal = $this->input->post('product_subtotal') ?: [];
        $ptotal_tax = $this->input->post('taxa') ?: [];
        $ptotal_disc = $this->input->post('disca') ?: [];
        $product_des = $this->input->post('product_description', true) ?: [];
        $product_unit = $this->input->post('unit') ?: [];
        $product_hsn = $this->input->post('hsn') ?: [];

        $productlist = [];
        $total_tax = 0;
        $total_discount = 0;
        $item_count = 0;

        if (!empty($product_id) && is_array($product_id)) {
            foreach ($product_id as $key => $pid) {
                if (empty($pid) || !isset($product_qty[$key]) || empty($product_qty[$key])) continue;

                $qty = numberClean($product_qty[$key]);
                $price = rev_amountExchange_s($product_price[$key] ?? 0, $currency, $this->aauth->get_user()->loc);
                $subtotal_row = rev_amountExchange_s($product_subtotal[$key] ?? 0, $currency, $this->aauth->get_user()->loc);

                // Calculate tax and discount amounts if not provided
                $tax_rate = numberClean($product_tax[$key] ?? 0);
                $discount_rate = numberClean($product_discount[$key] ?? 0);
                
                $item_total_tax = isset($ptotal_tax[$key]) && !empty($ptotal_tax[$key]) 
                    ? numberClean($ptotal_tax[$key]) 
                    : ($price * $qty * $tax_rate / 100);
                    
                $item_total_discount = isset($ptotal_disc[$key]) && !empty($ptotal_disc[$key]) 
                    ? numberClean($ptotal_disc[$key]) 
                    : ($price * $qty * $discount_rate / 100);

                $total_discount += $item_total_discount;
                $total_tax += $item_total_tax;

                $productlist[] = [
                    'tid' => $entry_insert_id,  // Use the actual database record ID, not the form TID
                    'pid' => $pid,
                    'product' => $product_name[$key] ?? '',
                    'batch' => $batch_no[$key] ?? '',
                    'expiry' => $expiry_date[$key] ?? '',
                    'code' => $product_hsn[$key] ?? '',
                    'qty' => $qty,
                    'price' => $price,
                    'selling_price' => $selling_price[$key] ?? 0,
                    'profit' => $profit[$key] ?? 0,
                    'profit_margin' => $profit_margin[$key] ?? 0,
                    'tax' => $tax_rate,
                    'discount' => $discount_rate,
                    'subtotal' => $subtotal_row,
                    'totaltax' => rev_amountExchange_s($item_total_tax, $currency, $this->aauth->get_user()->loc),
                    'totaldiscount' => rev_amountExchange_s($item_total_discount, $currency, $this->aauth->get_user()->loc),
                    'product_des' => $product_des[$key] ?? '',
                    'unit' => $product_unit[$key] ?? ''
                ];

                // Update stock if enabled
                if ($pid > 0 && $this->input->post('update_stock') == 'yes' && $this->aauth->premission(14)) {
                    $this->db->set('qty', "qty+$qty", FALSE)->where('pid', $pid)->update('pos_products');
                }

                $item_count += $qty;
            }
        }

        if (empty($productlist)) {
            echo json_encode(['status' => 'Error', 'message' => 'No products found in purchase entry list.']);
            return;
        }

        // Insert items and update totals
        $this->db->insert_batch('pos_purchase_invoice_items', $productlist);
        
        $update_data = [
            'discount' => rev_amountExchange_s($total_discount, $currency, $this->aauth->get_user()->loc),
            'tax' => rev_amountExchange_s($total_tax, $currency, $this->aauth->get_user()->loc),
            'items' => $item_count
        ];
        $this->db->where('id', $entry_insert_id)->update('pos_purchase_entries', $update_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
            echo json_encode(['status' => 'Error', 'message' => 'Failed to save purchase entry: ' . ($error['message'] ?? 'Unknown error')]);
        } else {
            // Update supplier's purchase total for confirmation tracking
            $this->load->model('supplier_model', 'supplier');
            $this->supplier->update_purchase_total($customer_id, $total);
            
            echo json_encode(['status' => 'Success', 'message' => "Purchase entry created successfully! <a href='view_entry?id=$entry_insert_id' class='btn btn-info btn-sm'>View</a>"]);
        }
    }



    //edit invoice
    public function edit()
    {

        $tid = $this->input->get('id');
        $data['id'] = $tid;
        $data['title'] = "Purchase Order $tid";
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
        $data['terms'] = $this->purchase->billingterms();
        $data['invoice'] = $this->purchase->purchase_details($tid);
        $data['products'] = $this->purchase->purchase_products($tid);;
        $head['title'] = "Edit Invoice #$tid";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->purchase->warehouses();
        $data['currency'] = $this->purchase->currencies();
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist_edit($data['invoice']['taxstatus']);
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase/edit', $data);
        $this->load->view('fixed/footer');

    }

    //invoices list
    public function index()
    {
        $head['title'] = "Manage Purchase Orders";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase/invoices');
        $this->load->view('fixed/footer');
    }

    // purchase entry list
public function purchase_entry()
{
    $head['title'] = "Manage Purchase Entries";
    $head['usernm'] = $this->aauth->get_user()->username;
    $this->load->view('fixed/header', $head);
    $this->load->view('purchase_entry/invoices'); // NEW VIEW
    $this->load->view('fixed/footer');
}


    //action
    // public function action()
    // {
    //     $currency = $this->input->post('mcurrency');
    //     $customer_id = $this->input->post('customer_id');
    //     $invocieno = $this->input->post('invocieno');
    //     $invoicedate = $this->input->post('invoicedate');
    //     $invocieduedate = $this->input->post('invocieduedate');
    //     $notes = $this->input->post('notes', true);
    //     $tax = $this->input->post('tax_handle');
    //     $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
    //     $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
    //     $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
    //     $ship_taxtype = $this->input->post('ship_taxtype');
    //     if ($ship_taxtype == 'incl') @$shipping = $shipping - $shipping_tax;
    //     $refer = $this->input->post('refer', true);
    //     $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
    //     $total_tax = 0;
    //     $total_discount = 0;
    //     $discountFormat = $this->input->post('discountFormat');
    //     $pterms = $this->input->post('pterms');
    //     $i = 0;
    //     if ($discountFormat == '0') {
    //         $discstatus = 0;
    //     } else {
    //         $discstatus = 1;
    //     }

    //     if ($customer_id == 0) {
    //         echo json_encode(array('status' => 'Error', 'message' =>
    //             "Please add a new supplier or search from a previous added!"));
    //         exit;
    //     }
    //     $this->db->trans_start();
    //     //products
    //     $transok = true;
    //     //Invoice Data
    //     $bill_date = datefordatabase($invoicedate);
    //     $bill_due_date = datefordatabase($invocieduedate);
    //     $data = array('tid' => $invocieno, 'invoicedate' => $bill_date, 'invoiceduedate' => $bill_due_date, 'subtotal' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'total' => $total, 'notes' => $notes, 'csd' => $customer_id, 'eid' => $this->aauth->get_user()->id, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms, 'loc' => $this->aauth->get_user()->loc, 'multi' => $currency);


    //     if ($this->db->insert('pos_purchase', $data)) {
    //         $invocieno = $this->db->insert_id();

    //         $pid = $this->input->post('pid');
    //         $productlist = array();
    //         $prodindex = 0;
    //         $itc = 0;
    //         $flag = false;
    //         $product_id = $this->input->post('pid');
    //         $product_name1 = $this->input->post('product_name', true);
    //         $product_qty = $this->input->post('product_qty');
    //         $product_price = $this->input->post('product_price');
    //         $product_tax = $this->input->post('product_tax');
    //         $product_discount = $this->input->post('product_discount');
    //         $product_subtotal = $this->input->post('product_subtotal');
    //         $ptotal_tax = $this->input->post('taxa');
    //         $ptotal_disc = $this->input->post('disca');
    //         $product_des = $this->input->post('product_description', true);
    //         $product_unit = $this->input->post('unit');
    //         $product_hsn = $this->input->post('hsn');


    //         foreach ($pid as $key => $value) {
    //             $total_discount += numberClean(@$ptotal_disc[$key]);
    //             $total_tax += numberClean($ptotal_tax[$key]);


    //             $data = array(
    //                 'tid' => $invocieno,
    //                 'pid' => $product_id[$key],
    //                 'product' => $product_name1[$key],
    //                 'code' => $product_hsn[$key],
    //                 'qty' => numberClean($product_qty[$key]),
    //                 'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
    //                 'tax' => numberClean($product_tax[$key]),
    //                 'discount' => numberClean($product_discount[$key]),
    //                 'subtotal' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
    //                 'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
    //                 'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
    //                 'product_des' => $product_des[$key],
    //                 'unit' => $product_unit[$key]
    //             );

    //             $flag = true;
    //             $productlist[$prodindex] = $data;
    //             $i++;
    //             $prodindex++;
    //             $amt = numberClean($product_qty[$key]);

    //             if ($product_id[$key] > 0) {
    //                 if ($this->input->post('update_stock') == 'yes' AND $this->aauth->premission(14)) {

    //                     $this->db->set('qty', "qty+$amt", FALSE);
    //                     $this->db->where('pid', $product_id[$key]);
    //                     $this->db->update('pos_products');
    //                 }
    //                 $itc += $amt;
    //             }

    //         }
    //         if ($prodindex > 0) {
    //             $this->db->insert_batch('pos_purchase_items', $productlist);
    //             $this->db->set(array('discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc), 'tax' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc), 'items' => $itc));
    //             $this->db->where('id', $invocieno);
    //             $this->db->update('pos_purchase');

    //         } else {
    //             echo json_encode(array('status' => 'Error', 'message' =>
    //                 "Please choose product from product list. Go to Item manager section if you have not added the products."));
    //             $transok = false;
    //         }


    //         echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Purchase order success') . "<a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span>" . $this->lang->line('View') . " </a>"));
    //     } else {
    //         echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
    //         $transok = false;
    //     }


    //     if ($transok) {
    //         $this->db->trans_complete();
    //     } else {
    //         $this->db->trans_rollback();
    //     }



    // }
public function action()
{
    try {
        $response = ['status' => 'Error', 'message' => 'Something went wrong.']; // Default fallback

    $currency = $this->input->post('mcurrency');
    $customer_id = $this->input->post('customer_id');
    $invocieno = $this->input->post('invocieno');
    $invoicedate = $this->input->post('invoicedate');
    $invocieduedate = $this->input->post('invocieduedate');
    $notes = $this->input->post('notes', true);
    $tax = $this->input->post('tax_handle');
    $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
    $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
    $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
    $ship_taxtype = $this->input->post('ship_taxtype');
    if ($ship_taxtype == 'incl') @$shipping -= $shipping_tax;
    $refer = $this->input->post('refer', true);
    $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
    $discountFormat = $this->input->post('discountFormat');
    $pterms = $this->input->post('pterms');
    $discstatus = ($discountFormat == '0') ? 0 : 1;

    if ($customer_id == 0) {
        echo json_encode(['status' => 'Error', 'message' => 'Please select a supplier.']);
        return;
    }

    $this->db->trans_start();

    $data = [
        'tid' => $invocieno,
        'invoicedate' => datefordatabase($invoicedate),
        'invoiceduedate' => datefordatabase($invocieduedate),
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'ship_tax' => $shipping_tax,
        'ship_tax_type' => $ship_taxtype,
        'total' => $total,
        'notes' => $notes,
        'csd' => $customer_id,
        'eid' => $this->aauth->get_user()->id,
        'taxstatus' => $tax,
        'discstatus' => $discstatus,
        'format_discount' => $discountFormat,
        'refer' => $refer,
        'term' => $pterms,
        'loc' => $this->aauth->get_user()->loc,
        'multi' => $currency
    ];

    if (!$this->db->insert('pos_purchase', $data)) {
        echo json_encode(['status' => 'Error', 'message' => 'Failed to insert purchase.']);
        return;
    }

    $invocieno = $this->db->insert_id();

    // Gather posted product arrays
    $product_id = $this->input->post('pid');
    $product_name = $this->input->post('product_name', true);
    $product_qty = $this->input->post('product_qty');
    $product_price = $this->input->post('product_price');
    $product_tax = $this->input->post('product_tax');
    $product_discount = $this->input->post('product_discount');
    $product_subtotal = $this->input->post('product_subtotal');
    $ptotal_tax = $this->input->post('taxa');
    $ptotal_disc = $this->input->post('disca');
    $product_des = $this->input->post('product_description', true);
    $product_unit = $this->input->post('unit');
    $product_hsn = $this->input->post('hsn');
    $product_batch_no = $this->input->post('batch_no', true);
    $product_expiry_date = $this->input->post('expiry_date', true);
    
    // Clean the expiry_date array - remove any "english"/"nepali" values and match product count
    if (is_array($product_expiry_date) && is_array($product_id)) {
        $product_count = count($product_id);
        
        error_log("Original expiry_date array: " . print_r($product_expiry_date, true));
        
        // First filter out all "english"/"nepali" values to get only valid dates
        $valid_dates = [];
        foreach ($product_expiry_date as $value) {
            if ($value !== 'english' && $value !== 'nepali' && !empty($value)) {
                $valid_dates[] = $value;
            } else {
                error_log("Filtered out invalid expiry_date value: '$value'");
            }
        }
        
        // Now map the valid dates to products (in order)
        $cleaned_expiry_dates = [];
        for ($i = 0; $i < $product_count; $i++) {
            if (isset($valid_dates[$i])) {
                $cleaned_expiry_dates[$i] = $valid_dates[$i];
            } else {
                $cleaned_expiry_dates[$i] = ''; // Set to empty if no more valid dates
            }
        }
        
        $product_expiry_date = $cleaned_expiry_dates;
        error_log("Cleaned expiry_date array: " . print_r($product_expiry_date, true));
    }
    
    // Collect new batch fields
    $product_selling_price = $this->input->post('selling_price');
    $product_profit = $this->input->post('profit');
    $product_profit_margin = $this->input->post('profit_margin');

    $productlist = [];
    $total_tax = 0;
    $total_discount = 0;
    $itc = 0;

    // Debug: log counts for batches and products
    error_log("Batch Processing: Product count = " . count($product_id) . ", Batch no count = " . count($product_batch_no) . ", Expiry date count = " . count($product_expiry_date));

    foreach ($product_id as $key => $pid) {
        if (!$pid || !isset($product_qty[$key]) || !$product_qty[$key]) continue;

        $qty = numberClean($product_qty[$key]);
        $amt = rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc);
        $subtotal_row = rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc);

        $total_discount += numberClean($ptotal_disc[$key]);
        $total_tax += numberClean($ptotal_tax[$key]);

        $batch_no = $product_batch_no[$key] ?? '';
        $expiry_date_raw = $product_expiry_date[$key] ?? '';
        
        // Debug log the expiry date value with more detail
        error_log("Debug: Processing expiry_date for product $key: '" . $expiry_date_raw . "' (Length: " . strlen($expiry_date_raw) . ")");
        error_log("Debug: All expiry_date POST data: " . print_r($product_expiry_date, true));
        
        // Skip processing if expiry date contains invalid values
        if ($expiry_date_raw === 'english' || $expiry_date_raw === 'nepali') {
            error_log("Warning: Skipping invalid expiry_date value: " . $expiry_date_raw);
            $expiry_date = null;
        } else {
            $expiry_date = !empty($expiry_date_raw) ? datefordatabase($expiry_date_raw) : null;
            error_log("Debug: Final expiry_date for product $key: " . ($expiry_date ?: 'NULL'));
        }
        // New fields
        $selling_price = isset($product_selling_price[$key]) ? numberClean($product_selling_price[$key]) : 0;
        $profit = isset($product_profit[$key]) ? numberClean($product_profit[$key]) : 0;
        $profit_margin = isset($product_profit_margin[$key]) ? numberClean($product_profit_margin[$key]) : 0;

        $productlist[] = [
            'tid' => $invocieno,
            'pid' => $pid,
            'product' => $product_name[$key],
            'code' => $product_hsn[$key],
            'qty' => $qty,
            'price' => $amt,
            'tax' => numberClean($product_tax[$key]),
            'discount' => numberClean($product_discount[$key]),
            'subtotal' => $subtotal_row,
            'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
            'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
            'product_des' => $product_des[$key],
            'unit' => $product_unit[$key],
            'batch_no' => $batch_no,
            'expiry_date' => $expiry_date,
            // New fields
            'selling_price' => $selling_price,
            'profit' => $profit,
            'profit_margin' => $profit_margin
        ];

        // Update stock in products
        if ($pid > 0 && $this->input->post('update_stock') == 'yes' && $this->aauth->premission(14)) {
            $this->db->set('qty', "qty+$qty", FALSE)->where('pid', $pid)->update('pos_products');
            
            // Check if this is a variant product and update parent stock
            $this->db->select('merge, sub');
            $this->db->where('pid', $pid);
            $product_info = $this->db->get('pos_products')->row();
            
            if ($product_info && $product_info->merge == 1) {
                // This is a variant, update parent total stock
                $this->load->model('products_model');
                $this->products_model->update_parent_total_stock($product_info->sub);
            }
        }
  // ======= INSERT THIS BLOCK TO VALIDATE WAREHOUSE =======
   
    // Get the correct warehouse ID from the product
$this->db->select('warehouse');
$this->db->where('pid', $pid);
$product_warehouse = $this->db->get('pos_products')->row();

if ($product_warehouse && $product_warehouse->warehouse > 0) {
    $warehouse_id = $product_warehouse->warehouse;
} else {
    // Fallback to user's location if product warehouse not set
    $warehouse_id = $this->aauth->get_user()->loc;
}

// Validate the warehouse exists
$this->db->where('id', $warehouse_id);
$valid_warehouse = $this->db->get('pos_warehouse')->row();

if (!$valid_warehouse) {
    // Last resort fallback - use first warehouse
    $this->db->select('id');
    $this->db->limit(1);
    $fallback = $this->db->get('pos_warehouse')->row();
    $warehouse_id = $fallback ? $fallback->id : 4; // Default to 4 only if no warehouses exist
}
    // ======= END WAREHOUSE VALIDATION =======

        $this->db->where([
            'product_id' => $pid,
            'batch_no' => $batch_no,
            'expiry_date' => $expiry_date,
            'warehouse_id' => $warehouse_id
        ]);
        $query = $this->db->get('pos_product_batches');

        // Debug: log last query for batch check
        error_log("Batch query SQL: " . $this->db->last_query());

        $existing = $query->row();

        if ($existing) {
            // Update qty and new fields
            $update_status = $this->db->set('qty', 'qty+' . $qty, FALSE)
                ->set('selling_price', $selling_price)
                ->set('profit', $profit)
                ->set('profit_margin', $profit_margin)
                ->where('id', $existing->id)
                ->update('pos_product_batches');

            if (!$update_status) {
                $error = $this->db->error();
                error_log("Batch update failed: " . print_r($error, true));
                echo json_encode(['status' => 'Error', 'message' => 'Batch update failed: ' . $error['message']]);
                exit;
            }
        } else {
            $insert_data = [
                'product_id' => $pid,
                'batch_no' => $batch_no,
                'expiry_date' => $expiry_date,
                'warehouse_id' => $warehouse_id,
                'qty' => $qty,
                'purchase_price' => $amt,
                'selling_price' => $selling_price,
                'profit' => $profit,
                'profit_margin' => $profit_margin,
                'purchase_tid' => $invocieno
            ];
            $insert_status = $this->db->insert('pos_product_batches', $insert_data);

            if (!$insert_status) {
                $error = $this->db->error();
                error_log("Batch insert failed: " . print_r($error, true));
                echo json_encode(['status' => 'Error', 'message' => 'Batch insert failed: ' . $error['message']]);
                exit;
            }
        }

        $itc += $qty;
    }

    // Final batch insert for purchase items
    if (!empty($productlist)) {
        // Debug: Show ALL raw POST data for expiry dates
        error_log('Raw POST expiry_date array: ' . print_r($this->input->post('expiry_date'), true));
        error_log('Raw POST batch_no array: ' . print_r($this->input->post('batch_no'), true));
        error_log('Raw POST product_name array: ' . print_r($this->input->post('product_name'), true));
        
        // Also create a simple debug file for easier viewing
        file_put_contents('debug_purchase_insert.txt', 
            "Purchase Order: $invocieno\n" .
            "Raw POST expiry_date: " . print_r($this->input->post('expiry_date'), true) . "\n" .
            "Raw POST batch_no: " . print_r($this->input->post('batch_no'), true) . "\n" .
            "Items to insert: " . count($productlist) . "\n" .
            "Data: " . json_encode($productlist, JSON_PRETTY_PRINT) . "\n\n", 
            FILE_APPEND
        );
        
        $insert_result = $this->db->insert_batch('pos_purchase_items', $productlist);
        
        // Check for database errors
        if (!$insert_result) {
            $db_error = $this->db->error();
            error_log('Database insert error: ' . json_encode($db_error));
            file_put_contents('debug_purchase_insert.txt', 
                "ERROR: Database insert failed: " . json_encode($db_error) . "\n\n", 
                FILE_APPEND
            );
            echo json_encode(['status' => 'Error', 'message' => 'Database insert failed: ' . $db_error['message']]);
            return;
        }
        
        error_log('Successfully inserted ' . count($productlist) . ' products');
        file_put_contents('debug_purchase_insert.txt', 
            "SUCCESS: Inserted " . count($productlist) . " products successfully\n\n", 
            FILE_APPEND
        );
        
        $this->db->where('id', $invocieno)->update('pos_purchase', [
            'discount' => rev_amountExchange_s($total_discount, $currency, $this->aauth->get_user()->loc),
            'tax' => rev_amountExchange_s($total_tax, $currency, $this->aauth->get_user()->loc),
            'items' => $itc
        ]);
    } else {
        echo json_encode(['status' => 'Error', 'message' => 'No product found in purchase list.']);
        return;
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        echo json_encode([
            'status' => 'Error',
            'message' => 'Database transaction failed: ' . $this->db->error()['message']
        ]);
    } else {
        echo json_encode([
            'status' => 'Success',
            'message' => "Purchase created successfully! <a href='view?id=$invocieno' class='btn btn-info btn-sm'>View</a>"
        ]);
    }
    } catch (Exception $e) {
        error_log("Purchase action() exception: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        echo json_encode([
            'status' => 'Error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

public function ajax_list()
    {

        $list = $this->purchase->get_datatables();
        $data = array();

        $no = $this->input->post('start');

        foreach ($list as $invoices) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $invoices->tid;
            $row[] = $invoices->name;
            $row[] = dateformat($invoices->invoicedate);
            $row[] = amountExchange($invoices->total, 0, $this->aauth->get_user()->loc);
            $row[] = '<span class="st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';
            // $row[] = '<a href="' . base_url("purchase/view?id=$invoices->id") . '" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> ' . $this->lang->line('View') . '</a> &nbsp; <a href="' . base_url("purchase/printinvoice?id=$invoices->id") . '&d=1" class="btn btn-info btn-xs"  title="Download"><span class="fa fa-download"></span></a>&nbsp; &nbsp;<a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-xs delete-object"><span class="fa fa-trash"></span></a>';
          //cancel----button---added---
            $row[] = '<a href="' . base_url("purchase/view?id=$invoices->id") . '" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> ' . $this->lang->line('View') . '</a> &nbsp; 
            <a href="' . base_url("purchase/printinvoice?id=$invoices->id") . '&d=1" class="btn btn-info btn-xs"  title="Download"><span class="fa fa-download"></span></a> &nbsp; 
            <a href="#cancel-bill" class="btn btn-danger btn-xs cancel-bill-btn" data-id="' . $invoices->id . '"><i class="fa fa-minus-circle"></i> ' . $this->lang->line('Cancel') . '</a>';
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->purchase->count_all(),
            "recordsFiltered" => $this->purchase->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);

    }
    // Purchase Entry List
    public function ajax_entry_list()
    {
        $list = $this->purchase->get_datatables_entries(); // Fetch data from the model
        $data = [];
        $no = $this->input->post('start');
        foreach ($list as $entry) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $entry->tid; // Transaction ID
            $row[] = $entry->name; // Supplier Name from pos_supplier table
            $row[] = dateformat($entry->invoicedate); // Invoice Date formatted
            $row[] = amountExchange($entry->total, 0, $this->aauth->get_user()->loc); // Total Amount formatted
            $row[] = '<span class="st-' . $entry->status . '">' . $this->lang->line(ucwords($entry->status)) . '</span>'; // Status with styling
            $row[] = '<a href="' . base_url("purchase/view_entry?id=$entry->id") . '" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> ' . $this->lang->line('View') . '</a> &nbsp; 
                      <a href="' . base_url("purchase/edit_entry?id=$entry->id") . '" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> ' . $this->lang->line('Edit') . '</a> &nbsp; 
                      <a href="' . base_url("purchase/printinvoice?id=$entry->id") . '&d=1" class="btn btn-info btn-xs"  title="Download"><span class="fa fa-download"></span></a> &nbsp; 
                      <a href="#cancel-bill" class="btn btn-danger btn-xs cancel-bill-btn" data-id="' . $entry->id . '"><i class="fa fa-minus-circle"></i> ' . $this->lang->line('Cancel') . '</a>';
            $data[] = $row;
        }

        $output = [
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->purchase->count_all_entries(),
            "recordsFiltered" => $this->purchase->count_filtered_entries(),
            "data" => $data,
        ];
        echo json_encode($output);
    }



    public function view()
    {
        $this->load->model('accounts_model');
        $data['acclist'] = $this->accounts_model->accountslist((integer)$this->aauth->get_user()->loc);
        $tid = intval($this->input->get('id'));
        $data['id'] = $tid;
        $head['title'] = "Purchase $tid";
        $data['invoice'] = $this->purchase->purchase_details($tid);
        $data['products'] = $this->purchase->purchase_products($tid);
        $data['activity'] = $this->purchase->purchase_transactions($tid);
        $data['attach'] = $this->purchase->attach($tid);
        $data['employee'] = $this->purchase->employee($data['invoice']['eid']);
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        if ($data['invoice']['tid']) $this->load->view('purchase/view', $data);
        $this->load->view('fixed/footer');

    }


    public function printinvoice()
    {
        $tid = $this->input->get('id');
        $type = $this->input->get('type'); // 'entry' for purchase entry, default for purchase order

        $data['id'] = $tid;
        
        if ($type === 'entry') {
            $data['title'] = "Purchase Entry $tid";
            $data['invoice'] = $this->purchase->purchase_entry_details($tid);
            if ($data['invoice']) {
                // Use the record ID directly for purchase entries
                $data['products'] = $this->purchase->purchase_entry_products($tid);
            } else {
                show_404('Purchase entry not found');
                return;
            }
            $data['general'] = array('title' => $this->lang->line('Purchase Entry'), 'person' => $this->lang->line('Supplier'), 'prefix' => prefix(2), 't_type' => 1);
        } else {
            $data['title'] = "Purchase $tid";
            $data['invoice'] = $this->purchase->purchase_details($tid);
            $data['products'] = $this->purchase->purchase_products($tid);
            $data['general'] = array('title' => $this->lang->line('Purchase Order'), 'person' => $this->lang->line('Supplier'), 'prefix' => prefix(2), 't_type' => 0);
        }
        
        $data['employee'] = $this->purchase->employee($data['invoice']['eid']);
        $data['invoice']['multi'] = 0;

        //  Add this: show "Copy of Original" if print_count > 1
        $data['copy_of_original'] = $data['invoice']['print_count'] > 1 ? "Copy of Original" : "";


        ini_set('memory_limit', '64M');

        if ($data['invoice']['taxstatus'] == 'cgst' || $data['invoice']['taxstatus'] == 'igst') {
            $html = $this->load->view('print_files/invoice-a4-gst_v' . INVV, $data, true);
        } else {
            $html = $this->load->view('print_files/invoice-a4_v' . INVV, $data, true);
        }

        //PDF Rendering
        $this->load->library('pdf');
        if (INVV == 1) {
            $header = $this->load->view('print_files/invoice-header_v' . INVV, $data, true);
            $pdf = $this->pdf->load_split(array('margin_top' => 40));
            $pdf->SetHTMLHeader($header);
        }
        if (INVV == 2) {
            $pdf = $this->pdf->load_split(array('margin_top' => 5));
        }
        $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $data['invoice']['tid'] . '</div>');

        $pdf->WriteHTML($html);
        
        // ✅ Now update the print_count AFTER PDF generation (if column exists)
        if ($type === 'entry') {
            // Check if print_count column exists in pos_purchase_entries
            $fields = $this->db->field_data('pos_purchase_entries');
            $has_print_count = false;
            foreach ($fields as $field) {
                if ($field->name === 'print_count') {
                    $has_print_count = true;
                    break;
                }
            }
            
            if ($has_print_count) {
                $this->db->set('print_count', 'print_count + 1', FALSE);
                $this->db->where('id', $tid);
                $this->db->update('pos_purchase_entries');
            }
        } else {
            $this->db->set('print_count', 'print_count + 1', FALSE);
            $this->db->where('id', $tid);
            $this->db->update('pos_purchase');
        }

        if ($this->input->get('d')) {

            $pdf->Output('Purchase_#' . $data['invoice']['tid'] . '.pdf', 'D');
        } else {
            $pdf->Output('Purchase_#' . $data['invoice']['tid'] . '.pdf', 'I');
        }


    }

    public function delete_i()
    {
        $id = $this->input->post('deleteid');

        if ($this->purchase->purchase_delete($id)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                "Purchase Order #$id has been deleted successfully!"));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>
                "There is an error! Purchase has not deleted."));
        }

    }

    public function editaction()
    {
        $currency = $this->input->post('mcurrency');
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('iid');
        $invoicedate = $this->input->post('invoicedate');
        $invocieduedate = $this->input->post('invocieduedate');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $refer = $this->input->post('refer', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
        $total_tax = 0;
        $total_discount = 0;
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms');
        $ship_taxtype = $this->input->post('ship_taxtype');
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;

        $itc = 0;
        if ($discountFormat == '0') {
            $discstatus = 0;
        } else {
            $discstatus = 1;
        }

        if ($customer_id == 0) {
            echo json_encode(array('status' => 'Error', 'message' =>
                "Please add a new supplier or search from a previous added!"));
            exit();
        }

        $this->db->trans_start();
        $flag = false;
        $transok = true;


        //Product Data
        $pid = $this->input->post('pid');
        $productlist = array();

        $prodindex = 0;

        $this->db->delete('pos_purchase_items', array('tid' => $invocieno));
        $product_id = $this->input->post('pid');
        $product_name1 = $this->input->post('product_name', true);
        $product_qty = $this->input->post('product_qty');
        $old_product_qty = $this->input->post('old_product_qty');
        if ($old_product_qty == '') $old_product_qty = 0;
        $product_price = $this->input->post('product_price');
        $product_tax = $this->input->post('product_tax');
        $product_discount = $this->input->post('product_discount');
        $product_subtotal = $this->input->post('product_subtotal');
        $ptotal_tax = $this->input->post('taxa');
        $ptotal_disc = $this->input->post('disca');
        $product_des = $this->input->post('product_description', true);
        $product_unit = $this->input->post('unit');
        $product_hsn = $this->input->post('hsn');

        foreach ($pid as $key => $value) {
            $total_discount += numberClean(@$ptotal_disc[$key]);
            $total_tax += numberClean($ptotal_tax[$key]);
            $data = array(
                'tid' => $invocieno,
                'pid' => $product_id[$key],
                'product' => $product_name1[$key],
                'code' => $product_hsn[$key],
                'qty' => numberClean($product_qty[$key]),
                'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'tax' => numberClean($product_tax[$key]),
                'discount' => numberClean($product_discount[$key]),
                'subtotal' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                'product_des' => $product_des[$key],
                'unit' => $product_unit[$key]
            );


            $productlist[$prodindex] = $data;

            $prodindex++;
            $amt = numberClean($product_qty[$key]);
            $itc += $amt;

            if ($this->input->post('update_stock') == 'yes') {
                $amt = numberClean(@$product_qty[$key]) - numberClean(@$old_product_qty[$key]);
                $this->db->set('qty', "qty+$amt", FALSE);
                $this->db->where('pid', $product_id[$key]);
                $this->db->update('pos_products');
                
                // Check if this is a variant product and update parent stock
                $this->db->select('merge, sub');
                $this->db->where('pid', $product_id[$key]);
                $product_info = $this->db->get('pos_products')->row();
                
                if ($product_info && $product_info->merge == 1) {
                    // This is a variant, update parent total stock
                    $this->load->model('products_model');
                    $this->products_model->update_parent_total_stock($product_info->sub);
                }
            }
            $flag = true;
        }

        $bill_date = datefordatabase($invoicedate);
        $bill_due_date = datefordatabase($invocieduedate);
        $total_discount = rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc);
        $total_tax = rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc);

        $data = array('invoicedate' => $bill_date, 'invoiceduedate' => $bill_due_date, 'subtotal' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount' => $total_discount, 'tax' => $total_tax, 'total' => $total, 'notes' => $notes, 'csd' => $customer_id, 'items' => $itc, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms, 'multi' => $currency);
        $this->db->set($data);
        $this->db->where('id', $invocieno);

        if ($flag) {

            if ($this->db->update('pos_purchase', $data)) {
                $this->db->insert_batch('pos_purchase_items', $productlist);
                echo json_encode(array('status' => 'Success', 'message' =>
                    "Purchase order has  been updated successfully! <a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> View </a> "));
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    "There is a missing field!"));
                $transok = false;
            }


        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                "Please add atleast one product in order!"));
            $transok = false;
        }

        if ($this->input->post('update_stock') == 'yes') {
            if ($this->input->post('restock')) {
                foreach ($this->input->post('restock') as $key => $value) {
                    $myArray = explode('-', $value);
                    $prid = $myArray[0];
                    $dqty = numberClean($myArray[1]);
                    if ($prid > 0) {

                        $this->db->set('qty', "qty-$dqty", FALSE);
                        $this->db->where('pid', $prid);
                        $this->db->update('pos_products');
                        
                        // Check if this is a variant product and update parent stock
                        $this->db->select('merge, sub');
                        $this->db->where('pid', $prid);
                        $product_info = $this->db->get('pos_products')->row();
                        
                        if ($product_info && $product_info->merge == 1) {
                            // This is a variant, update parent total stock
                            $this->load->model('products_model');
                            $this->products_model->update_parent_total_stock($product_info->sub);
                        }
                    }
                }

            }
        }

        if ($transok) {
            $this->db->trans_complete();
        } else {
            $this->db->trans_rollback();
        }
    }

    public function update_status()
    {
        $tid = $this->input->post('tid');
        $status = $this->input->post('status');

        // Check if this ID exists in pos_purchase_entries (purchase entries)
        $this->db->where('id', $tid);
        $entry_check = $this->db->get('pos_purchase_entries');
        
        if ($entry_check->num_rows() > 0) {
            // This is a purchase entry - update pos_purchase_entries
            $this->db->set('status', $status);
            $this->db->where('id', $tid);
            $this->db->update('pos_purchase_entries');
            
            echo json_encode(array('status' => 'Success', 'message' =>
                'Purchase Entry Status updated successfully!', 'pstatus' => $status));
        } else {
            // This is a purchase order - update pos_purchase
            $this->db->set('status', $status);
            $this->db->where('id', $tid);
            $this->db->update('pos_purchase');

            echo json_encode(array('status' => 'Success', 'message' =>
                'Purchase Order Status updated successfully!', 'pstatus' => $status));
        }
    }

    public function file_handling()
    {
        if ($this->input->get('op')) {
            $name = $this->input->get('name');
            $invoice = $this->input->get('invoice');
            if ($this->purchase->meta_delete($invoice, 4, $name)) {
                echo json_encode(array('status' => 'Success'));
            }
        } else {
            $id = $this->input->get('id');
            $this->load->library("Uploadhandler_generic", array(
                'accept_file_types' => '/\.(gif|jpe?g|png|docx|docs|txt|pdf|xls)$/i', 'upload_dir' => FCPATH . 'userfiles/attach/', 'upload_url' => base_url() . 'userfiles/attach/'
            ));
            $files = (string)$this->uploadhandler_generic->filenaam();
            if ($files != '') {

                $this->purchase->meta_insert($id, 4, $files);
            }
        }
    }

    // AJAX: Get next batch number
    public function get_next_batch_no() {
        $this->db->select('batch_no');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $latest = $this->db->get('pos_product_batches')->row();
        if ($latest && preg_match('/^B(\d{5})$/', $latest->batch_no, $matches)) {
            $next_num = intval($matches[1]) + 1;
        } else {
            $next_num = 1;
        }
        $next_batch = 'B' . str_pad($next_num, 5, '0', STR_PAD_LEFT);
        echo json_encode(['batch_no' => $next_batch]);
    }

    // AJAX: Get all batches for a product (and warehouse)
public function get_product_batches() {
    $product_id = $this->input->get('product_id');
    $warehouse_id = $this->input->get('warehouse_id'); // Optional, can default to user's loc

    if (!$product_id) {
        echo json_encode(['status' => 'Error', 'message' => 'No product ID provided.']);
        return;
    }

    if (!$warehouse_id) {
        $warehouse_id = $this->aauth->get_user()->loc;
    }

    $this->db->where('product_id', $product_id);
    // $this->db->where('warehouse_id', $warehouse_id);
      $this->db->where('(warehouse_id = ' . $warehouse_id . ' OR warehouse_id = 0)', NULL, FALSE);
    $batches = $this->db->get('pos_product_batches')->result_array();

    if ($batches) {
        echo json_encode(['status' => 'Success', 'batches' => $batches]);
    } else {
        echo json_encode(['status' => 'Empty', 'batches' => []]);
    }
}    // View Purchase Entry
    public function view_entry()
    {
        $this->load->model('accounts_model');
        $data['acclist'] = $this->accounts_model->accountslist((integer)$this->aauth->get_user()->loc);
        $tid = intval($this->input->get('id'));
        $data['id'] = $tid;
        $head['title'] = "Purchase Entry $tid";
        $data['invoice'] = $this->purchase->purchase_entry_details($tid);
        if (!$data['invoice']) {
            show_404('Purchase entry not found');
            return;
        }
        
        // Use the record ID directly to get products (since we changed the TID in products to use record ID)
        $data['products'] = $this->purchase->purchase_entry_products($tid);
        
        // Debug: Check what products are being retrieved
        if (empty($data['products'])) {
            log_message('debug', 'No products found for purchase entry record ID: ' . $tid);
        } else {
            log_message('debug', 'Found ' . count($data['products']) . ' products for record ID: ' . $tid);
        }
        
        $data['activity'] = $this->purchase->purchase_entry_transactions($tid);
        $data['attach'] = $this->purchase->attach_entry($tid);
        $data['employee'] = isset($data['invoice']['eid']) ? $this->purchase->employee($data['invoice']['eid']) : null;
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        if ($data['invoice']['tid']) $this->load->view('purchase_entry/view', $data); // Use dedicated purchase entry view
        else show_404('Purchase entry not found');
        $this->load->view('fixed/footer');
    }

    // Edit Purchase Entry  
    public function edit_entry()
    {
        try {
            $tid = $this->input->get('id');
            if (!$tid) {
                show_404('Purchase entry ID not provided');
                return;
            }
            
            $data['id'] = $tid;
            $data['title'] = "Purchase Entry $tid";
            $this->load->library("Common");
            $this->load->model('customers_model', 'customers');
            $data['customergrouplist'] = $this->customers->group_list();
            $data['terms'] = $this->purchase->billingterms();
            $data['invoice'] = $this->purchase->purchase_entry_details($tid);
            
            if (!$data['invoice']) {
                show_404('Purchase entry not found');
                return;
            }
            
            $data['products'] = $this->purchase->purchase_entry_products($tid);
            $head['title'] = "Edit Purchase Entry #$tid";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data['warehouse'] = $this->purchase->warehouses();
            $data['currency'] = $this->purchase->currencies();
            $this->load->model('plugins_model', 'plugins');
            $data['exchange'] = $this->plugins->universal_api(5);
            $data['taxdetails'] = $this->common->taxdetail();
            $data['taxlist'] = $this->common->taxlist_edit($data['invoice']['taxstatus']);
            
            $this->load->view('fixed/header', $head);
            $this->load->view('purchase_entry/edit', $data);
            $this->load->view('fixed/footer');
        } catch (Exception $e) {
            log_message('error', 'Edit entry error: ' . $e->getMessage());
            show_error('An error occurred while loading the purchase entry: ' . $e->getMessage());
        }
    }

    // Debug function to check form data
    public function debug_entry()
    {
        echo "<h3>POST Data:</h3>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        echo "<h3>Specific Values:</h3>";
        echo "Supplier ID (csd): " . $this->input->post('csd') . "<br>";
        echo "Product Names: ";
        print_r($this->input->post('product_name'));
        echo "<br>Total: " . $this->input->post('total') . "<br>";
        echo "Invoice Date: " . $this->input->post('invoicedate') . "<br>";
        echo "Due Date: " . $this->input->post('invocieduedate') . "<br>";
        echo "Terms: " . $this->input->post('pterms') . "<br>";
    }

    // Edit purchase entry - saves to pos_purchase_entries and pos_purchase_invoice_items
    public function editaction_entry()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        
        // Debug: Log all POST data
        error_log('EDITACTION_ENTRY DEBUG: POST data received: ' . print_r($_POST, true));
        
        // Get input data with defaults
        $entry_id = $this->input->post('iid'); // The database ID of the purchase entry
        $currency = $this->input->post('mcurrency') ?: 0;
        $customer_id = $this->input->post('customer_id');
        error_log('EDITACTION_ENTRY DEBUG: entry_id = ' . $entry_id . ', customer_id = ' . $customer_id);

        // Get current entry to preserve currency if not provided
        $current_entry = $this->purchase->purchase_entry_details($entry_id);
        if (!$current_entry) {
            echo json_encode(['status' => 'Error', 'message' => 'Purchase entry not found.']);
            return;
        }
        if (!$currency) {
            $currency = $current_entry['multi'] ?: 0;
        }

        // Validate entry exists
        $this->db->select('id');
        $this->db->from('pos_purchase_entries');
        $this->db->where('id', $entry_id);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Purchase entry not found: ' . $entry_id]);
            return;
        }
        $invocieno = $this->input->post('invocieno');
        
        // Validate invoice number uniqueness (excluding current entry)
        $this->db->select('id');
        $this->db->from('pos_purchase_entries');
        $this->db->where('tid', $invocieno);
        $this->db->where('id !=', $entry_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Invoice number already exists: ' . $invocieno]);
            return;
        }
        
        $invoicedate_raw = $this->input->post('invoicedate');
        $invocieduedate_raw = $this->input->post('invocieduedate');
        // Clean date inputs by removing language suffixes
        $invoicedate = $invoicedate_raw ? str_replace([' EN', ' NP', ' english', ' nepali', 'EN', 'NP'], '', $invoicedate_raw) : '';
        $invocieduedate = $invocieduedate_raw ? str_replace([' EN', ' NP', ' english', ' nepali', 'EN', 'NP'], '', $invocieduedate_raw) : '';
        $notes = $this->input->post('notes', true) ?: '';
        $tax = $this->input->post('tax_handle') ?: 'yes';
        $subtotal = numberClean($this->input->post('subtotal') ?: 0);
        $shipping = numberClean($this->input->post('shipping') ?: 0);
        $shipping_tax = numberClean($this->input->post('ship_tax') ?: 0);
        $ship_taxtype = $this->input->post('ship_taxtype') ?: 'incl';
        if ($ship_taxtype == 'incl') $shipping -= $shipping_tax;
        $refer = $this->input->post('refer', true) ?: '';
        $total = rev_amountExchange_s($this->input->post('total') ?: 0, $currency, $this->aauth->get_user()->loc);
        $discountFormat = $this->input->post('discountFormat') ?: '%';
        $pterms = $this->input->post('pterms') ?: 1;
        $discstatus = ($discountFormat == '0') ? 0 : 1;

        if ($customer_id == 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Please select a supplier.']);
            return;
        }

        // Validate customer exists
        $this->db->select('id');
        $this->db->from('pos_supplier');
        $this->db->where('id', $customer_id);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid supplier ID: ' . $customer_id]);
            return;
        }

        // Validate product IDs exist
        $product_id = $this->input->post('pid') ?: [];
        $invalid_pids = [];
        foreach ($product_id as $pid) {
            if (!empty($pid)) {
                $this->db->select('pid');
                $this->db->from('pos_products');
                $this->db->where('pid', $pid);
                $query = $this->db->get();
                if ($query->num_rows() == 0) {
                    $invalid_pids[] = $pid;
                }
            }
        }
        if (!empty($invalid_pids)) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid product IDs: ' . implode(', ', $invalid_pids)]);
            return;
        }

        if (empty($product_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'No products selected.']);
            return;
        }

        // Build product list and calculate totals BEFORE starting transaction
        $product_id = $this->input->post('pid') ?: [];
        $product_name = $this->input->post('product_name', true) ?: [];
        $batch_no = $this->input->post('batch_no', true) ?: [];
        $expiry_date = $this->input->post('expiry_date') ?: [];
        $product_qty = $this->input->post('product_qty') ?: [];
        $product_price = $this->input->post('product_price') ?: [];
        $selling_price = $this->input->post('selling_price') ?: [];
        $profit = $this->input->post('profit') ?: [];
        $profit_margin = $this->input->post('profit_margin') ?: [];
        $product_tax = $this->input->post('product_tax') ?: [];
        $product_discount = $this->input->post('product_discount') ?: [];
        $product_subtotal = $this->input->post('product_subtotal') ?: [];
        $ptotal_tax = $this->input->post('taxa') ?: [];
        $ptotal_disc = $this->input->post('disca') ?: [];
        $product_des = $this->input->post('product_description', true) ?: [];
        $product_unit = $this->input->post('unit') ?: [];
        $product_hsn = $this->input->post('hsn') ?: [];

        $productlist = [];
        $total_tax = 0;
        $total_discount = 0;
        $item_count = 0;

        if (!empty($product_id) && is_array($product_id)) {
            foreach ($product_id as $key => $pid) {
                if (empty($pid) || !isset($product_qty[$key]) || empty($product_qty[$key])) continue;

                $qty = numberClean($product_qty[$key]);
                $price = numberClean($product_price[$key] ?? 0);
                $subtotal_row = $qty * $price;

                // Calculate tax and discount amounts if not provided
                $tax_rate = numberClean($product_tax[$key] ?? 0);
                $discount_rate = numberClean($product_discount[$key] ?? 0);

                $item_total_tax = isset($ptotal_tax[$key]) && !empty($ptotal_tax[$key])
                    ? numberClean($ptotal_tax[$key])
                    : ($price * $qty * $tax_rate / 100);

                $item_total_discount = isset($ptotal_disc[$key]) && !empty($ptotal_disc[$key])
                    ? numberClean($ptotal_disc[$key])
                    : ($price * $qty * $discount_rate / 100);

                $total_discount += $item_total_discount;
                $total_tax += $item_total_tax;

                // Process expiry date
                $expiry_date_raw = $expiry_date[$key] ?? '';
                if ($expiry_date_raw === 'english' || $expiry_date_raw === 'nepali' || empty($expiry_date_raw)) {
                    $processed_expiry = null;
                } else {
                    $processed_expiry = datefordatabase($expiry_date_raw);
                }

                $productlist[] = [
                    'tid' => $entry_id,
                    'pid' => $pid,
                    'product' => $product_name[$key] ?? '',
                    'batch' => $batch_no[$key] ?? '',
                    'expiry' => $processed_expiry,
                    'code' => $product_hsn[$key] ?? '',
                    'qty' => $qty,
                    'price' => $price,
                    'selling_price' => $selling_price[$key] ?? 0,
                    'profit' => $profit[$key] ?? 0,
                    'profit_margin' => $profit_margin[$key] ?? 0,
                    'tax' => $tax_rate,
                    'discount' => $discount_rate,
                    'subtotal' => $subtotal_row,
                    'totaltax' => $item_total_tax,
                    'totaldiscount' => $item_total_discount,
                    'product_des' => $product_des[$key] ?? '',
                    'unit' => $product_unit[$key] ?? ''
                ];

                $item_count++;
            }
        }

        $this->db->trans_start();

        // Calculate subtotal and total from products
        $calculated_subtotal = 0;
        foreach ($productlist as $p) {
            $calculated_subtotal += $p['subtotal'];
        }
        $calculated_total = $calculated_subtotal + $shipping + $total_tax - $total_discount;

        // Update main purchase entry record
        $entry_data = [
            'tid' => $invocieno,
            'invoicedate' => $invoicedate ? datefordatabase($invoicedate) : null,
            'invoiceduedate' => $invocieduedate ? datefordatabase($invocieduedate) : null,
            'subtotal' => $calculated_subtotal,
            'shipping' => $shipping,
            'ship_tax' => $shipping_tax,
            'ship_tax_type' => $ship_taxtype,
            'total' => $calculated_total,
            'tax' => $total_tax,
            'discount' => $total_discount,
            'items' => $item_count,
            'notes' => $notes,
            'csd' => $customer_id,
            'taxstatus' => $tax,
            'discstatus' => $discstatus,
            'format_discount' => '%',
            'refer' => $refer,
            'term' => $pterms,
            'multi' => $currency
        ];

        // Debug: Log entry data before update
        error_log('EDITACTION_ENTRY DEBUG: Updating entry ID ' . $entry_id . ' with data: ' . print_r($entry_data, true));

        $this->db->where('id', $entry_id);
        $this->db->update('pos_purchase_entries', $entry_data);

        if (!$this->db->trans_status()) {
            $db_error = $this->db->error();
            $this->db->trans_rollback();
            $debug_data = json_encode($entry_data + ['product_count' => count($product_id), 'productlist_count' => count($productlist), 'db_error' => $db_error]);
            error_log('EDITACTION_ENTRY DEBUG: Failed to update purchase entry. DB Error: ' . json_encode($db_error));
            echo json_encode(['status' => 'Error', 'message' => 'Failed to update purchase entry record. DB Error: ' . ($db_error['message'] ?: 'Unknown error') . '. Data: ' . $debug_data]);
            return;
        }

        // Delete existing items and re-insert
        $this->db->delete('pos_purchase_invoice_items', array('tid' => $entry_id));

        if (!$this->db->trans_status()) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'Error', 'message' => 'Failed to delete existing items.']);
            return;
        }

        // Insert new items
        if (!empty($productlist)) {
            $this->db->insert_batch('pos_purchase_invoice_items', $productlist);
            error_log('EDITACTION_ENTRY DEBUG: Inserted ' . count($productlist) . ' product items');
        }

        if (!$this->db->trans_status()) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'Error', 'message' => 'Failed to insert new items.']);
            return;
        }

        // Debug: Check transaction status
        error_log('EDITACTION_ENTRY DEBUG: Transaction status: ' . ($this->db->trans_status() ? 'TRUE' : 'FALSE'));
        error_log('EDITACTION_ENTRY DEBUG: Last database error: ' . $this->db->error()['message']);

        if ($this->db->trans_status() === FALSE) {
            $db_error = $this->db->error();
            $debug_info = "Entry ID: $entry_id, Customer ID: $customer_id, Product count: " . count($product_id) . ", DB Error: " . ($db_error['message'] ?: 'None');
            $this->db->trans_rollback();
            echo json_encode(['status' => 'Error', 'message' => 'Database error occurred while updating purchase entry. Debug: ' . $debug_info]);
        } else {
            $this->db->trans_commit();
            error_log('EDITACTION_ENTRY DEBUG: Transaction committed successfully');
            echo json_encode(['status' => 'Success', 'message' =>
                "Purchase entry has been updated successfully! <a href='view_entry?id=$entry_id' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> View </a> "]);
        }
    }

}
