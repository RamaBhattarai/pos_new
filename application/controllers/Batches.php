<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batches extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(11)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $this->li_a = 'stock';
    }

    // Main batch listing page
    public function index()
    {
        $head['title'] = "Batch Management";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('batches/list');
        $this->load->view('fixed/footer');
    }

    // AJAX endpoint for batch listing (DataTables)
    public function ajax_list()
    {
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        
        // Base query
        $this->db->select('
            pb.id,
            pb.batch_no,
            p.product_name,
            p.product_code,
            pb.qty,
            pb.expiry_date,
            pb.purchase_price,
            pb.selling_price,
            w.title as warehouse_name,
            pc.title as category_name,
            CASE 
                WHEN pb.expiry_date < CURDATE() THEN "Expired"
                WHEN pb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN "Expiring Soon"
                WHEN pb.qty <= 0 THEN "Out of Stock"
                ELSE "Active"
            END as status
        ');
        
        $this->db->from('pos_product_batches pb');
        $this->db->join('pos_products p', 'p.pid = pb.product_id', 'left');
        $this->db->join('pos_warehouse w', 'w.id = pb.warehouse_id', 'left');
        $this->db->join('pos_product_cat pc', 'pc.id = p.pcat', 'left');
        
        // User location restrictions
        if ($this->aauth->get_user()->loc) {
            $this->db->where('w.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('w.loc', 0);
        }
        
        // Search functionality
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('pb.batch_no', $search);
            $this->db->or_like('p.product_name', $search);
            $this->db->or_like('p.product_code', $search);
            $this->db->or_like('w.title', $search);
            $this->db->group_end();
        }
        
        // Count total records
        $total_query = clone $this->db;
        $total_records = $total_query->count_all_results('', FALSE);
        
        // Apply ordering
        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        $columns = ['pb.id', 'pb.batch_no', 'p.product_name', 'pb.qty', 'p.product_code', 'pc.title', 'w.title', 'pb.expiry_date', 'pb.selling_price', 'status'];
        
        if (isset($columns[$order_column])) {
            $this->db->order_by($columns[$order_column], $order_dir);
        }
        
        // Apply pagination
        $this->db->limit($length, $start);
        
        $query = $this->db->get();
        $data = [];
        $counter = $start + 1;
        
        foreach ($query->result_array() as $row) {
            $actions = '
                <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm" onclick="viewBatch(' . $row['id'] . ')" title="View Batch">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="editBatch(' . $row['id'] . ')" title="Edit Batch">
                        <i class="fa fa-edit"></i>
                    </button>
                </div>
            ';
            
            $status_class = '';
            switch($row['status']) {
                case 'Expired': $status_class = 'batch-status-expired'; break;
                case 'Expiring Soon': $status_class = 'batch-status-expiring'; break;
                case 'Out of Stock': $status_class = 'batch-status-out-of-stock'; break;
                default: $status_class = 'batch-status-active';
            }
            
            $data[] = [
                'DT_RowIndex' => $counter++,
                'batch_no' => $row['batch_no'],
                'product_name' => $row['product_name'],
                'qty' => number_format($row['qty'], 2),
                'product_code' => $row['product_code'],
                'category_name' => $row['category_name'],
                'warehouse_name' => $row['warehouse_name'],
                'expiry_date' => $row['expiry_date'] && $row['expiry_date'] !== '0000-00-00' ? $row['expiry_date'] : 'N/A',
                'selling_price' => $row['selling_price'],
                'status' => $row['status'],
                'actions' => $actions
            ];
        }
        
        $output = [
            "draw" => intval($draw),
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $data
        ];
        
        echo json_encode($output);
    }

    // View single batch details
    public function view($batch_id)
    {
        $this->db->select('
            pb.*,
            p.product_name,
            p.product_code,
            w.title as warehouse_name,
            pc.title as category_name
        ');
        $this->db->from('pos_product_batches pb');
        $this->db->join('pos_products p', 'p.pid = pb.product_id', 'left');
        $this->db->join('pos_warehouse w', 'w.id = pb.warehouse_id', 'left');
        $this->db->join('pos_product_cat pc', 'pc.id = p.pcat', 'left');
        $this->db->where('pb.id', $batch_id);
        
        $data['batch'] = $this->db->get()->row_array();
        
        if (!$data['batch']) {
            show_404();
            return;
        }
        
        $head['title'] = "Batch Details - " . $data['batch']['batch_no'];
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('batches/view', $data);
        $this->load->view('fixed/footer');
    }

    // Edit batch details
    public function edit($batch_id)
    {
        if ($this->input->post()) {
            // Get current batch data before updating
            $this->db->select('qty, product_id');
            $this->db->where('id', $batch_id);
            $current_batch = $this->db->get('pos_product_batches')->row();
            
            $old_qty = $current_batch->qty;
            $new_qty = numberClean($this->input->post('qty'));
            $qty_difference = $new_qty - $old_qty;
            
            $data = [
                'batch_no' => $this->input->post('batch_no'),
                'expiry_date' => datefordatabase($this->input->post('expiry_date')),
                'qty' => $new_qty,
                'selling_price' => numberClean($this->input->post('selling_price'))
            ];
            
            $this->db->trans_start();
            
            // Update the batch
            $this->db->where('id', $batch_id);
            $update_result = $this->db->update('pos_product_batches', $data);
            
            if ($update_result) {
                // Update the main product stock
                if ($qty_difference != 0) {
                    $this->db->set('qty', "qty+$qty_difference", FALSE);
                    $this->db->where('pid', $current_batch->product_id);
                    $this->db->update('pos_products');
                    
                    // Check if this is a variant product and update parent stock
                    $this->db->select('merge, sub');
                    $this->db->where('pid', $current_batch->product_id);
                    $product_info = $this->db->get('pos_products')->row();
                    
                    if ($product_info && $product_info->merge == 1) {
                        // Load the products model and update parent total stock
                        $this->load->model('products_model');
                        $parent_update_result = $this->products_model->update_parent_total_stock($product_info->sub);
                        
                        // If parent update fails, rollback the entire transaction
                        if (!$parent_update_result) {
                            $this->db->trans_rollback();
                            echo json_encode(['status' => 'Error', 'message' => 'Failed to update parent product stock']);
                            return;
                        }
                    }
                }
                
                $this->db->trans_complete();
                echo json_encode(['status' => 'Success', 'message' => 'Batch updated successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['status' => 'Error', 'message' => 'Failed to update batch']);
            }
        } else {
            // Get batch details for editing
            $this->db->select('pb.*, p.product_name, w.title as warehouse_name');
            $this->db->from('pos_product_batches pb');
            $this->db->join('pos_products p', 'p.pid = pb.product_id', 'left');
            $this->db->join('pos_warehouse w', 'w.id = pb.warehouse_id', 'left');
            $this->db->where('pb.id', $batch_id);
            $batch = $this->db->get()->row_array();
            
            echo json_encode(['batch' => $batch]);
        }
    }
}