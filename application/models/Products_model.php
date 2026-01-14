<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Products_model extends CI_Model
{
    var $table = 'pos_products';
    var $column_order = array(null, 'pos_products.product_name', 'pos_products.qty', 'pos_products.product_code', 'pos_product_cat.title', 'pos_products.product_price', null); //set column field database for datatable orderable
    var $column_search = array('pos_products.product_name', 'pos_products.product_code', 'pos_product_cat.title', 'pos_warehouse.title'); //set column field database for datatable searchable
    var $order = array('pos_products.pid' => 'desc'); // default order

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query($id = '', $w = '', $sub = '')
    {
        $this->db->select('pos_products.*,pos_product_cat.title AS c_title,pos_warehouse.title, (SELECT COUNT(*) FROM pos_products p2 WHERE p2.sub = pos_products.pid AND p2.merge = 1) as variant_count');
        $this->db->from($this->table);
        $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
        $this->db->where('pos_products.qty >=', 0); // Include products with 0 stock
    

        if ($sub) {
            $this->db->join('pos_product_cat', 'pos_product_cat.id = pos_products.sub_id');

            if ($this->input->post('group') != 'yes') $this->db->where('pos_products.merge', 0);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('pos_warehouse.loc', 0);
            }

            $this->db->where("pos_products.sub_id=$id");

        } else {
            $this->db->join('pos_product_cat', 'pos_product_cat.id = pos_products.pcat');

            if ($w) {

                if ($id > 0) {
                    $this->db->where("pos_warehouse.id = $id");
                    // $this->db->where('pos_products.sub_id', 0);
                }
                // Exclude variations (merge = 1) from warehouse list
                $this->db->where('pos_products.merge !=', 1);
                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('pos_warehouse.loc', 0);
                }

            } else {

                // Exclude variations (merge = 1) from the main product list unless caller explicitly requests grouped view
                if ($this->input->post('group') != 'yes') {
                    $this->db->where('pos_products.merge !=', 1);
                }

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
                    if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('pos_warehouse.loc', 0);
                }
                if ($id > 0) {
                    $this->db->where("pos_product_cat.id = $id");
                    $this->db->where('pos_products.sub_id', 0);
                }
            }
        }

        $i = 0;

        foreach ($this->column_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) // here order processing
        {
            $this->db->order_by($this->column_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    // function get_datatables($id = '', $w = '', $sub = '')
    // {
    //     if ($id > 0) {
    //         $this->_get_datatables_query($id, $w, $sub);
    //     } else {
    //         $this->_get_datatables_query();
    //     }
    //     if ($this->input->post('length') != -1)
    //         $this->db->limit($this->input->post('length'), $this->input->post('start'));
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function get_datatables($id = '', $w = '', $sub = '', $filter = [])
{
    $this->_get_datatables_query($id, $w, $sub);
    if (!empty($filter)) {
        foreach ($filter as $key => $value) {
            $this->db->where($key, $value);
        }
    }
    if ($this->input->post('length') != -1)
        $this->db->limit($this->input->post('length'), $this->input->post('start'));
    $query = $this->db->get();
    return $query->result();
}

    function count_filtered($id, $w = '', $sub = '')
    {
        if ($id > 0) {
            $this->_get_datatables_query($id, $w, $sub);
        } else {
            $this->_get_datatables_query();
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
        // Exclude variations from the overall count used by the datatable (variations have merge = 1)
        $this->db->where('pos_products.merge !=', 1);
        if ($this->aauth->get_user()->loc) {

            $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_warehouse.loc', 0);
        } elseif (!BDATA) {
            $this->db->where('pos_warehouse.loc', 0);
        }
        return $this->db->count_all_results();
    }

    public function addnew($catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $v_type, $v_stock, $v_alert, $wdate, $code_type, $w_type = '', $w_stock = '', $w_alert = '', $sub_cat = '', $b_id = '', $serial = '', $return_response = false)
    {
    $CI =& get_instance();
    $package = $CI->config->item('package');

    // Set product limit based on package
    if ($package == 'basic') {
        $product_limit = 800;
    } elseif ($package == 'standard') {
        $product_limit = 2000;
    } elseif ($package == 'premium') {
        $product_limit = 5000;
    } else {
        $product_limit = 800; // default fallback
    }

    // Count existing products
    $this->db->from('pos_products');
    $count = $this->db->count_all_results();

    if ($count >= $product_limit) {
        echo json_encode(array('status' => 'Error', 'message' => 'Product limit reached for your package.For assistance or upgrades, please contact Deskgoo Consulting at 9824729783 or 9816399804.'));
        return;
    }
        $ware_valid = $this->valid_warehouse($warehouse);
        if (!$sub_cat) $sub_cat = 0;
        if (!$b_id) $b_id = 0;
        $datetime1 = new DateTime(date('Y-m-d'));

        $datetime2 = new DateTime($wdate);

        $difference = $datetime1->diff($datetime2);
        if (!$difference->d > 0) {
            $wdate = null;
        }

        if ($this->aauth->get_user()->loc) {
            if ($ware_valid['loc'] == $this->aauth->get_user()->loc OR $ware_valid['loc'] == '0' OR $warehouse == 0) {
                if (strlen($barcode) > 5 AND is_numeric($barcode)) {
                    $data = array(
                        'pcat' => $catid,
                        'warehouse' => $warehouse,
                        'product_name' => $product_name,
                        'product_code' => $product_code,
                        'product_price' => $product_price,
                        'fproduct_price' => $factoryprice,
                        'taxrate' => $taxrate,
                        'disrate' => $disrate,
                        'qty' => $product_qty,
                        'product_des' => $product_desc,
                        'alert' => $product_qty_alert,
                        'unit' => $unit,
                        'image' => $image,
                        'barcode' => $barcode,
                        'expiry' => $wdate,
                        'code_type' => $code_type,
                        'sub_id' => $sub_cat,
                        'b_id' => $b_id,
                        'expiry_alert_seen' => 0
                    );

                } else {

                    $barcode = rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9);

                    $data = array(
                        'pcat' => $catid,
                        'warehouse' => $warehouse,
                        'product_name' => $product_name,
                        'product_code' => $product_code,
                        'product_price' => $product_price,
                        'fproduct_price' => $factoryprice,
                        'taxrate' => $taxrate,
                        'disrate' => $disrate,
                        'qty' => $product_qty,
                        'product_des' => $product_desc,
                        'alert' => $product_qty_alert,
                        'unit' => $unit,
                        'image' => $image,
                        'barcode' => $barcode,
                        'expiry' => $wdate,
                        'code_type' => 'EAN13',
                        'sub_id' => $sub_cat,
                        'b_id' => $b_id,
                        'expiry_alert_seen' => 0
                    );
                }
                $this->db->trans_start();
                if ($this->db->insert('pos_products', $data)) {
                    $pid = $this->db->insert_id();
                    $this->movers(1, $pid, $product_qty, 0, 'Stock Initialized');
                    $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    
                    $response = array('status' => 'Success', 'message' =>
                        $this->lang->line('ADDED') . "  <a href='add' class='btn btn-blue btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span>  </a> <a href='" . base_url('products') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>");
                    
                    if ($return_response) {
                        $this->db->trans_complete();
                        return array('pid' => $pid, 'response' => $response);
                    } else {
                        echo json_encode($response);
                    }
                } else {
                    $error_response = array('status' => 'Error', 'message' =>
                        $this->lang->line('ERROR'));
                    
                    if ($return_response) {
                        $this->db->trans_complete();
                        return array('pid' => null, 'response' => $error_response);
                    } else {
                        echo json_encode($error_response);
                    }
                }
                if ($serial) {
                    $serial_group = array();
                    foreach ($serial as $key => $value) {
                         if($value) $serial_group[] = array('product_id' => $pid, 'serial' => $value);
                    }
                    $this->db->insert_batch('pos_product_serials', $serial_group);
                }
                if ($v_type) {
                    foreach ($v_type as $key => $value) {
                        if ($v_type[$key] && numberClean($v_stock[$key]) > 0.00) {
                            $this->db->select('u.id,u.name,u2.name AS variation');
                            $this->db->join('pos_units u2', 'u.rid = u2.id', 'left');
                            $this->db->where('u.id', $v_type[$key]);
                            $query = $this->db->get('pos_units u');
                            $r_n = $query->row_array();
                            
                            // Generate variation code from the full variation name
                            // Extract key parts from variation name (e.g., "Blue + XL" -> "BXL")
                            $variation_name = $r_n['variation'] . '-' . $r_n['name'];
                            $variation_parts = preg_split('/[\s\-\+\&]+/', $variation_name);
                            $variation_short = '';
                            foreach ($variation_parts as $part) {
                                if (strlen(trim($part)) > 0) {
                                    $variation_short .= strtoupper(substr(trim($part), 0, 1));
                                }
                            }
                            // Limit to 4 characters max
                            $variation_short = substr($variation_short, 0, 4);
                            $variation_code = $product_code . '-' . $variation_short;
                            
                            $data['product_name'] = $product_name . '-' . $r_n['variation'] . '-' . $r_n['name'];
                            $data['product_code'] = $variation_code;
                            $data['qty'] = numberClean($v_stock[$key]);
                            $data['alert'] = numberClean($v_alert[$key]);
                            $data['merge'] = 1;
                            $data['sub'] = $pid;
                            $data['vb'] = $v_type[$key];
                            $this->db->insert('pos_products', $data);
                            $pidv = $this->db->insert_id();
                            $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                            $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                        }
                    }
                }
                if ($w_type) {
                    foreach ($w_type as $key => $value) {
                        if ($w_type[$key] && numberClean($w_stock[$key]) > 0.00 && $w_type[$key] != $warehouse) {
                            $data['product_name'] = $product_name;
                            $data['warehouse'] = $w_type[$key];
                            $data['qty'] = numberClean($w_stock[$key]);
                            $data['alert'] = numberClean($w_alert[$key]);
                            $data['merge'] = 2;
                            $data['sub'] = $pid;
                            $data['vb'] = $w_type[$key];
                            $data['expiry_alert_seen'] = 0;
                            $this->db->insert('pos_products', $data);
                            $pidv = $this->db->insert_id();
                            $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                            $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                        }
                    }
                }
                $this->db->trans_complete();
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        } else {
            if (strlen($barcode) > 5 AND is_numeric($barcode)) {
                $data = array(
                    'pcat' => $catid,
                    'warehouse' => $warehouse,
                    'product_name' => $product_name,
                    'product_code' => $product_code,
                    'product_price' => $product_price,
                    'fproduct_price' => $factoryprice,
                    'taxrate' => $taxrate,
                    'disrate' => $disrate,
                    'qty' => $product_qty,
                    'product_des' => $product_desc,
                    'alert' => $product_qty_alert,
                    'unit' => $unit,
                    'image' => $image,
                    'barcode' => $barcode,
                    'expiry' => $wdate,
                    'code_type' => $code_type,
                    'sub_id' => $sub_cat,
                    'b_id' => $b_id,
                    'expiry_alert_seen' => 0
                );
            } else {
                $barcode = rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9);
                $data = array(
                    'pcat' => $catid,
                    'warehouse' => $warehouse,
                    'product_name' => $product_name,
                    'product_code' => $product_code,
                    'product_price' => $product_price,
                    'fproduct_price' => $factoryprice,
                    'taxrate' => $taxrate,
                    'disrate' => $disrate,
                    'qty' => $product_qty,
                    'product_des' => $product_desc,
                    'alert' => $product_qty_alert,
                    'unit' => $unit,
                    'image' => $image,
                    'barcode' => $barcode,
                    'expiry' => $wdate,
                    'code_type' => 'EAN13',
                    'sub_id' => $sub_cat,
                    'b_id' => $b_id,
                    'expiry_alert_seen' => 0
                );
            }
            $this->db->trans_start();
            if ($this->db->insert('pos_products', $data)) {
                $pid = $this->db->insert_id();
                $this->movers(1, $pid, $product_qty, 0, 'Stock Initialized');
                $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                
                $response = array('status' => 'Success', 'message' =>
                    $this->lang->line('ADDED') . "  <a href='add' class='btn btn-blue btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span>  </a> <a href='" . base_url('products') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>");
                
                if ($return_response) {
                    $this->db->trans_complete();
                    return array('pid' => $pid, 'response' => $response);
                } else {
                    echo json_encode($response);
                }
            } else {
                $error_response = array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR'));
                
                if ($return_response) {
                    $this->db->trans_complete();
                    return array('pid' => null, 'response' => $error_response);
                } else {
                    echo json_encode($error_response);
                }
            }
            if ($serial) {
                $serial_group = array();
                foreach ($serial as $key => $value) {
                     if($value)  $serial_group[] = array('product_id' => $pid, 'serial' => $value);
                }
                $this->db->insert_batch('pos_product_serials', $serial_group);
            }
            if ($v_type) {
                foreach ($v_type as $key => $value) {
                    if ($v_type[$key] && numberClean($v_stock[$key]) > 0.00) {
                        $this->db->select('u.id,u.name,u2.name AS variation');
                        $this->db->join('pos_units u2', 'u.rid = u2.id', 'left');
                        $this->db->where('u.id', $v_type[$key]);

                        $query = $this->db->get('pos_units u');
                        $r_n = $query->row_array();
                        $data['product_name'] = $product_name . '-' . $r_n['variation'] . '-' . $r_n['name'];
                        $data['qty'] = numberClean($v_stock[$key]);
                        $data['alert'] = numberClean($v_alert[$key]);
                        $data['merge'] = 1;
                        $data['sub'] = $pid;
                        $data['vb'] = $v_type[$key];
                        $this->db->insert('pos_products', $data);
                        $pidv = $this->db->insert_id();
                        $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                        $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    }
                }
            }
            if ($w_type) {
                foreach ($w_type as $key => $value) {
                    if ($w_type[$key] && numberClean($w_stock[$key]) > 0.00 && $w_type[$key] != $warehouse) {

                        $data['product_name'] = $product_name;
                        $data['warehouse'] = $w_type[$key];
                        $data['qty'] = numberClean($w_stock[$key]);
                        $data['alert'] = numberClean($w_alert[$key]);
                        $data['merge'] = 2;
                        $data['sub'] = $pid;
                        
                        
                        $this->db->insert('pos_products', $data);
                        $pidv = $this->db->insert_id();
                        $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                        $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    }
                }
            }
            $this->custom->save_fields_data($pid, 4);
            $this->db->trans_complete();

        }
    }

    public function edit($pid, $catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $code_type, $sub_cat = '', $b_id = '', $vari = null, $serial = null, $wdate = null)
    {


        $this->db->select('qty');
        $this->db->from('pos_products');
        $this->db->where('pid', $pid);
        $query = $this->db->get();
        $r_n = $query->row_array();
        $ware_valid = $this->valid_warehouse($warehouse);
        $this->db->trans_start();
        if ($this->aauth->get_user()->loc) {
            if ($ware_valid['loc'] == $this->aauth->get_user()->loc OR $ware_valid['loc'] == '0' OR $warehouse == 0) {
                $data = array(
                    'pcat' => $catid,
                    'warehouse' => $warehouse,
                    'product_name' => $product_name,
                    'product_code' => $product_code,
                    'product_price' => $product_price,
                    'fproduct_price' => $factoryprice,
                    'taxrate' => $taxrate,
                    'disrate' => $disrate,
                    'qty' => $product_qty,
                    'product_des' => $product_desc,
                    'alert' => $product_qty_alert,
                    'unit' => $unit,
                    'image' => $image,
                    'barcode' => $barcode,
                    'code_type' => $code_type,
                    'sub_id' => $sub_cat,
                    'b_id' => $b_id,
                    'expiry_alert_seen' => 0
                );


				$datetime1 = new DateTime(date('Y-m-d'));

				$datetime2 = new DateTime($wdate);

				$difference = $datetime1->diff($datetime2);
				if ($difference->d > 0) {
					$data['expiry'] = $wdate;
				}


                $this->db->set($data);
                $this->db->where('pid', $pid);

                if ($this->db->update('pos_products')) {
                    if ($r_n['qty'] != $product_qty) {
                        $m_product_qty = $product_qty - $r_n['qty'];
                        $this->movers(1, $pid, $m_product_qty, 0, 'Stock Changes');
                    }
                    $this->aauth->applog("[Update Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    echo json_encode(array('status' => 'Success', 'message' =>
                        $this->lang->line('UPDATED') . " <a href='" . base_url('products/edit?id=' . $pid) . "' class='btn btn-blue btn-lg'><span class='fa fa-eye' aria-hidden='true'></span>  </a> <a href='" . base_url('products') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>"));
                } else {
                    echo json_encode(array('status' => 'Error', 'message' =>
                        $this->lang->line('ERROR')));
                }
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        } else {
            $data = array(
                'pcat' => $catid,
                'warehouse' => $warehouse,
                'product_name' => $product_name,
                'product_code' => $product_code,
                'product_price' => $product_price,
                'fproduct_price' => $factoryprice,
                'taxrate' => $taxrate,
                'disrate' => $disrate,
                'qty' => $product_qty,
                'product_des' => $product_desc,
                'alert' => $product_qty_alert,
                'unit' => $unit,
                'image' => $image,
                'barcode' => $barcode,
                'code_type' => $code_type,
                'sub_id' => $sub_cat,
                'b_id' => $b_id,
                'expiry_alert_seen' => 0
            );

			$datetime1 = new DateTime(date('Y-m-d'));

			$datetime2 = new DateTime($wdate);

			$difference = $datetime1->diff($datetime2);
			if ($difference->d > 0) {
				$data['expiry'] = $wdate;
			}

			$this->db->set($data);
            $this->db->where('pid', $pid);
            if ($this->db->update('pos_products')) {
                if ($r_n['qty'] != $product_qty) {
                    $m_product_qty = $product_qty - $r_n['qty'];
                    $this->movers(1, $pid, $m_product_qty, 0, 'Stock Changes');
                }
                $this->aauth->applog("[Update Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                echo json_encode(array('status' => 'Success', 'message' =>
                    $this->lang->line('UPDATED') . " <a href='" . base_url('products/edit?id=' . $pid) . "' class='btn btn-blue btn-lg'><span class='fa fa-eye' aria-hidden='true'></span>  </a> <a href='" . base_url('products') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>"));
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        }

        if (isset($serial['old'])) {
            $this->db->delete('pos_product_serials', array('product_id' => $pid,'status'=>0));
            $serial_group = array();
            foreach ($serial['old'] as $key => $value) {
                if($value) $serial_group[] = array('product_id' => $pid, 'serial' => $value);
            }
            $this->db->insert_batch('pos_product_serials', $serial_group);
        }
                if (isset($serial['new'])) {
            $serial_group = array();
            foreach ($serial['new'] as $key => $value) {
                 if($value)  $serial_group[] = array('product_id' => $pid, 'serial' => $value,'status'=>0);
            }

            $this->db->insert_batch('pos_product_serials', $serial_group);
        }
        $this->custom->edit_save_fields_data($pid, 4);


        $v_type = @$vari['v_type'];
        $v_stock = @$vari['v_stock'];
        $v_alert = @$vari['v_alert'];
        $w_type = @$vari['w_type'];
        $w_stock = @$vari['w_stock'];
        $w_alert = @$vari['w_alert'];

        if (isset($v_type)) {
            foreach ($v_type as $key => $value) {
                if ($v_type[$key] && numberClean($v_stock[$key]) > 0.00) {
                    $this->db->select('u.id,u.name,u2.name AS variation');
                    $this->db->join('pos_units u2', 'u.rid = u2.id', 'left');
                    $this->db->where('u.id', $v_type[$key]);
                    $query = $this->db->get('pos_units u');
                    $r_n = $query->row_array();
                    
                    // Get parent product code for variation code generation
                    $this->db->select('product_code');
                    $this->db->from('pos_products');
                    $this->db->where('pid', $pid);
                    $parent_query = $this->db->get();
                    $parent = $parent_query->row_array();
                    $parent_code = $parent['product_code'];
                    
                    // Generate variation code from the full variation name
                    // Extract key parts from variation name (e.g., "Blue + XL" -> "BXL")
                    $variation_name = $r_n['variation'] . '-' . $r_n['name'];
                    $variation_parts = preg_split('/[\s\-\+\&]+/', $variation_name);
                    $variation_short = '';
                    foreach ($variation_parts as $part) {
                        if (strlen(trim($part)) > 0) {
                            $variation_short .= strtoupper(substr(trim($part), 0, 1));
                        }
                    }
                    // Limit to 4 characters max
                    $variation_short = substr($variation_short, 0, 4);
                    $variation_code = $parent_code . '-' . $variation_short;
                    
                    $data['product_name'] = $product_name . '-' . $r_n['variation'] . '-' . $r_n['name'];
                    $data['product_code'] = $variation_code;
                    $data['qty'] = numberClean($v_stock[$key]);
                    $data['alert'] = numberClean($v_alert[$key]);
                    $data['merge'] = 1;
                    $data['sub'] = $pid;
                    $data['vb'] = $v_type[$key];
                    $this->db->insert('pos_products', $data);
                    $pidv = $this->db->insert_id();
                    $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                    $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                }
            }
        }
        if (isset($w_type)) {
            foreach ($w_type as $key => $value) {
                if ($w_type[$key] && numberClean($w_stock[$key]) > 0.00 && $w_type[$key] != $warehouse) {
                    $data['product_name'] = $product_name;
                    $data['warehouse'] = $w_type[$key];
                    $data['qty'] = numberClean($w_stock[$key]);
                    $data['alert'] = numberClean($w_alert[$key]);
                    $data['merge'] = 2;
                    $data['sub'] = $pid;
                    $data['vb'] = $w_type[$key];
                    $data['expiry_alert_seen'] = 0;
                    $this->db->insert('pos_products', $data);
                    $pidv = $this->db->insert_id();
                    $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                    $this->aauth->applog("[New Product] -$product_name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                }
            }
        }
        $this->db->trans_complete();

    }

    private function get_warehouse_id_by_name($name, $warehouses)
{
    $name = strtolower(trim($name));

    foreach ($warehouses as $wh) {
        if (strtolower(trim($wh->title)) === $name) {
            return $wh->id;  // exact match ignoring case found
        }
    }

    // If not found, insert new warehouse with that name
    $this->db->insert('pos_warehouse', ['title' => $name, 'extra' => 'warehouse', 'loc' => 0]);
    return $this->db->insert_id();
}





public function import_products($sheet_data)
{
    $insert_data = [];
    $row_count = 0;

    // Load warehouses once
    $warehouses = $this->db->select('id, title')->get('pos_warehouse')->result();

    // Helper to get warehouse ID by name (case insensitive)
    $get_warehouse_id_by_name = function($name) use ($warehouses) {
        $name = strtolower(trim($name));
        foreach ($warehouses as $wh) {
            if (strtolower(trim($wh->title)) === $name) {
                return $wh->id;
            }
        }
        // Not found, insert new warehouse
        $this->db->insert('pos_warehouse', ['title' => $name, 'extra' => 'warehouse', 'loc' => 0]);
        return $this->db->insert_id();
    };

    // Helper to check barcode validity (numeric & length > 5)
    $is_valid_barcode = function($barcode) {
        return $barcode && is_numeric($barcode) && strlen($barcode) > 5;
    };

    // Helper to check uniqueness of barcode
    $is_unique_barcode = function($barcode) {
        $this->db->where('barcode', $barcode);
        $exists = $this->db->count_all_results('pos_products');
        return $exists == 0;
    };

    // Helper to generate random barcode (simple 13 digit)
    $generate_barcode = function() use ($is_unique_barcode) {
        do {
            // You can replace with your EAN-13 generator here
            $barcode = rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9);
        } while (!$is_unique_barcode($barcode));
        return $barcode;
    };

    foreach ($sheet_data as $row) {
        if ($row_count == 0) {
            $row_count++;
            continue; // Skip header row
        }

        $product_name = isset($row['A']) && !empty($row['A']) ? $row['A'] : null;
        $product_price = isset($row['E']) && is_numeric($row['E']) ? $row['E'] : null;
        $qty = isset($row['I']) && is_numeric($row['I']) ? $row['I'] : null;

        if (!$product_name || !$product_price || !$qty) {
            continue; // Skip missing required data
        }

        // Category handling
        $category_name = isset($row['B']) ? trim($row['B']) : '';
$category_id = 1;
if (!empty($category_name)) {
    $this->db->where('LOWER(title)', strtolower($category_name));
    $cat = $this->db->get('pos_product_cat')->row();
    if ($cat) {
        $category_id = $cat->id;
    } else {
        $this->db->insert('pos_product_cat', ['title' => ucfirst(strtolower($category_name))]);
        $category_id = $this->db->insert_id();
    }
}


        // Warehouse handling
        $warehouse_name = isset($row['C']) ? $row['C'] : '';
        $warehouse_id = !empty(trim($warehouse_name)) ? $get_warehouse_id_by_name($warehouse_name) : 4;

        // Expiry date
        $expiry_raw = isset($row['N']) ? $row['N'] : null;
        $expiry_date = null;
        if ($expiry_raw) {
            if (is_numeric($expiry_raw)) {
                $unix_date = ($expiry_raw - 25569) * 86400;
                $expiry_date = gmdate("Y-m-d", $unix_date);
            } else {
                $time = strtotime($expiry_raw);
                $expiry_date = $time !== false ? date("Y-m-d", $time) : null;
            }
        }

        // Barcode validation and generation
        $barcode_raw = isset($row['M']) ? trim($row['M']) : null;
        if (!$is_valid_barcode($barcode_raw) || !$is_unique_barcode($barcode_raw)) {
            $barcode_raw = $generate_barcode();
        }

        $insert_data[] = [
            'product_name' => $product_name,
            'pcat' => $category_id,
            'warehouse' => $warehouse_id,
            'product_code' => isset($row['D']) ? $row['D'] : null,
            'product_price' => $product_price,
            'fproduct_price' => isset($row['F']) ? $row['F'] : 0.00,
            'taxrate' => isset($row['G']) ? $row['G'] : 0.00,
            'disrate' => isset($row['H']) ? $row['H'] : 0.00,
            'qty' => $qty,
            'alert' => isset($row['J']) ? $row['J'] : null,
            'product_des' => isset($row['K']) ? $row['K'] : null,
            'unit' => isset($row['L']) ? $row['L'] : null,
            'barcode' => $barcode_raw,
            'expiry' => $expiry_date,
            'image' => isset($row['O']) && !empty($row['O']) ? $row['O'] : 'deskgoo.png',
            'code_type' => 'EAN13',
            'sub_id' => 0,
            'b_id' => 0,
            'expiry_alert_seen' => 0,
        ];
    }

    if (!empty($insert_data)) {
    $batchSize = 500;  // You can adjust this number as needed
    $totalInserted = 0;

    for ($i = 0; $i < count($insert_data); $i += $batchSize) {
        $batch = array_slice($insert_data, $i, $batchSize);
        $inserted = $this->db->insert_batch('pos_products', $batch);
        if ($inserted) {
            $totalInserted += count($batch);
        } else {
            // If batch insert fails, you can handle error here if needed
            return false;
        }
    }
    return $totalInserted > 0;
}
return false;


    return false;
}





 // Get all products
    public function get_all_products() {
        $this->db->select('*');
        $this->db->from('pos_products');
        // Exclude parent products that have variations
        $this->db->where('pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge = 1 AND sub IS NOT NULL)');
        return $this->db->get()->result_array();
    }

    // Get product by ID
    public function get_product($product_id) {
        return $this->db->get_where('pos_products', ['pid' => $product_id])->row();
    }

    // Update stock quantity
    // public function update_stock($product_id, $new_qty) {
    //     $this->db->where('pid', $product_id);
    //     return $this->db->update('pos_products', ['product_qty' => $new_qty]);
    // }

    // Update stock with warehouse-aware logic
public function update_stock($product_id, $new_qty, $warehouse_id = null) {
    $this->db->where('pid', $product_id);
    if ($warehouse_id !== null) {
        $this->db->where('warehouse', $warehouse_id);
    }
    return $this->db->update('pos_products', ['product_qty' => $new_qty]);
}

// Get categories by warehouse with names

// Get categories by warehouse
public function get_categories_by_warehouse($warehouse_id) {
    $this->db->distinct();
    $this->db->select('c.id, c.title');
    $this->db->from('pos_products p');
    $this->db->join('pos_product_cat c', 'p.pcat = c.id');
    $this->db->where('p.warehouse', $warehouse_id);
    return $this->db->get()->result_array();
}

// Get subcategories by category and warehouse
public function get_subcategories_by_category_warehouse($category_id, $warehouse_id) {
    $this->db->distinct();
    $this->db->select('c.id, c.title');
    $this->db->from('pos_products p');
    $this->db->join('pos_product_cat c', 'p.sub_id = c.id');
    $this->db->where('p.pcat', $category_id);
    $this->db->where('p.warehouse', $warehouse_id);
    $this->db->where('p.sub_id !=', 0);
    return $this->db->get()->result_array();
}

// Get products by subcategory and warehouse
public function get_products_by_subcategory_warehouse($sub_category_id, $warehouse_id) {
    $this->db->where('sub_id', $sub_category_id);
    $this->db->where('warehouse', $warehouse_id);
    // Exclude parent products that have variations
    $this->db->where('pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge = 1 AND sub IS NOT NULL)');
    return $this->db->get('pos_products')->result_array();
}

// Get products by category and warehouse (optional)


public function get_products_by_category_warehouse($category_id, $warehouse_id) {
    $this->db->where('pcat', $category_id);
    $this->db->where('warehouse', $warehouse_id);
    // Exclude parent products that have variations
    $this->db->where('pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge = 1 AND sub IS NOT NULL)');
    return $this->db->get('pos_products')->result_array();
}




    public function prd_stats()
    {

        $whr = '';
        if ($this->aauth->get_user()->loc) {
            $whr = ' LEFT JOIN  pos_warehouse on pos_warehouse.id = pos_products.warehouse WHERE pos_warehouse.loc=' . $this->aauth->get_user()->loc;
            if (BDATA) $whr = ' LEFT JOIN  pos_warehouse on pos_warehouse.id = pos_products.warehouse WHERE pos_warehouse.loc=0 OR pos_warehouse.loc=' . $this->aauth->get_user()->loc;
        } elseif (!BDATA) {
            $whr = ' LEFT JOIN  pos_warehouse on pos_warehouse.id = pos_products.warehouse WHERE pos_warehouse.loc=0';
        }
        $query = $this->db->query("SELECT
COUNT(IF( pos_products.qty > 0, pos_products.qty, NULL)) AS instock,
COUNT(IF( pos_products.qty <= 0, pos_products.qty, NULL)) AS outofstock,
COUNT(pos_products.qty) AS total
FROM pos_products $whr");
        echo json_encode($query->result_array());
    }

    public function products_list($id, $term = '')
    {
        $this->db->select('pos_products.*');
        $this->db->from('pos_products');
        $this->db->where('pos_products.warehouse', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
            $this->db->where('pos_warehouse.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->join('pos_warehouse', 'pos_warehouse.id = pos_products.warehouse');
            $this->db->where('pos_warehouse.loc', 0);
        }
        if ($term) {
            $this->db->where("pos_products.product_name LIKE '%$term%'");
            $this->db->or_where("pos_products.product_code LIKE '$term%'");
        }
        // Exclude parent products that have variations
        $this->db->where('pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge = 1 AND sub IS NOT NULL)');
        $query = $this->db->get();
        return $query->result_array();

    }


    public function units()
    {
        $this->db->select('*');
        $this->db->from('pos_units');
        $this->db->where('type', 0);
        $query = $this->db->get();
        return $query->result_array();

    }

    public function serials($pid)
    {
        $this->db->select('*');
        $this->db->from('pos_product_serials');
        $this->db->where('product_id', $pid);

        $query = $this->db->get();
        return $query->result_array();


    }

    // public function transfer($from_warehouse, $products_l, $to_warehouse, $qty)
    // {
    //     $updateArray = array();
    //     $move = false;
    //     $qtyArray = explode(',', $qty);
    //     $this->db->select('title');
    //     $this->db->from('pos_warehouse');
    //     $this->db->where('id', $to_warehouse);
        
    //     $query = $this->db->get();
    //     $to_warehouse_name = $query->row_array()['title'];

    //     $i = 0;
    //     foreach ($products_l as $row) {
    //         $qty = 0;
    //         if (array_key_exists($i, $qtyArray)) $qty = $qtyArray[$i];

    //         $this->db->select('*');
    //         $this->db->from('pos_products');
    //         $this->db->where('pid', $row);
    //         $query = $this->db->get();
    //         $pr = $query->row_array();
    //         $pr2 = $pr;
    //         $c_qty = $pr['qty'];
    //         if ($c_qty - $qty < 0) {

    //         } elseif ($c_qty - $qty == 0) {
               
    
          


    //             if ($pr['merge'] == 2) {

    //                 $this->db->select('pid,product_name');
    //                 $this->db->from('pos_products');
    //                 $this->db->where('pid', $pr['sub']);
    //                 $this->db->where('warehouse', $to_warehouse);
    //                 $query = $this->db->get();
    //                 $pr = $query->row_array();

    //             } else {
    //                 $this->db->select('pid,product_name');
    //                 $this->db->from('pos_products');
    //                 $this->db->where('merge', 2);
    //                 $this->db->where('sub', $row);
    //                 $this->db->where('warehouse', $to_warehouse);
    //                 $query = $this->db->get();
    //                 $pr = $query->row_array();
    //             }


    //             $c_pid = $pr['pid'];
    //             $product_name = $pr['product_name'];

    //             if ($c_pid) {

    //                 $this->db->set('qty', "qty+$qty", FALSE);
    //                 $this->db->where('pid', $c_pid);
    //                 $this->db->update('pos_products');
    //                 $this->aauth->applog("[Product Transfer] -$product_name  -Qty-$qty ID " . $c_pid, $this->aauth->get_user()->username);
    //                 $this->db->delete('pos_products', array('pid' => $row));
    //                 $this->db->delete('pos_movers', array('d_type' => 1, 'rid1' => $row));
    //                 //  $this->movers(1, $c_pid, $qty, 0, 'Stock Transferred W ' . $to_warehouse_name); // added mover log for the destination product
                  
    //             } else {
    //                 $updateArray[] = array(
    //                     'pid' => $row,
    //                     'warehouse' => $to_warehouse
    //                 );
    //                 $move = true;
    //                 $product_name = $pr2['product_name'];
    //                 $this->db->delete('pos_movers', array('d_type' => 1, 'rid1' => $row));

    //                 $this->movers(1, $row, $qty, 0, 'Stock Transferred & Initialized W- ' . $to_warehouse_name);
    //                 $this->aauth->applog("[Product Transfer] -$product_name  -Qty-$qty W- $to_warehouse_name PID " . $pr2['pid'], $this->aauth->get_user()->username);
    //             }


    //         } else {
    //             $data['product_name'] = $pr['product_name'];
    //             $data['pcat'] = $pr['pcat'];
    //             $data['warehouse'] = $to_warehouse;
    //             $data['product_name'] = $pr['product_name'];
    //             $data['product_code'] = $pr['product_code'];
    //             $data['product_price'] = $pr['product_price'];
    //             $data['fproduct_price'] = $pr['fproduct_price'];
    //             $data['taxrate'] = $pr['taxrate'];
    //             $data['disrate'] = $pr['disrate'];
    //             $data['qty'] = $qty;
    //             $data['product_des'] = $pr['product_des'];
    //             $data['alert'] = $pr['alert'];
    //             $data['	unit'] = $pr['unit'];
    //             $data['image'] = $pr['image'];
    //             $data['barcode'] = $pr['barcode'];
    //             $data['merge'] = 2;
    //             $data['sub'] = $row;
    //             $data['vb'] = $to_warehouse;
    //             $data['expiry_alert_seen'] = 0;
    //             $data['expiry'] = $pr['expiry']; // <-- ADD THIS LINE
    //             if ($pr['merge'] == 2) {
    //                 $this->db->select('pid,product_name');
    //                 $this->db->from('pos_products');
    //                 $this->db->where('pid', $pr['sub']);
    //                 $this->db->where('warehouse', $to_warehouse);
    //                 $query = $this->db->get();
    //                 $pr = $query->row_array();
    //             } else {
    //                 $this->db->select('pid,product_name');
    //                 $this->db->from('pos_products');
    //                 $this->db->where('merge', 2);
    //                 $this->db->where('sub', $row);
    //                 $this->db->where('warehouse', $to_warehouse);
    //                 $query = $this->db->get();
    //                 $pr = $query->row_array();
    //             }


    //             $c_pid = $pr['pid'];
    //             $product_name = $pr2['product_name'];

    //             if ($c_pid) {

    //                 // Update stock in the destination warehouse
    //             $this->db->set('qty', "qty+$qty", FALSE);
    //             $this->db->where('pid', $c_pid);
    //             $this->db->update('pos_products');
    //             $this->movers(1, $c_pid, $qty, 0, 'Stock Transferred W ' . $to_warehouse_name);
    //             $this->aauth->applog("[Product Transfer] -$product_name  -Qty-$qty W $to_warehouse_name  ID " . $c_pid, $this->aauth->get_user()->username);


    //             } else {
    //                 $this->db->insert('pos_products', $data);
    //                 $pid = $this->db->insert_id();
    //                 $this->movers(1, $pid, $qty, 0, 'Stock Transferred & Initialized W ' . $to_warehouse_name);
    //                 $this->aauth->applog("[Product Transfer] -$product_name  -Qty-$qty  W $to_warehouse_name ID " . $pr2['pid'], $this->aauth->get_user()->username);

    //             }

    //             $this->db->set('qty', "qty-$qty", FALSE);
    //             $this->db->where('pid', $row);
    //             $this->db->update('pos_products');
    //             $this->movers(1, $row, -$qty, 0, 'Stock Transferred WID ' . $to_warehouse_name);
    //         }


    //         $i++;
    //     }

    //     if ($move) {
    //         $this->db->update_batch('pos_products', $updateArray, 'pid');
    //     }

    //     echo json_encode(array('status' => 'Success', 'message' =>
    //         $this->lang->line('UPDATED')));


    // }
    
public function transfer($from_warehouse, $products_l, $to_warehouse, $qty)
{
    $updateArray = array();
    $move = false;
    $qtyArray = explode(',', $qty);

    $this->db->select('title');
    $this->db->from('pos_warehouse');
    $this->db->where('id', $to_warehouse);
    $query = $this->db->get();
    $to_warehouse_name = $query->row_array()['title'];

    $i = 0;
    foreach ($products_l as $row) {
        $qty = 0;
        if (array_key_exists($i, $qtyArray)) $qty = $qtyArray[$i];

        // Fetch source product details
        $this->db->select('*');
        $this->db->from('pos_products');
        $this->db->where('pid', $row);
        $query = $this->db->get();
        $pr = $query->row_array();
        $pr2 = $pr;
        $c_qty = $pr['qty'];

        if ($c_qty - $qty < 0) {
            // Do nothing if stock is insufficient
        } elseif ($c_qty - $qty == 0) {
            // Stock becomes 0: Set qty = 0, do not delete
            $this->db->set('qty', 0, FALSE);
            $this->db->where('pid', $row);
            $this->db->update('pos_products');

            // Update parent stock if this is a variation (merge=1)
            if ($pr['merge'] == 1) {
                $this->update_parent_total_stock($pr['sub']);
            }

            // Check if destination product already exists by product code
            $this->db->select('pid, product_name');
            $this->db->from('pos_products');
            $this->db->where('product_code', $pr2['product_code']);
            $this->db->where('warehouse', $to_warehouse);
            $query = $this->db->get();
            $pr_dest = $query->row_array();

            $c_pid = $pr_dest['pid'] ?? null;
            $product_name = $pr2['product_name'];

            if ($c_pid) {
                $this->db->set('qty', "qty+$qty", FALSE);
                $this->db->where('pid', $c_pid);
                $this->db->update('pos_products');
                $this->movers(1, $c_pid, $qty, 0, 'Stock Transferred W ' . $to_warehouse_name);
                $this->aauth->applog("[Product Transfer] -$product_name  -Qty-$qty W $to_warehouse_name ID " . $c_pid, $this->aauth->get_user()->username);
                
                // Update parent stock at destination warehouse if this is a variation
                if ($pr_dest['merge'] == 1) {
                    // Find parent in destination warehouse and update its stock
                    $this->db->select('pid');
                    $this->db->from('pos_products');
                    $this->db->where('pid', $pr_dest['sub']);
                    $this->db->where('warehouse', $to_warehouse);
                    $parent_dest_check = $this->db->get()->row_array();
                    if ($parent_dest_check) {
                        $this->update_parent_total_stock_at_warehouse($pr_dest['sub'], $to_warehouse);
                    }
                }
            } 
            else {
                // Destination product does not exist – create new one
                $data = array(
                    'product_name' => $pr2['product_name'],
                    'pcat' => $pr2['pcat'],
                    'warehouse' => $to_warehouse,
                    'product_code' => $pr2['product_code'],
                    'product_price' => $pr2['product_price'],
                    'fproduct_price' => $pr2['fproduct_price'],
                    'taxrate' => $pr2['taxrate'],
                    'disrate' => $pr2['disrate'],
                    'qty' => $qty,
                    'product_des' => $pr2['product_des'],
                    'alert' => $pr2['alert'],
                    'unit' => $pr2['unit'],
                    'image' => $pr2['image'],
                    'barcode' => $pr2['barcode'],
                    'merge' => 2,
                    'sub' => $row,
                    'vb' => $to_warehouse,
                    'expiry_alert_seen' => 0,
                    'expiry' => $pr2['expiry']
                );
                $this->db->insert('pos_products', $data);
                $pid = $this->db->insert_id();
                $this->movers(1, $pid, $qty, 0, 'Stock Transferred & Initialized W ' . $to_warehouse_name);
                $this->aauth->applog("[Product Transfer] -$product_name  -Qty-$qty  W $to_warehouse_name ID " . $pr2['pid'], $this->aauth->get_user()->username);
                
                // Update parent stock at destination warehouse if this is a variation
                if ($data['merge'] == 1) {
                    // Find parent in destination warehouse and update its stock
                    $this->db->select('pid');
                    $this->db->from('pos_products');
                    $this->db->where('pid', $data['sub']);
                    $this->db->where('warehouse', $to_warehouse);
                    $parent_dest = $this->db->get()->row_array();
                    if ($parent_dest) {
                        $this->update_parent_total_stock_at_warehouse($data['sub'], $to_warehouse);
                    }
                }
            }
        } else {
            // Reduce stock from source warehouse
            $this->db->set('qty', "qty-$qty", FALSE);
            $this->db->where('pid', $row);
            $this->db->update('pos_products');
            $this->movers(1, $row, -$qty, 0, 'Stock Transferred WID ' . $to_warehouse_name);

            // Update parent stock if this is a variation (merge=1)
            if ($pr['merge'] == 1) {
                $this->update_parent_total_stock($pr['sub']);
            }

            // Check if destination product already exists - look for product with same code in destination warehouse
            $this->db->select('pid, merge, sub');
            $this->db->from('pos_products');
            $this->db->where('product_code', $pr2['product_code']);
            $this->db->where('warehouse', $to_warehouse);
            $query = $this->db->get();
            $pr_dest = $query->row_array();

            if ($pr_dest) {
                // Update existing destination stock
                $this->db->set('qty', "qty+$qty", FALSE);
                $this->db->where('pid', $pr_dest['pid']);
                $this->db->update('pos_products');
                $this->movers(1, $pr_dest['pid'], $qty, 0, 'Stock Transferred W ' . $to_warehouse_name);
                $this->aauth->applog("[Product Transfer] -" . $pr2['product_name'] . "  -Qty-$qty W $to_warehouse_name  ID " . $pr_dest['pid'], $this->aauth->get_user()->username);
                
                // Update parent stock at destination warehouse if this is a variation
                if ($pr_dest['merge'] == 1) {
                    // Find parent in destination warehouse and update its stock
                    $this->db->select('pid');
                    $this->db->from('pos_products');
                    $this->db->where('pid', $pr_dest['sub']);
                    $this->db->where('warehouse', $to_warehouse);
                    $parent_dest_check = $this->db->get()->row_array();
                    if ($parent_dest_check) {
                        $this->update_parent_total_stock_at_warehouse($pr_dest['sub'], $to_warehouse);
                    }
                }
            } else {
                // Destination product missing – insert
                $data = array(
                    'product_name' => $pr2['product_name'],
                    'pcat' => $pr2['pcat'],
                    'warehouse' => $to_warehouse,
                    'product_code' => $pr2['product_code'],
                    'product_price' => $pr2['product_price'],
                    'fproduct_price' => $pr2['fproduct_price'],
                    'taxrate' => $pr2['taxrate'],
                    'disrate' => $pr2['disrate'],
                    'qty' => $qty,
                    'product_des' => $pr2['product_des'],
                    'alert' => $pr2['alert'],
                    'unit' => $pr2['unit'],
                    'image' => $pr2['image'],
                    'barcode' => $pr2['barcode'],
                    'merge' => 2,
                    'sub' => $row,
                    'vb' => $to_warehouse,
                    'expiry_alert_seen' => 0,
                    'expiry' => $pr2['expiry']
                );
                $this->db->insert('pos_products', $data);
                $pid = $this->db->insert_id();
                $this->movers(1, $pid, $qty, 0, 'Stock Transferred & Initialized W ' . $to_warehouse_name);
                $this->aauth->applog("[Product Transfer] -" . $pr2['product_name'] . "  -Qty-$qty  W $to_warehouse_name ID " . $pr2['pid'], $this->aauth->get_user()->username);
                
                // Update parent stock at destination warehouse if this is a variation
                if ($data['merge'] == 1) {
                    // Find parent in destination warehouse and update its stock
                    $this->db->select('pid');
                    $this->db->from('pos_products');
                    $this->db->where('pid', $data['sub']);
                    $this->db->where('warehouse', $to_warehouse);
                    $parent_dest = $this->db->get()->row_array();
                    if ($parent_dest) {
                        $this->update_parent_total_stock_at_warehouse($data['sub'], $to_warehouse);
                    }
                }
            }
            
        }

        $i++;
    }

    if ($move) {
        $this->db->update_batch('pos_products', $updateArray, 'pid');
    }

    echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('UPDATED')));
}




    public function meta_delete($name)
    {
        if (@unlink(FCPATH . 'userfiles/product/' . $name)) {
            return true;
        }
    }

    public function valid_warehouse($warehouse)
    {
        $this->db->select('id,loc');
        $this->db->from('pos_warehouse');
        $this->db->where('id', $warehouse);
        $query = $this->db->get();
        $row = $query->row_array();
        return $row;
    }



    public function movers($type = 0, $rid1 = 0, $rid2 = 0, $rid3 = 0, $note = '')
    {
        $data = array(
            'd_type' => $type,
            'rid1' => $rid1,
            'rid2' => $rid2,
            'rid3' => $rid3,
            'note' => $note
        );
        $this->db->insert('pos_movers', $data);
    }

    public function update_parent_total_stock($parent_id)
    {
        // Calculate total stock from all variations
        $this->db->select_sum('qty');
        $this->db->where('sub', $parent_id);
        $this->db->where('merge', 1);
        $query = $this->db->get('pos_products');
        $result = $query->row_array();

        $total_stock = $result['qty'] ? $result['qty'] : 0;

        // Update parent product stock
        $this->db->where('pid', $parent_id);
        return $this->db->update('pos_products', array('qty' => $total_stock));
    }

    public function update_parent_total_stock_at_warehouse($parent_id, $warehouse_id)
    {
        // Calculate total stock from all variations in the specific warehouse
        $this->db->select_sum('qty');
        $this->db->where('sub', $parent_id);
        $this->db->where('merge', 1);
        $this->db->where('warehouse', $warehouse_id);
        $query = $this->db->get('pos_products');
        $result = $query->row_array();

        $total_stock = $result['qty'] ? $result['qty'] : 0;

        // Update parent product stock at the specific warehouse
        $this->db->where('pid', $parent_id);
        $this->db->where('warehouse', $warehouse_id);
        $this->db->update('pos_products', array('qty' => $total_stock));
    }

}
