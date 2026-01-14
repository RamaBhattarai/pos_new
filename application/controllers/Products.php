<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
    
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library("Aauth");

        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(2)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->load->model('products_model', 'products');
        $this->load->model('categories_model');
        $this->load->library("Custom");
        $this->li_a = 'stock';

    }

    public function index()
    {
        $head['title'] = "Products";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/products');
        $this->load->view('fixed/footer');
    }

    public function cat()
    {
        $head['title'] = "Product Categories";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/cat_productlist');
        $this->load->view('fixed/footer');

    }


    public function add()
    {
        $data['cat'] = $this->categories_model->category_list();
        $data['units'] = $this->products->units();
        $data['warehouse'] = $this->categories_model->warehouse_list();
        $data['custom_fields'] = $this->custom->add_fields(4);
        $this->load->model('units_model', 'units');
        $data['variables'] = $this->units->variables_list();
        $data['options'] = $this->units->options_list();  // New: get options (formerly variations)
        $head['title'] = "Add Product";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/product-add', $data);
        $this->load->view('fixed/footer');
    }


    public function product_list()
    {
        $catid = $this->input->get('id');
        $sub = $this->input->get('sub');

        $stock = $this->input->post('stock'); // Get stock filter from AJAX POST

    $filter = [];
    if ($stock == 'out') {
        $filter['qty <='] = 0;
    }

    if ($catid > 0) {
        $list = $this->products->get_datatables($catid, '', $sub, $filter);
    } else {
        $list = $this->products->get_datatables(null, '', null, $filter);
    }
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $row[] = $no;
            $pid = $prd->pid;

// // If image is default.png, use deskgoo.png instead
// $image = ($prd->image === 'default.png') ? 'deskgoo.png' : $prd->image;

// $imageUrl = base_url() . 'userfiles/product/thumbnail/' . $image;

// $row[] = '<a href="#" data-object-id="' . $pid . '" class="view-object">
//     <span class="avatar-lg align-baseline">
//         <img style="max-width: 100px" src="' . $imageUrl . '" alt="Product Image">
//     </span>&nbsp;' . $prd->product_name . '
// </a>';

//             // Replace default.png with deskgoo.png
// $image = ($prd->image === 'default.png') ? 'deskgoo.png' : $prd->image;
            // Use default image if product image is empty or null  
            $productImage = (!empty($prd->image) && $prd->image !== null) ? $prd->image : 'deskgoo.png';
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="view-object"><span class="avatar-lg align-baseline"><img style="max-width: 80px" src="' . base_url() . 'userfiles/product/thumbnail/' . $productImage . '" ></span>&nbsp;' . $prd->product_name . '</a>';
            $row[] = +$prd->qty;
            $row[] = $prd->product_code;
            $row[] = $prd->c_title;
            $row[] = $prd->title;
            $row[] = amountExchange($prd->product_price, 0, $this->aauth->get_user()->loc);
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success  btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> 
<div class="btn-group">
                                    <button type="button" class="btn btn-indigo dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i>  ' . $this->lang->line('Print') . '</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . base_url() . 'products/barcode?id=' . $pid . '" target="_blank"> ' . $this->lang->line('BarCode') . '</a><div class="dropdown-divider"></div> <a class="dropdown-item" href="' . base_url() . 'products/posbarcode?id=' . $pid . '" target="_blank"> ' . $this->lang->line('BarCode') . ' - Compact</a> <div class="dropdown-divider"></div>
                                             <a class="dropdown-item" href="' . base_url() . 'products/label?id=' . $pid . '" target="_blank"> ' . $this->lang->line('Product') . ' Label</a><div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/poslabel?id=' . $pid . '" target="_blank"> Label - Compact</a></div></div><a class="btn btn-pink  btn-sm" href="' . base_url() . 'products/report_product?id=' . $pid . '" target="_blank"> <span class="fa fa-pie-chart"></span> ' . $this->lang->line('Reports') . '</a> <div class="btn-group">
                                    <button type="button" class="btn btn btn-primary dropdown-toggle   btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-cog"></i>  </button>
                                    <div class="dropdown-menu">
&nbsp;<a href="' . base_url() . 'products/edit?id=' . $pid . '"  class="btn btn-purple btn-sm"><span class="fa fa-edit"></span>' . $this->lang->line('Edit') . '</a><div class="dropdown-divider"></div>&nbsp;<a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span>' . $this->lang->line('Delete') . '</a>
                                    </div>
                                </div>';
            $data[] = $row;
        }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->products->count_all($catid, '', $sub),
            "recordsFiltered" => $this->products->count_filtered($catid, '', $sub),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function addproduct()
    {
        if (!$this->aauth->premission(10)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }

        // Debug: Log all POST data
        error_log("ADDPRODUCT DEBUG: POST data received: " . print_r($_POST, true));

        $product_name = $this->input->post('product_name', true);
        $catid = $this->input->post('product_cat');
        $warehouse = $this->input->post('product_warehouse');
        $product_code = $this->input->post('product_code');
        $product_price = numberClean($this->input->post('product_price'));
        $factoryprice = numberClean($this->input->post('fproduct_price'));
        $taxrate = numberClean($this->input->post('product_tax', true));
        $disrate = numberClean($this->input->post('product_disc', true));
        $product_qty = numberClean($this->input->post('product_qty', true));
        $product_qty_alert = numberClean($this->input->post('product_qty_alert'));
        $product_desc = $this->input->post('product_desc', true);
        $image = $this->input->post('image');
        $unit = $this->input->post('unit', true);
        $barcode = $this->input->post('barcode');
        $v_type = $this->input->post('v_type');
        $v_stock = $this->input->post('v_stock');
        $v_alert = $this->input->post('v_alert');
        $w_type = $this->input->post('w_type');
        $w_stock = $this->input->post('w_stock');
        $w_alert = $this->input->post('w_alert');
        $wdate = datefordatabase($this->input->post('wdate'));
        $code_type = $this->input->post('code_type');
        $sub_cat = $this->input->post('sub_cat');
        $brand = $this->input->post('brand');
        $serial = $this->input->post('product_serial');
        if ($catid) {
            // Handle new variation system - check if variations are present
            $variant_ids = $this->input->post('variant_id');
            $variant_prices = $this->input->post('variant_price');
            $variant_stocks = $this->input->post('variant_stock');

            // Check if there are actually valid variations (not just empty form fields)
            $has_valid_variations = false;
            if ($variant_ids && is_array($variant_ids)) {
                foreach ($variant_ids as $index => $variant_id) {
                    if (!empty($variant_id) && isset($variant_stocks[$index]) && $variant_stocks[$index] > 0) {
                        $has_valid_variations = true;
                        break;
                    }
                }
            }

            // If variations are present, parent product should have 0 stock (stock managed by variations)
            if ($has_valid_variations) {
                $product_qty = 0;
            }

            $result = $this->products->addnew($catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $v_type, $v_stock, $v_alert, $wdate, $code_type, $w_type, $w_stock, $w_alert, $sub_cat, $brand, $serial, true);
            
            if ($result['pid']) {
                $product_id = $result['pid'];

                // Handle new variation system
                if ($has_valid_variations) {
                    error_log("ADDPRODUCT DEBUG: Processing " . count($variant_ids) . " variations");
                    foreach ($variant_ids as $index => $variant_id) {
                        $price = numberClean($variant_prices[$index]);
                        $stock = numberClean($variant_stocks[$index]);

                        error_log("ADDPRODUCT DEBUG: Processing variation $index - variant_id: $variant_id, price: $price, stock: $stock");

                        if ($price > 0 && $stock >= 0 && !empty($variant_id)) {
                            // Get parent product details
                            $this->db->select('*');
                            $this->db->where('pid', $product_id);
                            $parent_product = $this->db->get('pos_products')->row_array();

                            // Get variation name
                            $this->db->select('name');
                            $this->db->where('id', $variant_id);
                            $this->db->where('level_type', 3);
                            $this->db->where('parent_id', 0);
                            $variation_query = $this->db->get('pos_units');
                            $variation_name = $variation_query->row()->name;

                            // Shorten variation name by removing prefixes
                            $variation_name = str_replace(['Capacity ', 'Size ', 'color '], '', $variation_name);

                            // Get attributes for option_id and variation_option_id
                            $this->db->select('option_id, option_value_id');
                            $this->db->where('variation_id', $variant_id);
                            $attributes = $this->db->get('pos_variation_attributes')->result_array();
                            $option_id = isset($attributes[0]['option_id']) ? $attributes[0]['option_id'] : 0;
                            $variation_option_id = isset($attributes[0]['option_value_id']) ? $attributes[0]['option_value_id'] : 0;

                            // Create variation product name and code
                            $full_variation_name = $parent_product['product_name'] . '-' . $variation_name;
                            
                            // Generate variation code from variation name (e.g., "Blue Large" -> "BL")
                            $variation_parts = preg_split('/[\s\-\+\&]+/', $variation_name);
                            $variation_short = '';
                            foreach ($variation_parts as $part) {
                                if (strlen(trim($part)) > 0) {
                                    $variation_short .= strtoupper(substr(trim($part), 0, 1));
                                }
                            }
                            // Limit to 4 characters max
                            $variation_short = substr($variation_short, 0, 4);
                            $variation_code = $parent_product['product_code'] . '-' . $variation_short;

                            error_log("ADDPRODUCT DEBUG: Creating variation: $full_variation_name with code: $variation_code");

                            // Insert variation product
                            $variation_data = array(
                                'product_name' => $full_variation_name,
                                'pcat' => $parent_product['pcat'],
                                'warehouse' => $parent_product['warehouse'],
                                'product_code' => $variation_code,
                                'product_price' => $price,
                                'fproduct_price' => $price,
                                'qty' => $stock,
                                'alert' => 0, // Default alert
                                'merge' => 1,
                                'sub' => $product_id,
                                'vb' => 0,
                                'option_id' => $option_id,
                                'variation_option_id' => $variation_option_id,
                                'variation_id' => $variant_id,
                                'unit' => $parent_product['unit'],
                                'taxrate' => $parent_product['taxrate'],
                                'disrate' => $parent_product['disrate'],
                                'barcode' => rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9),
                                'code_type' => 'EAN13',
                                'expiry' => '2028-12-31',
                                'expiry_alert_seen' => 0,
                                'sub_id' => 0,
                                'b_id' => 0
                            );

                            if ($this->db->insert('pos_products', $variation_data)) {
                                $variation_pid = $this->db->insert_id();
                                error_log("ADDPRODUCT DEBUG: Variation created with PID: $variation_pid");
                                // Log stock movement
                                $this->products->movers(1, $variation_pid, $stock, 0, 'Variation Stock Initialized');
                            } else {
                                error_log("ADDPRODUCT DEBUG: Failed to insert variation");
                            }
                        } else {
                            error_log("ADDPRODUCT DEBUG: Skipping variation $index - invalid data");
                        }
                    }

                    // Update parent product total stock from all variations
                    $this->products->update_parent_total_stock($product_id);
                    error_log("ADDPRODUCT DEBUG: Updated parent stock for product $product_id");
                } else {
                    error_log("ADDPRODUCT DEBUG: No variation data found or not an array");
                }

                // Return success response
                echo json_encode($result['response']);
            } else {
                // Return error response
                echo json_encode($result['response']);
            }
        }
    }

    public function delete_i()
    {
        if ($this->aauth->premission(11)) {
            $id = $this->input->post('deleteid');
            if ($id) {
                $this->db->delete('pos_products', array('pid' => $id));
                $this->db->delete('pos_products', array('sub' => $id, 'merge' => 1));
                $this->db->delete('pos_movers', array('d_type' => 1, 'rid1' => $id));
                $this->db->set('merge', 0);
                $this->db->where('sub', $id);
                $this->db->update('pos_products');
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
            } else {
                echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }
    }

    public function edit()
    {
        if (!$this->aauth->premission(14)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $pid = $this->input->get('id');
        $this->db->select('*');
        $this->db->from('pos_products');
        $this->db->where('pid', $pid);
        $query = $this->db->get();
        $data['product'] = $query->row_array();
        if ($data['product']['merge'] > 0) {
            $this->db->select('*');
            $this->db->from('pos_products');
            $this->db->where('merge', 1);
            $this->db->where('sub', $pid);
            $query = $this->db->get();
            $data['product_var'] = $query->result_array();
            $this->db->select('*');
            $this->db->from('pos_products');
            $this->db->where('merge', 2);
            $this->db->where('sub', $pid);
            $query = $this->db->get();
            $data['product_ware'] = $query->result_array();
        }


        $data['units'] = $this->products->units();
        $data['serial_list'] = $this->products->serials($data['product']['pid']);
        $data['cat_ware'] = $this->categories_model->cat_ware($pid);
        $data['cat_sub'] = $this->categories_model->sub_cat_curr($data['product']['sub_id']);
        $data['cat_sub_list'] = $this->categories_model->sub_cat_list($data['product']['pcat']);
        $data['warehouse'] = $this->categories_model->warehouse_list();
        $data['cat'] = $this->categories_model->category_list();
        $data['custom_fields'] = $this->custom->view_edit_fields($pid, 4);
        $head['title'] = "Edit Product";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->model('units_model', 'units');
        $data['variables'] = $this->units->variables_list();
        $data['options'] = $this->units->options_list();  // New: get options
        $this->load->view('fixed/header', $head);
        $this->load->view('products/product-edit', $data);
        $this->load->view('fixed/footer');

    }

    public function editproduct()
    {
        if (!$this->aauth->premission(14)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $pid = $this->input->post('pid');

        // Fetch product data to check if it's a variation
        $this->db->select('*');
        $this->db->from('pos_products');
        $this->db->where('pid', $pid);
        $query = $this->db->get();
        $product_data = $query->row_array();
        $product_name = $this->input->post('product_name', true);
        $catid = $this->input->post('product_cat');
        $warehouse = $this->input->post('product_warehouse');
        $product_code = $this->input->post('product_code');
        $product_price = numberClean($this->input->post('product_price'));
        $factoryprice = numberClean($this->input->post('fproduct_price'));
        $taxrate = numberClean($this->input->post('product_tax'));
        $disrate = numberClean($this->input->post('product_disc'));
        $product_qty = numberClean($this->input->post('product_qty'));
        $product_qty_alert = numberClean($this->input->post('product_qty_alert'));
        $product_desc = $this->input->post('product_desc', true);
        $image = $this->input->post('image');
        $unit = $this->input->post('unit');
        $barcode = $this->input->post('barcode');
        $code_type = $this->input->post('code_type');
        $sub_cat = $this->input->post('sub_cat');
        if (!$sub_cat) $sub_cat = 0;
        $brand = $this->input->post('brand');
        $vari = array();
        $vari['v_type'] = $this->input->post('v_type');
        $vari['v_stock'] = $this->input->post('v_stock');
        $vari['v_alert'] = $this->input->post('v_alert');
        $vari['w_type'] = $this->input->post('w_type');
        $vari['w_stock'] = $this->input->post('w_stock');
        $vari['w_alert'] = $this->input->post('w_alert');
        $serial = array();
        $serial['new'] = $this->input->post('product_serial');
        $serial['old'] = $this->input->post('product_serial_e');
		$wdate = datefordatabase($this->input->post('wdate'));
		$wdate =substr($wdate,0,10);
		//print_r($wdate);
        
        // Handle new variation system for edit
        $variant_ids = $this->input->post('variation_id');
        $variant_prices = $this->input->post('variation_price');
        $variant_stocks = $this->input->post('variation_stock');
        
        // Check if there are actually valid variations (not just empty form fields)
        $has_valid_variations = false;
        if ($variant_ids && is_array($variant_ids)) {
            foreach ($variant_ids as $index => $variant_id) {
                if (!empty($variant_id) && isset($variant_stocks[$index]) && $variant_stocks[$index] > 0) {
                    $has_valid_variations = true;
                    break;
                }
            }
        }
        
        if ($has_valid_variations) {
            // If variations are present, parent product should have 0 stock (stock managed by variations)
            $product_qty = 0;
        }
        
        if ($has_valid_variations) {
            // Process new variations for the product
            foreach ($variant_ids as $index => $variant_id) {
                $price = numberClean($variant_prices[$index]);
                $stock = numberClean($variant_stocks[$index]);
                
                if ($price > 0 && $stock >= 0 && !empty($variant_id)) {
                    // Get parent product details
                    $this->db->select('*');
                    $this->db->where('pid', $pid);
                    $parent_product = $this->db->get('pos_products')->row_array();
                    
                    // Get variation name
                    $this->db->select('name');
                    $this->db->where('id', $variant_id);
                    $this->db->where('level_type', 3);
                    $this->db->where('parent_id', 0);
                    $variation_query = $this->db->get('pos_units');
                    $variation_name = $variation_query->row()->name;

                    // Shorten variation name by removing prefixes
                    $variation_name = str_replace(['Capacity ', 'Size ', 'color '], '', $variation_name);
                    
                    // Get attributes for option_id and variation_option_id
                    $this->db->select('option_id, option_value_id');
                    $this->db->where('variation_id', $variant_id);
                    $attributes = $this->db->get('pos_variation_attributes')->result_array();
                    $option_id = isset($attributes[0]['option_id']) ? $attributes[0]['option_id'] : 0;
                    $variation_option_id = isset($attributes[0]['option_value_id']) ? $attributes[0]['option_value_id'] : 0;
                    
                    // Create variation product name and code
                    $full_variation_name = $parent_product['product_name'] . '-' . $variation_name;
                    
                    // Generate variation code from variation name (e.g., "Blue Large" -> "BL")
                    $variation_parts = preg_split('/[\s\-\+\&]+/', $variation_name);
                    $variation_short = '';
                    foreach ($variation_parts as $part) {
                        if (strlen(trim($part)) > 0) {
                            $variation_short .= strtoupper(substr(trim($part), 0, 1));
                        }
                    }
                    // Limit to 4 characters max
                    $variation_short = substr($variation_short, 0, 4);
                    $variation_code = $parent_product['product_code'] . '-' . $variation_short;
                    
                    // Insert variation product
                    $variation_data = array(
                        'product_name' => $full_variation_name,
                        'pcat' => $parent_product['pcat'],
                        'warehouse' => $parent_product['warehouse'],
                        'product_code' => $variation_code,
                        'product_price' => $price,
                        'fproduct_price' => $price,
                        'qty' => $stock,
                        'alert' => 0,
                        'merge' => 1,
                        'sub' => $pid,
                        'vb' => 0,
                        'option_id' => $option_id,
                        'variation_option_id' => $variation_option_id,
                        'variation_id' => $variant_id,
                        'unit' => $parent_product['unit'],
                        'taxrate' => $parent_product['taxrate'],
                        'disrate' => $parent_product['disrate'],
                        'barcode' => rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9),
                        'code_type' => 'EAN13',
                        'expiry' => '2028-12-31',
                        'expiry_alert_seen' => 0,
                        'sub_id' => 0,
                        'b_id' => 0
                    );
                    
                    if ($this->db->insert('pos_products', $variation_data)) {
                        $variation_pid = $this->db->insert_id();
                        // Log stock movement
                        $this->products->movers(1, $variation_pid, $stock, 0, 'Variation Stock Initialized');
                    }
                }
            }
            
            // Update parent product total stock
            $this->update_parent_total_stock($pid);
        }
        
        if ($pid) {
            $this->products->edit($pid, $catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $code_type, $sub_cat, $brand, $vari, $serial,$wdate);
        }

        // Update parent stock if editing a variation
        if ($product_data['merge'] == 1) {
            $this->update_parent_total_stock($product_data['sub']);
        }

        // Update parent stock if it has variations
        $this->db->where('sub', $pid);
        $this->db->where('merge', 1);
        if ($this->db->count_all_results('pos_products') > 0) {
            $this->update_parent_total_stock($pid);
        }
    }


    public function warehouseproduct_list()
    {
        $catid = $this->input->get('id');
        $list = $this->products->get_datatables($catid, true);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $row[] = $no;
            $pid = $prd->pid;
            $name = $prd->product_name;
            if ($prd->variant_count > 0) {
                $name .= ' (' . $prd->variant_count . ' variants)';
            }
            $row[] = $name;
            $row[] = +$prd->qty;
            $row[] = $prd->product_code;
            $row[] = $prd->c_title;
            $row[] = amountExchange($prd->product_price, 0, $this->aauth->get_user()->loc);
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> <a href="' . base_url() . 'products/edit?id=' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a> <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>';
            $data[] = $row;
        }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->products->count_all($catid, true),
            "recordsFiltered" => $this->products->count_filtered($catid, true),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function prd_stats()
    {
        $this->products->prd_stats();
    }

    public function stock_transfer_products()
    {
        $wid = $this->input->get('wid');
        $customer = $this->input->post('product');
        $terms = @$customer['term'];
        $result = $this->products->products_list($wid, $terms);
        echo json_encode($result);
    }

    public function sub_cat()
    {
        $wid = $this->input->get('id');
        $string = $this->input->post('product');


        if(isset($string['term'])) $this->db->like('title', $string['term']);
        $this->db->from('pos_product_cat');
        $this->db->where('rel_id', $wid);
        $this->db->where('c_type', 1);
        $query = $this->db->get();
        $result = $query->result_array();


        echo json_encode($result);
    }

    public function stock_transfer()
    {
        if ($this->input->post()) {
            $products_l = $this->input->post('products_l');
            $from_warehouse = $this->input->post('from_warehouse');
            $to_warehouse = $this->input->post('to_warehouse');
            $qty = $this->input->post('products_qty');
            $this->products->transfer($from_warehouse, $products_l, $to_warehouse, $qty);
        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Stock Transfer";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/stock_transfer', $data);
            $this->load->view('fixed/footer');
        }
    }


    public function file_handling()
    {
        if ($this->input->get('op')) {
            $name = $this->input->get('name');
            if ($this->products->meta_delete($name)) {
                echo json_encode(array('status' => 'Success'));
            }
        } else {
            $id = $this->input->get('id');
            $this->load->library("Uploadhandler_generic", array(
                'accept_file_types' => '/\.(gif|jpeg|jpg|png|jpe?g)$/i',  // More explicit
				'upload_dir' => FCPATH . 'userfiles/product/',
				'upload_url' => base_url() . 'userfiles/product/',
				'max_file_size' => 10485760, // 10MB (10 * 1024 * 1024 bytes)
				'max_width'=>4096,  // Increased from 2024 to 4096 pixels
				'max_height'=>4096, // Increased from 2024 to 4096 pixels
                'image_quality'=> 90     // Optional: Maintains quality after resizing
            ));
        }
    }

    public function barcode()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,barcode,code_type');
            $this->db->from('pos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();
            $data['name'] = $resultz['product_name'];
            $data['code'] = $resultz['barcode'];
            $data['ctype'] = $resultz['code_type'];
            $html = $this->load->view('barcode/view', $data, true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($data['name'] . '_barcode.pdf', 'I');
        }
    }

    public function posbarcode()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,barcode,code_type');
            $this->db->from('pos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();
            $data['name'] = $resultz['product_name'];
            $data['code'] = $resultz['barcode'];
            $data['ctype'] = $resultz['code_type'];
            $html = $this->load->view('barcode/posbarcode', $data, true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_thermal();
            $pdf->WriteHTML($html);
            $pdf->Output($data['name'] . '_barcode.pdf', 'I');

        }
    }

    public function view_over()
    {
        $pid = $this->input->post('id');
        $this->db->select('pos_products.*,pos_warehouse.title');
        $this->db->from('pos_products');
        $this->db->where('pos_products.pid', $pid);
        $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('pos_warehouse.loc', 0);
        }

        $query = $this->db->get();
        $data['product'] = $query->row_array();

        // Fetch variations with hierarchical information (Option → Option Value → Variant)
        $this->db->select('pos_products.*,pos_warehouse.title, 
                          u1.name as option_name, 
                          u2.name as option_value_name, 
                          u3.name as variant_name');
        $this->db->from('pos_products');
        $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
        $this->db->join('pos_units u1', 'pos_products.option_id = u1.id', 'left');
        $this->db->join('pos_units u2', 'pos_products.variation_option_id = u2.id', 'left');
        $this->db->join('pos_units u3', 'pos_products.variation_id = u3.id', 'left');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('pos_warehouse.loc', 0);
        }
        $this->db->where('pos_products.merge', 1);
        $this->db->where('pos_products.sub', $pid);
        $query = $this->db->get();
        $data['product_variations'] = $query->result_array();

        $this->db->select('pos_products.*,pos_warehouse.title');
        $this->db->from('pos_products');
        $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('pos_warehouse.loc', 0);
        }
        $this->db->where('pos_products.sub', $pid);
        $this->db->where('pos_products.merge', 2);
        $query = $this->db->get();
        $data['product_warehouse'] = $query->result_array();


        $this->load->view('products/view-over', $data);


    }


    public function label()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,product_price,product_code,barcode,expiry,code_type');
            $this->db->from('pos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();

            $html = $this->load->view('barcode/label', array('lab' => $resultz), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');

        }
    }


    public function poslabel()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,product_price,product_code,barcode,expiry,code_type');
            $this->db->from('pos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();
            $html = $this->load->view('barcode/poslabel', array('lab' => $resultz), true);
            ini_set('memory_limit', '64M');
            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_thermal();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');
        }
    }

    public function report_product()
    {
        $pid = intval($this->input->post('id'));

        $r_type = intval($this->input->post('r_type'));
        $s_date = datefordatabase($this->input->post('s_date'));
        $e_date = datefordatabase($this->input->post('e_date'));

        if ($pid && $r_type) {


            switch ($r_type) {
                case 1 :
                    $query = $this->db->query("SELECT pos_invoices.tid,pos_invoice_items.qty,pos_invoice_items.price,pos_invoices.invoicedate FROM pos_invoice_items LEFT JOIN pos_invoices ON pos_invoices.id=pos_invoice_items.tid WHERE pos_invoice_items.pid='$pid' AND pos_invoices.status!='canceled' AND (DATE(pos_invoices.invoicedate) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;

                case 2 :
                    $query = $this->db->query("SELECT pos_purchase.tid,pos_purchase_items.qty,pos_purchase_items.price,pos_purchase.invoicedate FROM pos_purchase_items LEFT JOIN pos_purchase ON pos_purchase.id=pos_purchase_items.tid WHERE pos_purchase_items.pid='$pid' AND pos_purchase.status!='canceled' AND (DATE(pos_purchase.invoicedate) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;

                case 3 :
                    $query = $this->db->query("SELECT rid2 AS qty, DATE(d_time) AS  invoicedate,note FROM pos_movers  WHERE pos_movers.d_type='1' AND rid1='$pid'  AND (DATE(d_time) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;
            }

            $this->db->select('*');
            $this->db->from('pos_products');
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $product = $query->row_array();

            $cat_ware = $this->categories_model->cat_ware($pid, $this->aauth->get_user()->loc);

//if(!$cat_ware) exit();
            $html = $this->load->view('products/statementpdf-ltr', array('report' => $result, 'product' => $product, 'cat_ware' => $cat_ware, 'r_type' => $r_type), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($pid . 'report.pdf', 'I');
        } else {
            $pid = intval($this->input->get('id'));
            $this->db->select('*');
            $this->db->from('pos_products');
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $product = $query->row_array();
            $head['title'] = "Product Sales";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/statement', array('id' => $pid, 'product' => $product));
            $this->load->view('fixed/footer');
        }
    }

    public function custom_label()
    {
        if ($this->input->post()) {
            require APPPATH . 'third_party/barcode/autoload.php';
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $bar_width = $this->input->post('bar_width');
            $label_width = $this->input->post('label_width');
            $label_height = $this->input->post('label_height');
            $product_name = $this->input->post('product_name');
            $font_size = $this->input->post('font_size');
            $max_char = $this->input->post('max_char');
            $b_type = $this->input->post('b_type');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $products = array();
            if(!$this->input->post('products_l')) exit('No Product Selected!');
            foreach ($this->input->post('products_l') as $row) {
                // $this->db->select('pos_products.product_name,pos_products.product_price,pos_products.product_code,pos_products.barcode,pos_products.expiry,pos_products.code_type,pos_warehouse.title,pos_warehouse.loc');
                $this->db->select('pos_products.product_name,pos_products.product_price,pos_products.product_code,pos_products.barcode,pos_products.code_type,pos_warehouse.title,pos_warehouse.loc');
                $this->db->from('pos_products');
                $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('pos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('pos_products.pid', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);


            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows, 'bar_width' => $bar_width, 'label_width' => $label_width, 'label_height' => $label_height, 'product_name' => $product_name, 'font_size' => $font_size, 'max_char' => $max_char, 'b_type' => $b_type);


            $this->load->view('barcode/custom_label', array('products' => $products, 'style' => $design));

            /*
                        $html = $this->load->view('barcode/custom_label', array('products' => $products, 'style' => $design), true);
                        ini_set('memory_limit', '64M');

                        //PDF Rendering
                        $this->load->library('pdf');
                        $pdf = $this->pdf->load_en();
                        $pdf->WriteHTML($html);
                        $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');
            */

        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Custom Label";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/custom_label', $data);
            $this->load->view('fixed/footer');
        }
    }

   
public function expiry_alerts() {
    $today = date('Y-m-d');
    $expiry_limit = date('Y-m-d', strtotime('+2 days'));

    $this->db->select('pos_products.pid, pos_products.product_name, pos_products.expiry, pos_warehouse.title as warehouse_name');
    $this->db->from('pos_products');
    $this->db->join('pos_warehouse', 'pos_products.warehouse = pos_warehouse.id', 'left');
    $this->db->where('pos_products.expiry >=', $today);
    $this->db->where('pos_products.expiry <=', $expiry_limit);
    $this->db->where('pos_products.expiry_alert_seen', 0); // NEW: only unseen alerts

    $products = $this->db->get()->result_array();

    foreach ($products as &$product) {
        $product['wdate'] = $product['expiry'];
        $product['name'] = $product['product_name']; 
    }

    echo json_encode($products);
}

public function mark_seen_and_edit() {
    $pid = $this->input->get('pid');
    
    // Mark the alert as seen
    $this->db->where('pid', $pid);
    $this->db->update('pos_products', ['expiry_alert_seen' => 1]);

    // Redirect to the edit page
    redirect('products/edit?id=' . $pid);
}

public function mark_seen_ajax() {
    $pid = $this->input->get('pid');
    $this->db->where('pid', $pid);
    $this->db->update('pos_products', ['expiry_alert_seen' => 1]);

    echo json_encode(['status' => 'success']);
}





public function manage_expiry()
{
    // Pagination setup
    $limit = 10;
    $start = $this->uri->segment(3, 0); 

    // Count total rows
    $today = date('Y-m-d');
    $expiry_limit = date('Y-m-d', strtotime('+3 days'));

    // Count total matching products
    $this->db->from('pos_products');
    $this->db->where('expiry >=', $today);
    $this->db->where('expiry <=', $expiry_limit);
    $total_rows = $this->db->count_all_results();

    // Fetch paginated products
    $this->db->select('pos_products.pid, pos_products.product_name, pos_products.expiry, pos_warehouse.title as warehouse_name, pos_products.expiry_alert_seen');
    $this->db->from('pos_products');
    $this->db->join('pos_warehouse', 'pos_products.warehouse = pos_warehouse.id', 'left');
    $this->db->where('pos_products.expiry >=', $today);
    $this->db->where('pos_products.expiry <=', $expiry_limit);
    $this->db->limit($limit, $start);
    $products = $this->db->get()->result_array();

    // Pagination config
    $this->load->library('pagination');
    $config['base_url'] = base_url('products/manage_expiry');
    $config['total_rows'] = $total_rows;
    $config['per_page'] = $limit;
    $config['uri_segment'] = 3;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
    $config['cur_tag_close'] = '</span></li>';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['attributes'] = ['class' => 'page-link'];

    $this->pagination->initialize($config);

    $head['title'] = 'Expiring Products Notifications';
    $head['usernm'] = $this->aauth->get_user()->username;
    $data['products'] = $products;
    $data['pagination'] = $this->pagination->create_links();

    $this->load->view('fixed/header', $head);
    $this->load->view('products/manage_expiry', $data);
    $this->load->view('fixed/footer');
}


public function import_excel()
{
    $this->load->library('upload');

    $config['upload_path'] = FCPATH . 'uploads/';

    $config['allowed_types'] = 'xls|xlsx';
    $config['max_size'] = 2048; // 2MB
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('excel_file')) {
        $error = $this->upload->display_errors();
        $this->session->set_flashdata('error', "File upload failed: $error");
        redirect('products'); // Redirect back with error message
    } else {
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];

        if (!file_exists($file_path)) {
            $this->session->set_flashdata('error', 'Uploaded file not found.');
            redirect('products');
        }

        require_once FCPATH . 'vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            $sheet_data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $this->session->set_flashdata('error', 'Error reading Excel file: ' . $e->getMessage());
            unlink($file_path);
            redirect('products');
        } catch (Throwable $t) {
            $this->session->set_flashdata('error', 'General error: ' . $t->getMessage());
            unlink($file_path);
            redirect('products');
        }

        $this->load->model('Products_model');
        $imported = $this->Products_model->import_products($sheet_data);

        unlink($file_path); // Delete the uploaded file

        if ($imported) {
            $this->session->set_flashdata('success', 'Products imported successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to import products.');
        }

        redirect('products');
    }
}



    public function check_product_name()
    {
        $product_name = $this->input->post('product_name');
        // $warehouse = $this->input->post('product_warehouse'); // Optional: check by warehouse too

        $this->db->where('LOWER(product_name)', strtolower($product_name));
        // if ($warehouse) {
        //     $this->db->where('warehouse', $warehouse);
        // }
        $exists = $this->db->get('pos_products')->num_rows() > 0;

        echo json_encode(['exists' => $exists]);
    }

    // New method: Get variation options for a specific option (AJAX)
    public function get_variation_options()
    {
        $option_id = $this->input->post('option_id');
        $this->load->model('units_model', 'units');
        $variation_options = $this->units->variation_options_by_option($option_id);
        echo json_encode($variation_options);
    }

    // New method: Get variations for a specific variation option (AJAX)
    public function get_variations()
    {
        $variation_option_id = $this->input->post('variation_option_id');
        $this->load->model('units_model', 'units');
        $variations = $this->units->variations_by_variation_option($variation_option_id);
        echo json_encode($variations);
    }

    // New method: Get all variations for a parent product (for POS popup)
    public function get_product_variations_popup()
    {
        $product_id = $this->input->post('product_id');
        
        $this->db->select('pv.*, po.name as option_name, pvo.name as variation_option_name, pvar.name as variation_name');
        $this->db->from('pos_products pv');
        $this->db->join('pos_units po', 'po.id = pv.option_id', 'left');
        $this->db->join('pos_units pvo', 'pvo.id = pv.variation_option_id', 'left');
        $this->db->join('pos_units pvar', 'pvar.id = pv.variation_id', 'left');
        $this->db->where('pv.sub', $product_id);
        $this->db->where('pv.merge', 1);
        $variations = $this->db->get()->result_array();
        
        echo json_encode($variations);
    }

    // New method: Add product variation with 3-level system
    public function add_product_variation()
    {
        $product_id = $this->input->post('product_id');
        $option_id = $this->input->post('option_id');
        $variation_option_id = $this->input->post('variation_option_id');
        $variation_id = $this->input->post('variation_id');
        $price = $this->input->post('variation_price');
        $stock = $this->input->post('variation_stock');
        $alert = $this->input->post('variation_alert');
        $sku = $this->input->post('variation_sku');

        // Get parent product details
        $this->db->select('*');
        $this->db->where('pid', $product_id);
        $parent_product = $this->db->get('pos_products')->row_array();

        // Get variation names for product name
        $this->db->select('u1.name as option_name, u2.name as variation_option_name, u3.name as variation_name');
        $this->db->from('pos_units u1');
        $this->db->join('pos_units u2', 'u1.id = u2.parent_id', 'left');
        $this->db->join('pos_units u3', 'u2.id = u3.parent_id', 'left');
        $this->db->where('u1.id', $option_id);
        $this->db->where('u2.id', $variation_option_id);
        if ($variation_id) {
            $this->db->where('u3.id', $variation_id);
        }
        $variation_info = $this->db->get()->row_array();

        // Shorten variation name by removing prefixes
        $variation_info['variation_name'] = str_replace(['Capacity ', 'Size ', 'color '], '', $variation_info['variation_name']);

        // Create variation product name
        $variation_name = $parent_product['product_name'];
        if ($variation_info['option_name']) {
            $variation_name .= '-' . $variation_info['option_name'];
        }
        if ($variation_info['variation_option_name']) {
            $variation_name .= '-' . $variation_info['variation_option_name'];
        }
        if ($variation_info['variation_name']) {
            $variation_name .= '-' . $variation_info['variation_name'];
        }

        // Insert variation product
        $variation_data = array(
            'product_name' => $variation_name,
            'pcat' => $parent_product['pcat'],
            'warehouse' => $parent_product['warehouse'],
            'product_code' => $sku ? $sku : '', // Clean: only use SKU if provided, otherwise leave empty
            'product_price' => $price,
            'fproduct_price' => $price, // Final price same as product price
            'qty' => $stock,
            'alert' => $alert,
            'merge' => 1,
            'sub' => $product_id,
            'vb' => 0, // Required field
            'option_id' => $option_id,
            'variation_option_id' => $variation_option_id,
            'variation_id' => $variation_id,
            'unit' => $parent_product['unit'],
            'taxrate' => $parent_product['taxrate'],
            'disrate' => $parent_product['disrate'],
            'barcode' => rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9), // Auto-generate barcode
            'code_type' => 'EAN13',
            'expiry' => '2028-12-31', // Default expiry
            'expiry_alert_seen' => 0,
            'sub_id' => 0,
            'b_id' => 0
        );

        if ($this->db->insert('pos_products', $variation_data)) {
            $variation_pid = $this->db->insert_id();
            
            // Update parent product total stock
            $this->update_parent_total_stock($product_id);
            
            // Log stock movement
            $this->products->movers(1, $variation_pid, $stock, 0, 'Variation Stock Initialized');
            
            echo json_encode(array('status' => 'Success', 'message' => 'Product variation added successfully'));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => 'Failed to add product variation'));
        }
    }

    // Update parent product total stock from all variations
    private function update_parent_total_stock($product_id)
    {
        $this->db->select_sum('qty');
        $this->db->where('sub', $product_id);
        $this->db->where('merge', 1);
        $result = $this->db->get('pos_products')->row();
        $total_stock = $result->qty ? $result->qty : 0;

        $this->db->where('pid', $product_id);
        $this->db->update('pos_products', array('qty' => $total_stock));
    }


    public function custom_label_old()
    {
        if ($this->input->post()) {
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $products = array();


            foreach ($this->input->post('products_l') as $row) {
                $this->db->select('pos_products.product_name,pos_products.product_price,pos_products.product_code,pos_products.barcode,pos_products.expiry,pos_products.code_type,pos_warehouse.title,pos_warehouse.loc');
                $this->db->from('pos_products');
                $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('pos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('pos_products.pid', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);

            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows);


            $html = $this->load->view('barcode/custom_label', array('products' => $products, 'style' => $design), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_en();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');


        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Custom Label";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/custom_label', $data);
            $this->load->view('fixed/footer');
        }
    }

    public function standard_label()
    {
        if ($this->input->post()) {
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $standard_label = $this->input->post('standard_label');
            $products = array();


            foreach ($this->input->post('products_l') as $row) {
                $this->db->select('pos_products.product_name,pos_products.product_price,pos_products.product_code,pos_products.barcode,pos_products.expiry,pos_products.code_type,pos_warehouse.title,pos_warehouse.loc');
                $this->db->from('pos_products');
                $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('pos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('pos_products.pid', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);

            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows);

            switch ($standard_label) {
                case 'eu30019' :
                    $html = $this->load->view('standard_label/eu30019', array('products' => $products, 'style' => $design), true);
                    break;
            }


            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_en();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');


        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Stock Transfer";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/standard_label', $data);
            $this->load->view('fixed/footer');
        }
    }

    


}
