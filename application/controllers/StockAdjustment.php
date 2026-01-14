<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockAdjustment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->model('Products_model');
        $this->load->library('aauth');
    }

    // Show list of stock adjustments
    public function index() {
        $this->db->select('adjustment_no, reason, adjustment_date, status');
        $this->db->from('pos_stock_adjustments');
        $this->db->group_by('adjustment_no');
        $this->db->order_by('id', 'DESC');
        
        $data['adjustments'] = $this->db->get()->result_array();

        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Stock Adjustment List';

        $this->load->view('fixed/header', $head);
        $this->load->view('stock_adjustment/list', $data);
        $this->load->view('fixed/footer');
    }

    // Show create stock adjustment form
    public function create() {
        $data['products'] = $this->Products_model->get_all_products();
        $data['categories'] = $this->db->get_where('pos_product_cat', ['c_type' => 0])->result_array();
        $data['subcategories'] = $this->db->get_where('pos_product_cat', ['c_type' => 1])->result_array();
        $data['warehouses'] = $this->db->get('pos_warehouse')->result_array();
       
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Create Stock Adjustment';

        $this->load->view('fixed/header', $head);
        $this->load->view('stock_adjustment/create', $data);
        $this->load->view('fixed/footer');
    }
// Fetch categories by warehouse (AJAX)
public function get_categories() {
    $warehouse_id = $this->input->post('warehouse_id');
    $categories = $this->Products_model->get_categories_by_warehouse($warehouse_id);
    echo json_encode($categories);
}

// Fetch subcategories by category and warehouse (AJAX)
public function get_subcategories() {
    $category_id = $this->input->post('category_id');
    $warehouse_id = $this->input->post('warehouse_id'); // Make sure this is passed
    $subcategories = $this->Products_model->get_subcategories_by_category_warehouse($category_id, $warehouse_id);
    echo json_encode($subcategories);
}

// Fetch products by category or subcategory and warehouse (AJAX)

public function get_products() {
    $warehouse_id = $this->input->post('warehouse_id');
    $category_id = $this->input->post('category_id');
    $sub_category_id = $this->input->post('sub_category_id');

    if ($sub_category_id) {
        $products = $this->Products_model->get_products_by_subcategory_warehouse($sub_category_id, $warehouse_id);
    } elseif ($category_id) {
        $products = $this->Products_model->get_products_by_category_warehouse($category_id, $warehouse_id);
    } else {
        $products = [];
    }

    echo json_encode($products);
}

    
    // Save stock adjustment
    public function save() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $adjustment_reason = $this->input->post('adjustment_reason', true);
        $adjustment_date   = $this->input->post('adjustment_date', true);
        $status            = $this->input->post('status', true);
        $warehouse         = $this->input->post('warehouse', true);

        $product_ids       = $this->input->post('product_id', true);
        $adjustment_types  = $this->input->post('adjustment_type', true);
        $quantities        = $this->input->post('quantity', true);
        $notes             = $this->input->post('note', true);

        // Validate inputs
        if (!is_array($product_ids) || empty($product_ids)) {
            $this->session->set_flashdata('error', 'No products selected.');
            redirect('StockAdjustment/create');
            return;
        }

        $count = count($product_ids);
        if (
            count($adjustment_types) !== $count ||
            count($quantities) !== $count ||
            count($notes) !== $count
        ) {
            $this->session->set_flashdata('error', 'Input arrays length mismatch.');
            redirect('StockAdjustment/create');
            return;
        }

        if (empty($adjustment_reason)) {
            $this->session->set_flashdata('error', 'Adjustment reason is required.');
            redirect('StockAdjustment/create');
            return;
        }

        if (empty($warehouse) || (int)$warehouse <= 0) {
            $this->session->set_flashdata('error', 'A valid warehouse must be selected.');
            redirect('StockAdjustment/create');
            return;
        }

        $adjustment_date = (!empty($adjustment_date) && $adjustment_date !== 'null') ? $adjustment_date : date('Y-m-d');
        $status = (!empty($status)) ? $status : 'Active';
        $warehouse_id = (int)$warehouse;

        $timestamp = time();
        $adjustment_no = 'ADJ-' . $timestamp;

        $errors = [];

        $this->db->trans_start();

        foreach ($product_ids as $index => $product_id_raw) {
    $product_id = (int)$product_id_raw;
    $adjustment_type = isset($adjustment_types[$index]) ? $adjustment_types[$index] : '';
    $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
    $note = isset($notes[$index]) ? $notes[$index] : '';

            if (!$product_id) {
                $errors[] = "Invalid product selected at row " . ($index + 1);
                continue;
            }
            if (!in_array($adjustment_type, ['increment', 'decrement'])) {
                $errors[] = "Invalid adjustment type at row " . ($index + 1);
                continue;
            }
            if ($quantity <= 0) {
                $errors[] = "Quantity must be greater than zero at row " . ($index + 1);
                continue;
            }

            // Fetch product to verify existence
            $product = $this->db->get_where('pos_products', ['pid' => $product_id])->row();
            if (!$product) {
                $errors[] = "Product not found at row " . ($index + 1);
                continue;
            }

          // Fetch the product row for the selected warehouse
$product = $this->db->get_where('pos_products', [
    'pid' => $product_id,
    'warehouse' => $warehouse_id
])->row();

if ($product) {
    $current_stock = (int)$product->qty;
    $new_stock = ($adjustment_type === 'increment') ? $current_stock + $quantity : $current_stock - $quantity;

    if ($new_stock < 0) {
        $errors[] = "Stock cannot be negative at row " . ($index + 1);
        continue;
    }

            // Insert stock adjustment record
            // Insert stock adjustment record
    $adjustment_data = [
        'adjustment_no'    => $adjustment_no,
        'product_id'       => $product_id,
        'adjustment_type'  => $adjustment_type,
        'quantity'         => $quantity,
        'reason'           => $adjustment_reason,
        'note'             => $note,
        'adjustment_date'  => $adjustment_date,
        'status'           => $status,
        'warehouse'        => $warehouse_id,
    ];

    $inserted = $this->db->insert('pos_stock_adjustments', $adjustment_data);

    if (!$inserted) {
        $errors[] = "Failed to insert adjustment at row " . ($index + 1);
        continue;
    }

            
            // Update the qty in pos_products for this warehouse
            $this->db->where('pid', $product_id);
            $this->db->where('warehouse', $warehouse_id);
            $updated = $this->db->update('pos_products', ['qty' => $new_stock]);

            if (!$updated) {
                $errors[] = "Failed to update stock at row " . ($index + 1);
            }

            // Update parent stock if this is a variation
            $this->db->select('merge, sub');
            $this->db->from('pos_products');
            $this->db->where('pid', $product_id);
            $this->db->where('warehouse', $warehouse_id);
            $query = $this->db->get();
            $product_info = $query->row_array();
            if ($product_info && $product_info['merge'] == 1) {
                $this->Products_model->update_parent_total_stock_at_warehouse($product_info['sub'], $warehouse_id);
            }            // Update the qty in pos_products for this warehouse
    $this->db->where('pid', $product_id);
    $this->db->where('warehouse', $warehouse_id);
    $updated = $this->db->update('pos_products', ['qty' => $new_stock]);

    if (!$updated) {
        $errors[] = "Failed to update stock at row " . ($index + 1);
    }
} else {
    $errors[] = "Product not found for warehouse at row " . ($index + 1);
    continue;
}

            if (!$updated) {
                $errors[] = "Failed to update stock at row " . ($index + 1);
            }
        }

        $this->db->trans_complete();

        if (!empty($errors)) {
            $this->session->set_flashdata('error', implode("<br>", $errors));
        } else {
            $this->session->set_flashdata('success', 'Stock adjustments saved successfully.');
        }

        redirect('StockAdjustment/index');
    }

    // View adjustment details by adjustment number
    public function view($adjustment_no = null) {
        if (!$adjustment_no) {
            show_error('No adjustment number provided.');
        }

        $adjustment = $this->db->get_where('pos_stock_adjustments', ['adjustment_no' => $adjustment_no])->row_array();
        if (!$adjustment) {
            show_error('Adjustment not found.');
        }

        $company = $this->db->get('pos_system')->row_array();

        $this->db->select('psa.*, p.product_code, p.product_name, p.product_price, w.title AS warehouse_name');
        $this->db->from('pos_stock_adjustments psa');
        $this->db->join('pos_products p', 'psa.product_id = p.pid', 'left');
        $this->db->join('pos_warehouse w', 'psa.warehouse = w.id', 'left');
        $this->db->where('psa.adjustment_no', $adjustment_no);

        $adjusted_products = $this->db->get()->result_array();

        $data = [
            'company' => $company,
            'adjustment' => $adjustment,
            'adjusted_products' => $adjusted_products
        ];

        $head['title'] = 'Stock Adjustment Report - ' . $adjustment_no;
        $head['usernm'] = $this->aauth->get_user()->username;

        $this->load->view('fixed/header', $head);
        $this->load->view('stock_adjustment/view', $data);
        $this->load->view('fixed/footer');
    }

    
  
// Show the edit form
public function edit($adjustment_no = null) {
    if (!$adjustment_no) {
        show_404();
    }

    $header = $this->db->get_where('pos_stock_adjustments', ['adjustment_no' => $adjustment_no])->row_array();
    if (!$header) {
        $this->session->set_flashdata('error', 'Adjustment not found.');
        redirect('StockAdjustment/index');
        return;
    }

    $product_adjustments = $this->db->get_where('pos_stock_adjustments', ['adjustment_no' => $adjustment_no])->result_array();

    foreach ($product_adjustments as $k => $adj) {
    $product = $this->db->get_where('pos_products', [
        'pid' => $adj['product_id'],
        'warehouse' => $adj['warehouse']
    ])->row_array();

    $warehouse = $this->db->get_where('pos_warehouse', [
        'id' => $adj['warehouse']
    ])->row_array();

    $product_adjustments[$k]['code']  = $product['product_code'] ?? '';
    $product_adjustments[$k]['name']  = $product['product_name'] ?? '';
    $product_adjustments[$k]['stock'] = $product['qty'] ?? 0;
    $product_adjustments[$k]['warehouse_name'] = $warehouse['title'] ?? '';
}

    $data = [
        'header' => $header,
        'product_adjustments' => $product_adjustments
    ];

     $head['title'] = 'Edit Stock Adjustment';
    $head['usernm'] = $this->aauth->get_user()->username;

    $this->load->view('fixed/header', $head);
    $this->load->view('stock_adjustment/edit', $data);
    $this->load->view('fixed/footer');
}


public function update() {
    $original_quantities = $this->input->post('original_quantity');
$original_types = $this->input->post('original_adjustment_type');
    $adjustment_no = $this->input->post('adjustment_no');
    $adjustment_reason = $this->input->post('adjustment_reason');
    $product_ids = $this->input->post('product_id');
    $adjustment_types = $this->input->post('adjustment_type');
    $quantities = $this->input->post('quantity');

    if (!$adjustment_no || !is_array($product_ids)) {
        $this->session->set_flashdata('error', 'Invalid request.');
        redirect('StockAdjustment/edit/'.$adjustment_no);
        return;
    }

    $errors = [];
    $this->db->trans_start();

    foreach ($product_ids as $index => $product_id) {
        $product_id = (int)$product_id;
        $adjustment_type = $adjustment_types[$index];
        $quantity = (int)$quantities[$index];
        $original_quantity = (int)$original_quantities[$index];
$original_type = $original_types[$index];

// Only update if something changed
if ($quantity == $original_quantity && $adjustment_type == $original_type) {
    continue; // Skip unchanged products
}

        // Fetch the adjustment row to get warehouse
        $adj = $this->db->get_where('pos_stock_adjustments', [
            'adjustment_no' => $adjustment_no,
            'product_id' => $product_id
        ])->row_array();

        if (!$adj) {
            $errors[] = "Adjustment not found for product ID $product_id.";
            continue;
        }

        $warehouse_id = $adj['warehouse'];

        // Fetch the product row for the selected warehouse
        $product = $this->db->get_where('pos_products', [
            'pid' => $product_id,
            'warehouse' => $warehouse_id
        ])->row();

        if (!$product) {
            $errors[] = "Product not found for warehouse for product ID $product_id.";
            continue;
        }

        // Calculate new stock based on current stock in DB
$current_stock = (int)$product->qty;

// No need to revert old adjustment, because current stock already includes it

$new_stock = $current_stock; // Start from current stock

// Apply new adjustment only
if ($adjustment_type === 'increment') {
    $new_stock += $quantity;
} else {
    $new_stock -= $quantity;     
}


// Now apply the new adjustment
$new_stock = ($adjustment_type === 'increment') ? $current_stock + $quantity : $current_stock - $quantity;

        if ($new_stock < 0) {
            $errors[] = "Stock cannot be negative for product ID $product_id.";
            continue;
        }

        // Update product stock
        $this->db->where('pid', $product_id);
        $this->db->where('warehouse', $warehouse_id);
        $this->db->update('pos_products', ['qty' => $new_stock]);

        // Update parent stock if this is a variation
        $this->db->select('merge, sub');
        $this->db->from('pos_products');
        $this->db->where('pid', $product_id);
        $this->db->where('warehouse', $warehouse_id);
        $query = $this->db->get();
        $product_info = $query->row_array();
        if ($product_info && $product_info['merge'] == 1) {
            $this->Products_model->update_parent_total_stock_at_warehouse($product_info['sub'], $warehouse_id);
        }

        // Update adjustment record
        $this->db->where('adjustment_no', $adjustment_no);
        $this->db->where('product_id', $product_id);
        $this->db->update('pos_stock_adjustments', [
            'adjustment_type' => $adjustment_type,
            'quantity' => $quantity,
            'reason' => $adjustment_reason
        ]);
    }

    $this->db->trans_complete();

    if (!empty($errors)) {
        $this->session->set_flashdata('error', implode("<br>", $errors));
    } else {
        $this->session->set_flashdata('success', 'Stock adjustment updated successfully.');
    }
    redirect('StockAdjustment/index');
}

public function delete($adjustment_no = null) {
    if (!$adjustment_no) {
        show_404();
    }

    // Optionally, revert stock changes here if you want to undo the adjustment effect
    $adjustments = $this->db->get_where('pos_stock_adjustments', ['adjustment_no' => $adjustment_no])->result_array();
foreach ($adjustments as $adj) {
    $product = $this->db->get_where('pos_products', [
        'pid' => $adj['product_id'],
        'warehouse' => $adj['warehouse']
    ])->row();
    if ($product) {
        $current_stock = (int)$product->qty;
        if ($adj['adjustment_type'] === 'increment') {
            $new_stock = $current_stock - $adj['quantity'];
        } else {
            $new_stock = $current_stock + $adj['quantity'];
        }
        $this->db->where('pid', $adj['product_id']);
        $this->db->where('warehouse', $adj['warehouse']);
        $this->db->update('pos_products', ['qty' => $new_stock]);

        // Update parent stock if this is a variation
        $this->db->select('merge, sub');
        $this->db->from('pos_products');
        $this->db->where('pid', $adj['product_id']);
        $this->db->where('warehouse', $adj['warehouse']);
        $query = $this->db->get();
        $product_info = $query->row_array();
        if ($product_info && $product_info['merge'] == 1) {
            $this->Products_model->update_parent_total_stock_at_warehouse($product_info['sub'], $adj['warehouse']);
        }
    }
}
    // Delete all adjustment rows for this adjustment_no
    $this->db->where('adjustment_no', $adjustment_no);
    $this->db->delete('pos_stock_adjustments');

    $this->session->set_flashdata('success', 'Stock adjustment deleted successfully.');
    redirect('StockAdjustment/index');
}

}
