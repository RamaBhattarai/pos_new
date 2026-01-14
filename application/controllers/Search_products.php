<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Search_products extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library("Aauth");
		$this->load->model('search_model');
		if (!$this->aauth->is_loggedin()) {
			redirect('/user/', 'refresh');
		}
		if (!$this->aauth->premission(1)) {
			exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
		}
	}

//search product in invoice
	public function search()
	{
		$this->load->model('plugins_model', 'plugins');
		$billing_settings = $this->plugins->universal_api(67);
		$result = array();
		$out = array();
		$row_num = $this->input->post('row_num', true);
		$name = $this->input->post('name_startsWith', true);
		$wid = $this->input->post('wid', true);
		$qw = '';
		if ($wid > 0) {
			$qw = "(pos_products.warehouse='$wid') AND ";
		}
		if ($billing_settings['key2']) $qw .= "(pos_products.expiry IS NULL OR DATE (pos_products.expiry)<" . date('Y-m-d') . ") AND ";
		$join = '';

		if ($this->aauth->get_user()->loc) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$join2 = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			if (BDATA) $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR pos_warehouse.loc=0) AND '; else $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$qw .= '(pos_warehouse.loc=0) AND ';
		}
		$e = '';
		if ($billing_settings['key1'] == 1) {
			$e .= ',pos_product_serials.serial';
			$join .= 'LEFT JOIN pos_product_serials ON pos_product_serials.product_id=pos_products.pid';
			$qw .= '(pos_product_serials.status=0) AND ';
		}

		if ($name) {
			if ($billing_settings['key1'] == 2) {
				$e .= ',pos_product_serials.serial';
				$query = $this->db->query("SELECT pos_products.pid,pos_products.product_name,pos_products.product_price,pos_products.product_code,pos_products.taxrate,pos_products.disrate,pos_products.product_des,pos_products.qty,pos_products.unit $e  FROM pos_product_serials LEFT JOIN pos_products  ON pos_products.pid=pos_product_serials.product_id $join WHERE " . $qw . "(UPPER(pos_product_serials.serial) LIKE '" . strtoupper($name) . "%') AND (pos_products.merge=1 OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 6");
			} else {
				$query = $this->db->query("SELECT pos_products.pid,pos_products.product_name,pos_products.product_price,pos_products.product_code,pos_products.taxrate,pos_products.disrate,pos_products.product_des,pos_products.qty,pos_products.unit $e  FROM pos_products $join WHERE " . $qw . "((UPPER(pos_products.product_name) LIKE '%" . strtoupper($name) . "%') OR (UPPER(pos_products.product_code) LIKE '" . strtoupper($name) . "%')) AND (pos_products.merge=1 OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 6");
			}

			$result = $query->result_array();
			foreach ($result as $row) {
				// Check if product uses batches
				// $batch_query = $this->db->query("SELECT * FROM pos_product_batches WHERE product_id = ? AND warehouse_id = ? AND qty > 0", array($row['pid'], $wid));
				$batch_query = $this->db->query("SELECT * FROM pos_product_batches WHERE product_id = ? AND qty > 0 AND (warehouse_id = ? OR warehouse_id = 0)", array($row['pid'], $wid));
				$batches = $batch_query->result_array();
				// if (!empty($batches)) {
				// 	foreach ($batches as $batch) {
				// 		$name_arr = array(
				// 			$row['product_name'] . ' (' . $batch['batch_no'] . ')',
				// 			amountExchange_s($batch['selling_price'], 0, $this->aauth->get_user()->loc),
				// 			$row['pid'],
				// 			amountFormat_general($row['taxrate']),
				// 			amountFormat_general($row['disrate']),
				// 			$row['product_des'],
				// 			$row['unit'],
				// 			$row['product_code'],
				// 			amountFormat_general($batch['qty']),
				// 			$row_num,
				// 			null, // serial (if needed)
				// 			$batch['id'], // batchid
				// 			$batch['batch_no'], // batch_no
				// 			$batch['expiry'] // expiry
				// 		);
				// 		array_push($out, $name_arr);
				// 	}
				if (!empty($batches)) {
    foreach ($batches as $batch) {
        $name_arr = array(
            $row['product_name'] . ' (' . $batch['batch_no'] . ')',
            amountExchange_s($batch['selling_price'], 0, $this->aauth->get_user()->loc),
            $row['pid'],
            amountFormat_general($row['taxrate']),
            amountFormat_general($row['disrate']),
            $row['product_des'],
            $row['unit'],
            $row['product_code'],
            amountFormat_general($batch['qty']),
            $row_num,
            null, // serial (if needed)
            $batch['id'], // batchid
            $batch['batch_no'], // batch_no
            $batch['expiry_date'] // expiry
        );
        array_push($out, $name_arr);
    
}
				} else {
					// Non-batch product
					$name_arr = array(
						$row['product_name'],
						amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc),
						$row['pid'],
						amountFormat_general($row['taxrate']),
						amountFormat_general($row['disrate']),
						$row['product_des'],
						$row['unit'],
						$row['product_code'],
						amountFormat_general($row['qty']),
						$row_num,
						null, // serial (if needed)
						null, // batchid
						null, // batch_no
						null  // expiry
					);
					array_push($out, $name_arr);
				}
			}
			echo json_encode($out);
		}

	}

	public function puchase_search()
	{
		$result = array();
		$out = array();
		$row_num = $this->input->post('row_num', true);
		$name = $this->input->post('name_startsWith', true);
		$wid = $this->input->post('wid', true);
		$qw = '';
		if ($wid > 0) {
			$qw = "(pos_products.warehouse='$wid' ) AND ";
		}
		$join = '';
		if ($this->aauth->get_user()->loc) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			if (BDATA) $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR pos_warehouse.loc=0) AND '; else $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$qw .= '(pos_warehouse.loc=0) AND ';
		}
		if ($name) {
			$query = $this->db->query("SELECT pos_products.pid,pos_products.product_name,pos_products.product_code,pos_products.fproduct_price,pos_products.taxrate,pos_products.disrate,pos_products.product_des,pos_products.unit,pos_products.qty FROM pos_products $join WHERE " . $qw . "((UPPER(pos_products.product_name) LIKE '%" . strtoupper($name) . "%') OR (UPPER(pos_products.product_code) LIKE '" . strtoupper($name) . "%')) AND (pos_products.merge=1 OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 6");

			$result = $query->result_array();
			foreach ($result as $row) {
				$name = array($row['product_name'], amountExchange_s($row['fproduct_price'], 0, $this->aauth->get_user()->loc), $row['pid'], $row['taxrate'], $row['disrate'], $row['product_des'], $row['unit'], $row['product_code'], amountFormat_general($row['qty']), $row_num);
				array_push($out, $name);
			}
		}
		
		// Always return JSON, even if no search term or no results
		echo json_encode($out);

	}

	public function csearch()
	{
		$result = array();
		$out = array();
		$name = $this->input->get('keyword', true);
		$whr = '';
		if ($this->aauth->get_user()->loc) {
			$whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
			if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$whr = ' (loc=0) AND ';
		}
		if ($name) {
			$query = $this->db->query("SELECT id,name,address,city,phone,email,discount_c FROM pos_customers WHERE $whr (UPPER(name)  LIKE '%" . strtoupper($name) . "%' OR UPPER(phone)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
			$result = $query->result_array();
			echo '<ol>';
			$i = 1;
			foreach ($result as $row) {

				echo "<li onClick=\"selectCustomer('" . $row['id'] . "','" . $row['name'] . " ','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "','" . amountFormat_general($row['discount_c']) . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
				$i++;
			}
			echo '</ol>';
		}

	}

	public function party_search()
	{
		$result = array();
		$out = array();
		$tbl = 'pos_customers';
		$name = $this->input->get('keyword', true);

		$ty = $this->input->get('ty', true);
		if ($ty) $tbl = 'pos_supplier';
		$whr = '';


		if ($this->aauth->get_user()->loc) {
			$whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
			if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$whr = ' (loc=0) AND ';
		}


		if ($name) {
			$query = $this->db->query("SELECT id,name,address,city,phone,email FROM $tbl  WHERE $whr (UPPER(name)  LIKE '%" . strtoupper($name) . "%' OR UPPER(phone)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
			$result = $query->result_array();
			echo '<ol>';
			$i = 1;
			foreach ($result as $row) {

				echo "<li onClick=\"selectCustomer('" . $row['id'] . "','" . $row['name'] . " ','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
				$i++;
			}
			echo '</ol>';
		}

	}

	// public function pos_c_search()
	// {
	// 	$result = array();
	// 	$out = array();
	// 	$name = $this->input->get('keyword', true);
	// 	$whr = '';
	// 	if ($this->aauth->get_user()->loc) {
	// 		$whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
	// 		if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
	// 	} elseif (!BDATA) {
	// 		$whr = ' (loc=0) AND ';
	// 	}

	// 	if ($name) {
	// 		$query = $this->db->query("SELECT id,name,phone,discount_c FROM pos_customers WHERE $whr (UPPER(name)  LIKE '%" . strtoupper($name) . "%' OR UPPER(phone)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
	// 		$result = $query->result_array();
	// 		echo '<ol>';
	// 		$i = 1;
	// 		foreach ($result as $row) {
	// 			echo "<li onClick=\"PselectCustomer('" . $row['id'] . "','" . $row['name'] . " ','" . amountFormat_general($row['discount_c']) . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
	// 			$i++;
	// 		}
	// 		echo '</ol>';
	// 	}

	// }

	public function pos_c_search()
{
    $name = $this->input->get('keyword', true);
    $whr = '';
    if ($this->aauth->get_user()->loc) {
        $whr = ' (c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0) AND ';
        if (!BDATA) $whr = ' (c.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
    } elseif (!BDATA) {
        $whr = ' (c.loc=0) AND ';
    }

    if ($name) {
        // Modified query to include group title and discount
        $query = $this->db->query("
            SELECT c.id, c.name, c.phone, c.discount_c, g.title AS group_name, g.disc_rate
            FROM pos_customers c
            LEFT JOIN pos_cust_group g ON c.gid = g.id
            WHERE $whr 
                (UPPER(c.name) LIKE '%" . strtoupper($name) . "%' 
                OR UPPER(c.phone) LIKE '" . strtoupper($name) . "%') 
            LIMIT 6
        ");

        $result = $query->result_array();

        echo '<ol>';
        $i = 1;
        foreach ($result as $row) {
            $group = $row['group_name'] ?? 'Default Group';
            $disc = $row['disc_rate'] ?? '0';
            echo "<li onClick=\"PselectCustomer('" . $row['id'] . "','" . $row['name'] . "','" . amountFormat_general($row['disc_rate']) . "')\">
                    <span>$i</span>
                    <p><strong>" . $row['name'] . "</strong> &nbsp; &nbsp; " . $row['phone'] . "<br>
                    <small style='color:gray;'>" . $group . " (" . amountFormat_general($disc) . "%)</small></p>
                  </li>";
            $i++;
        }
        echo '</ol>';
    }
}



	public function supplier()
	{
		$result = array();
		$out = array();
		$name = $this->input->get('keyword', true);

		$whr = '';
		if ($this->aauth->get_user()->loc) {
			$whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
			if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$whr = ' (loc=0) AND ';
		}
		if ($name) {
			$query = $this->db->query("SELECT id,name,address,city,phone,email FROM pos_supplier WHERE $whr (UPPER(name)  LIKE '%" . strtoupper($name) . "%' OR UPPER(phone)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
			$result = $query->result_array();
			echo '<ol>';
			$i = 1;
			foreach ($result as $row) {
				echo "<li onClick=\"selectSupplier('" . $row['id'] . "','" . $row['name'] . " ','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
				$i++;
			}
			echo '</ol>';
		}

	}


	public function purchase_entry_supplier_search()
{
    $keyword = $this->input->get('keyword', true);
    if (!$keyword) {
        echo '';
        return;
    }

    $query = $this->db->query("SELECT id, name, address, phone FROM pos_supplier WHERE UPPER(name) LIKE '%" . strtoupper($keyword) . "%' OR UPPER(phone) LIKE '%" . strtoupper($keyword) . "%' LIMIT 6");
    $results = $query->result_array();

    echo '<ol>';
    $i = 1;
    foreach ($results as $row) {
        echo '<li class="supplier-select" 
                  data-id="' . $row['id'] . '" 
                  data-name="' . htmlspecialchars(trim($row['name'])) . '" 
                  data-address="' . htmlspecialchars($row['address']) . '" 
                  data-phone="' . $row['phone'] . '">
                  <span>' . $i . '</span>
                  <p>' . $row['name'] . ' &nbsp;&nbsp; ' . $row['phone'] . '</p>
              </li>';
        $i++;
    }
    echo '</ol>';
}


	public function pos_search()
	{
		$out = '';
		$this->load->model('plugins_model', 'plugins');
		$billing_settings = $this->plugins->universal_api(67);
		$name = (string)$this->input->post('name', true);
		$cid = $this->input->post('cid', true);
		$wid = $this->input->post('wid', true);
		$qw = '';
		if ($wid > 0) {
			$qw .= "(pos_products.warehouse='$wid') AND ";
		}
		if ($billing_settings['key2']) $qw .= "(pos_products.expiry IS NULL OR DATE (pos_products.expiry)<" . date('Y-m-d') . ") AND ";
		if ($cid > 0) {
			$qw .= "(pos_products.pcat='$cid') AND ";
		}
		$join = '';
		if ($this->aauth->get_user()->loc) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			if (BDATA) $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR pos_warehouse.loc=0) AND '; else $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$qw .= '(pos_warehouse.loc=0) AND ';
		}

		$e = '';
		if ($billing_settings['key1'] == 1) {
			$e .= ',pos_product_serials.serial';
			$join .= 'LEFT JOIN pos_product_serials ON pos_product_serials.product_id=pos_products.pid ';
			$qw .= '(pos_product_serials.status=0) AND  ';
		}


		$bar = '';
		if (is_numeric($name)) {
			$b = array('-', '-', '-');
			$c = array(3, 4, 11);
			$barcode = $name;
			for ($i = count($c) - 1; $i >= 0; $i--) {
				$barcode = substr_replace($barcode, $b[$i], $c[$i], 0);
			}

			$bar = " OR (pos_products.barcode LIKE '" . (substr($barcode, 0, -1)) . "%' OR pos_products.barcode LIKE '" . $name . "%')";
		}
		if ($billing_settings['key1'] == 2) {

			$query = "SELECT pos_products.*,pos_product_serials.serial FROM pos_product_serials  LEFT JOIN pos_products  ON pos_products.pid=pos_product_serials.product_id $join WHERE " . $qw . "pos_product_serials.serial LIKE '" . strtoupper($name) . "%' AND (pos_products.merge=1 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 16";


		} else {
			$query = "SELECT pos_products.* $e FROM pos_products $join WHERE " . $qw . "(UPPER(pos_products.product_name) LIKE '%" . strtoupper($name) . "%' $bar OR pos_products.product_code LIKE '" . strtoupper($name) . "%') AND (pos_products.merge=1 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 16";

		}


		$query = $this->db->query($query);

		$result = $query->result_array();
		$i = 0;
		echo '<div class="row match-height">';
		foreach ($result as $row) {
			// Use default image if product image is empty or null
			$productImage = (!empty($row['image']) && $row['image'] !== null) ? $row['image'] : 'deskgoo.png';

			$out .= '    <div class="col-3 border mb-1 "><div class="rounded">
                                 <a   id="posp' . $i . '"  class="select_pos_item btn btn-outline-light-blue round"   data-name="' . $row['product_name'] . '"  data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '"  data-tax="' . amountFormat_general($row['taxrate']) . '"  data-discount="' . amountFormat_general($row['disrate']) . '"   data-pcode="' . $row['product_code'] . '"   data-pid="' . $row['pid'] . '"  data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . @$row['serial'] . '">
                                        <img class="round"
                                             src="' . base_url('userfiles/product/' . $productImage) . '"  style="max-height: 100%;max-width: 100%">
                                        <div class="text-xs-center text">
                                       
                                            <small style="white-space: pre-wrap;">' . $row['product_name'] . '</small>

                                            
                                        </div></a>
                                  
                                </div></div>';

			$i++;
			//   if ($i % 4 == 0) $out .= '</div><div class="row">';
		}

		echo $out;

	}

	// public function get_id_from_barcode() {
	// 	$barcode = trim($this->input->post('name')); // Trim spaces
	// 	$barcode = (string)$barcode; // Ensure it's a string

	// 	if (strlen($barcode) === 13) {
	// 		$barcode = substr($barcode, 0, 12); // Remove check digit
	// 	}
	
	// 	if ($barcode) {
	// 		// Try exact match first
	// 		$this->db->select('pid');
	// 		$this->db->from('pos_products');
	// 		$this->db->where('barcode', $barcode); // ONLY exact match
	// 		$query = $this->db->get()->row();
	
	// 		if (!empty($query)) {
	// 			echo $query->pid;
	// 		} else {
	// 			// Try fuzzy match: barcode starting with the scanned input
	// 			$this->db->select('pid');
	// 			$this->db->from('pos_products');
	// 			$this->db->like('barcode', $barcode, 'after');
	// 			$query = $this->db->get()->row();
	
	// 			if (!empty($query)) {
	// 				echo $query->pid;
	// 			} else {
	// 				echo "Product not found";
	// 			}
	// 		}
	// 	} else {
	// 		echo "Invalid barcode";
	// 	}
	// }

	public function get_id_from_barcode()
{
    $barcode = trim($this->input->post('name')); // Trim input
	
    $barcode = (string)$barcode;
	$warehouse = $this->input->post('warehouse');  //  Get warehouse from POST

    $barcode12 = strlen($barcode) === 13 ? substr($barcode, 0, 12) : $barcode;

    if ($barcode12) {
        // Try exact match on 12-digit barcode
        $this->db->select('pid, product_name, product_price, product_code, taxrate, disrate, product_des, qty, unit');
        $this->db->from('pos_products');
        $this->db->where('barcode', $barcode12);
		$this->db->where('warehouse', $warehouse);  // Add warehouse condition
        $query = $this->db->get()->row();

		if (!empty($query)) {
            $pid = $query->pid;
            
            // Check if this product has batches
            $this->db->where('product_id', $pid);
            $this->db->where('(warehouse_id = ' . $warehouse . ' OR warehouse_id = 0)', NULL, FALSE);
            $this->db->where('qty >', 0);
            // Order by expiry date to get earliest expiring batch first (FEFO)
            $this->db->order_by('expiry_date', 'ASC');
            $this->db->limit(1);
            $batch = $this->db->get('pos_product_batches')->row();
            
            if ($batch) {
				// Return product ID and batch info as JSON
                echo json_encode([
                    'pid' => $pid,
                    'has_batches' => true,
                    'batch_id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'expiry_date' => $batch->expiry_date,
                    'product_name' => $query->product_name,
                    'product_price' => amountExchange_s($batch->selling_price, 0, $this->aauth->get_user()->loc),
                    'product_code' => $query->product_code,
                    'taxrate' => amountFormat_general($query->taxrate),
                    'disrate' => amountFormat_general($query->disrate),
                    'product_des' => $query->product_des,
                    'unit' => $query->unit,
                    'stock' => amountFormat_general($batch->qty)
                ]);
                return;
            }
            
            // No batches, return all product details as JSON
            echo json_encode([
                'pid' => $pid,
                'has_batches' => false,
                'product_name' => $query->product_name,
                'product_price' => amountExchange_s($query->product_price, 0, $this->aauth->get_user()->loc),
                'product_code' => $query->product_code,
                'taxrate' => amountFormat_general($query->taxrate),
                'disrate' => amountFormat_general($query->disrate),
                'product_des' => $query->product_des,
                'unit' => $query->unit,
                'stock' => amountFormat_general($query->qty)
            ]);
            return;
        }

        // Fallback: fuzzy match
        $this->db->select('pid, product_name, product_price, product_code, taxrate, disrate, product_des, qty, unit');
        $this->db->from('pos_products');
        $this->db->like('barcode', $barcode12, 'after');
		$this->db->where('warehouse', $warehouse);  //  Add warehouse condition
        $query = $this->db->get()->row();

        if (!empty($query)) {
            // Return product details as JSON (no batches found)
            echo json_encode([
                'pid' => $query->pid,
                'has_batches' => false,
                'product_name' => $query->product_name,
                'product_price' => amountExchange_s($query->product_price, 0, $this->aauth->get_user()->loc),
                'product_code' => $query->product_code,
                'taxrate' => amountFormat_general($query->taxrate),
                'disrate' => amountFormat_general($query->disrate),
                'product_des' => $query->product_des,
                'unit' => $query->unit,
                'stock' => amountFormat_general($query->qty)
            ]);
        } else {
            echo json_encode(["error" => "Product not found"]);
        }
    } else {
        echo json_encode(["error" => "Invalid barcode"]);
    }
}




	public function v2_pos_search()
	{

		$out = '';
		$this->load->model('plugins_model', 'plugins');
		$billing_settings = $this->plugins->universal_api(67);
		$name = (string)$this->input->post('name', true);
		$cid = (int)$this->input->post('cid', true);
		$wid = (int)$this->input->post('wid', true);
		$enable_bar = (string)$this->input->post('bar', true);
		$flag_p = false;
		
		// DEBUG: Log input parameters
		error_log("POS Search Debug - Input - name: '$name', cid: $cid, wid: $wid, enable_bar: '$enable_bar'");
		error_log("POS Search Debug - User Location: " . $this->aauth->get_user()->loc);
		error_log("POS Search Debug - BDATA: " . (BDATA ? 'true' : 'false'));

		$qw = '';

		if ($wid > 0) {
			$qw .= "(pos_products.warehouse='$wid') AND ";
		}
		if ($billing_settings['key2']) $qw .= "(pos_products.expiry IS NULL OR DATE (pos_products.expiry)<" . date('Y-m-d') . ") AND ";
		if ($cid > 0) {
			$qw .= "(pos_products.pcat='$cid') AND ";
		}
		$join = '';

		if ($this->aauth->get_user()->loc) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			if (BDATA) $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR pos_warehouse.loc=0) AND '; else $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$qw .= '(pos_warehouse.loc=0) AND ';
		}

		$e = '';
		if ($billing_settings['key1'] == 1) {
			$e .= ',pos_product_serials.serial';
			$join .= 'LEFT JOIN pos_product_serials ON pos_product_serials.product_id=pos_products.pid ';
			$qw .= '(pos_product_serials.status=0) AND  ';
		}

		$bar = '';
		$p_class = 'v2_select_pos_item';
		if ($enable_bar == 'true' and is_numeric($name) and strlen($name) >= 8) {
			$flag_p = true;
			$bar = " (pos_products.barcode = '" . (substr($name, 0, -1)) . "' OR pos_products.barcode LIKE '" . $name . "%')";
			$query = "SELECT pos_products.*  FROM pos_products $join WHERE " . $qw . "$bar AND (pos_products.merge=0 AND pos_products.pid IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0) OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) ORDER BY pos_products.product_name";
			$p_class = 'v2_select_pos_item_bar';
		} 
		elseif ($enable_bar == 'false' or !$enable_bar) {
			$flag_p = true;
			if ($billing_settings['key1'] == 2) {
				$query = "SELECT pos_products.*,pos_product_serials.serial FROM pos_product_serials  LEFT JOIN pos_products  ON pos_products.pid=pos_product_serials.product_id $join WHERE " . $qw . "pos_product_serials.serial LIKE '" . strtoupper($name) . "%' AND (pos_products.merge=0 AND pos_products.pid IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0) OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 18";
			} else {
				$query = "SELECT pos_products.* $e FROM pos_products $join WHERE " . $qw . "(UPPER(pos_products.product_name) LIKE '%" . strtoupper($name) . "%' $bar OR pos_products.product_code LIKE '" . strtoupper($name) . "%') AND (pos_products.merge=0 AND pos_products.pid IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0) OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) ORDER BY pos_products.product_name LIMIT 18";
			}
		}
		elseif (trim($name) === '') {
			$flag_p = true;
			$query = "SELECT pos_products.* $e FROM pos_products $join WHERE " . $qw . 
				 "(pos_products.merge=0 AND pos_products.pid IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0) OR pos_products.merge=2 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) ORDER BY pos_products.product_name LIMIT 30";
		}

		if ($flag_p) {
			// DEBUG: Log the query and parameters
			error_log("POS Search Debug - Query: " . $query);
			error_log("POS Search Debug - WID: " . $wid);
			error_log("POS Search Debug - CID: " . $cid);
			
			$query = $this->db->query($query);
			$result = $query->result_array();
			
			// DEBUG: Log the result count
			error_log("POS Search Debug - Results found: " . count($result));

			$i = 0;
			$out = '<div class="row match-height">';
			foreach ($result as $row) {
				// Use default image if product image is empty or null
				$productImage = (!empty($row['image']) && $row['image'] !== null) ? $row['image'] : 'deskgoo.png';
				
				// Check if this is a parent product with variations
				$has_variations = '0';
				if ($row['merge'] == 0) {
					// Check if this product has variations (products with merge=1 and sub = this pid)
					$variation_check = $this->db->query("SELECT COUNT(*) as count FROM pos_products WHERE merge=1 AND sub = " . $row['pid'])->row();
					if ($variation_check && $variation_check->count > 0) {
						$has_variations = '1';
					}
				}
				
				// Determine product_id for batch lookup (use pid, as batches should be in the current warehouse)
				$product_id_for_batch = $row['pid'];
				
				// Check if product uses batches
				$batch_query = $this->db->query("SELECT * FROM pos_product_batches WHERE product_id = ? AND qty > 0 AND (warehouse_id = ? OR warehouse_id = 0)", array($product_id_for_batch, $wid));
				$batches = $batch_query->result_array();

				if (!empty($batches)) {
					foreach ($batches as $batch) {
						$product_name = $row['product_name'] . ' (' . $batch['batch_no'] . ')';
						$out .= '    <div class="col-2 border mb-1"><div class="rounded">
<a id="posp' . $row['pid'] . '_' . $batch['id'] . '" class="' . $p_class . ' round" data-name="' . $product_name . '" data-price="' . amountExchange_s($batch['selling_price'], 0, $this->aauth->get_user()->loc) . '" data-tax="' . amountFormat_general($row['taxrate']) . '" data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '" data-pid="' . $row['pid'] . '" data-stock="' . amountFormat_general($batch['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . (isset($row['serial']) ? $row['serial'] : '') . '" data-bar="' . (isset($row['barcode']) ? $row['barcode'] : '') . '" data-has-variations="' . $has_variations . '" data-batchid="' . $batch['id'] . '" data-batchno="' . $batch['batch_no'] . '" data-expiry="' . $batch['expiry_date'] . '">
<img class="round" src="' . base_url('userfiles/product/' . $productImage) . '" style="max-height: 100%;max-width: 100%">
<div class="text-center" style="margin-top: 4px;"><small style="white-space: pre-wrap;">' . $product_name . '</small></div></a></div></div>';
					}
				} else {
					$product_name = $row['product_name'];
					$out .= '    <div class="col-2 border mb-1"><div class="rounded">
<a id="posp' . $row['pid'] . '" class="' . $p_class . ' round" data-name="' . $product_name . '" data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '" data-tax="' . amountFormat_general($row['taxrate']) . '" data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '" data-pid="' . $row['pid'] . '" data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . (isset($row['serial']) ? $row['serial'] : '') . '" data-bar="' . (isset($row['barcode']) ? $row['barcode'] : '') . '" data-has-variations="' . $has_variations . '">
<img class="round" src="' . base_url('userfiles/product/' . $productImage) . '" style="max-height: 100%;max-width: 100%">
<div class="text-center" style="margin-top: 4px;"><small style="white-space: pre-wrap;">' . $product_name . '</small></div></a></div></div>';
				}
			}
			$out .= '</div>';
			
			// DEBUG: Log the final output
			error_log("POS Search Debug - Final output length: " . strlen($out));
			
			echo $out;
		}
	}
	
	// TEMPORARY DEBUG METHOD - Remove after testing
	public function debug_warehouse_products() {
		$wid = $this->input->get('wid') ?: 4; // Default to Main Warehouse
		
		$query = "SELECT pid, product_name, warehouse, qty, merge, sub FROM pos_products WHERE warehouse = $wid";
		$result = $this->db->query($query)->result_array();
		
		echo "<h3>Debug: Products in Warehouse $wid</h3>";
		echo "<pre>";
		print_r($result);
		echo "</pre>";
		
		// Test the exact query used in v2_pos_search
		$qw = "(pos_products.warehouse='$wid') AND ";
		$testQuery = "SELECT pos_products.* FROM pos_products WHERE $qw (pos_products.merge=1 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0)))";
		
		echo "<h3>Debug: v2_pos_search Query Results</h3>";
		echo "<p>Query: $testQuery</p>";
		$testResult = $this->db->query($testQuery)->result_array();
		echo "<pre>";
		print_r($testResult);
		echo "</pre>";
	}

	public function get_product_variations_popup()
	{
		$pid = $this->input->post('pid');
		$wid = $this->input->post('wid');
		$this->load->model('plugins_model', 'plugins');
		$billing_settings = $this->plugins->universal_api(67);

		$qw = '';
		if ($wid > 0) {
			$qw .= "(pos_products.warehouse='$wid') AND ";
		}
		if ($billing_settings['key2']) $qw .= "(pos_products.expiry IS NULL OR DATE (pos_products.expiry)<" . date('Y-m-d') . ") AND ";

		$join = '';
		if ($this->aauth->get_user()->loc) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			if (BDATA) $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR pos_warehouse.loc=0) AND '; else $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$qw .= '(pos_warehouse.loc=0) AND ';
		}

		$query = "SELECT pos_products.* FROM pos_products $join WHERE " . $qw . "pos_products.sub = $pid AND pos_products.merge=1 ORDER BY pos_products.product_name";

		$result = $this->db->query($query)->result_array();

		$out = '<div class="container-fluid">';
		foreach ($result as $row) {
			$productImage = (!empty($row['image']) && $row['image'] !== null) ? $row['image'] : 'deskgoo.png';

			// Check if this variation has batches
			$batch_query = $this->db->query("SELECT * FROM pos_product_batches WHERE product_id = ? AND qty > 0 AND (warehouse_id = ? OR warehouse_id = 0)", array($row['pid'], $wid));
			$batches = $batch_query->result_array();

			if (!empty($batches)) {
				foreach ($batches as $batch) {
					$product_name = $row['product_name'] . ' (' . $batch['batch_no'] . ')';
					$expiry_info = '';
					if (!empty($batch['expiry_date'])) {
						$expiry_date = date('d/m/Y', strtotime($batch['expiry_date']));
						$expiry_info = '<small class="text-muted">Exp: ' . $expiry_date . '</small>';
					}
					$out .= '<div class="row mb-2 p-2 border rounded">
						<div class="col-4 p-1">
							<img class="img-fluid rounded" src="' . base_url('userfiles/product/' . $productImage) . '" alt="' . $product_name . '" style="width: 100%; height: 60px; object-fit: contain; background: #f8f9fa;">
						</div>
						<div class="col-8 p-1">
							<h6 class="mb-1" style="font-size: 12px; font-weight: 600;">' . $product_name . '</h6>
							<div class="mb-1">
								<span class="badge badge-success mr-1" style="font-size: 11px;">' . amountExchange_s($batch['selling_price'], 0, $this->aauth->get_user()->loc) . '</span>
								<span class="badge badge-info" style="font-size: 11px;">Stock: ' . amountFormat_general($batch['qty']) . '</span>
							</div>
							' . $expiry_info . '
							<button class="btn btn-primary btn-sm float-right select-variation" style="font-size: 11px; padding: 3px 10px;" data-name="' . $product_name . '" data-price="' . amountExchange_s($batch['selling_price'], 0, $this->aauth->get_user()->loc) . '" data-tax="' . amountFormat_general($row['taxrate']) . '" data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '" data-pid="' . $row['pid'] . '" data-stock="' . amountFormat_general($batch['qty']) . '" data-unit="' . $row['unit'] . '" data-batchid="' . $batch['id'] . '" data-batchno="' . $batch['batch_no'] . '" data-expiry="' . $batch['expiry_date'] . '">Select</button>
						</div>
					</div>';
				}
			} else {
				$product_name = $row['product_name'];
				$out .= '<div class="row mb-2 p-2 border rounded">
					<div class="col-4 p-1">
						<img class="img-fluid rounded" src="' . base_url('userfiles/product/' . $productImage) . '" alt="' . $product_name . '" style="width: 100%; height: 60px; object-fit: contain; background: #f8f9fa;">
					</div>
					<div class="col-8 p-1">
						<h6 class="mb-1" style="font-size: 12px; font-weight: 600;">' . $product_name . '</h6>
						<div class="mb-1">
							<span class="badge badge-success mr-1" style="font-size: 11px;">' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '</span>
							<span class="badge badge-info" style="font-size: 11px;">Stock: ' . amountFormat_general($row['qty']) . '</span>
						</div>
						<button class="btn btn-primary btn-sm float-right select-variation" style="font-size: 11px; padding: 3px 10px;" data-name="' . $product_name . '" data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '" data-tax="' . amountFormat_general($row['taxrate']) . '" data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '" data-pid="' . $row['pid'] . '" data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '">Select</button>
					</div>
				</div>';
			}
		}
		$out .= '</div>';

		echo $out;
	}

	public function group_pos_search()
	{

		$out = '';
		$this->load->model('plugins_model', 'plugins');
		$billing_settings = $this->plugins->universal_api(67);
		$name = $this->input->post('name', true);
		$cid = $this->input->post('cid', true);
		$wid = $this->input->post('wid', true);


		$qw = '';

		if ($wid > 0) {
			$qw .= "(pos_product_groups.warehouse='$wid') AND ";
		}

		$join = '';

		if ($this->aauth->get_user()->loc) {
			$qw .= "(pos_product_groups.loc='" . $this->aauth->get_user()->loc . "') AND ";
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			if (BDATA) $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR pos_warehouse.loc=0) AND '; else $qw .= '(pos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
		} elseif (!BDATA) {
			$join = 'LEFT JOIN pos_warehouse ON pos_warehouse.id=pos_products.warehouse';
			$qw .= '(pos_warehouse.loc=0) AND ';
		}

		$e = '';
		if ($billing_settings['key1'] == 1) {
			$e .= ',pos_product_serials.serial';
			$join .= 'LEFT JOIN pos_product_serials ON pos_product_serials.product_id=pos_products.pid ';
			$qw .= '(pos_product_serials.status=0) AND  ';
		}

		$bar = '';

		if (is_numeric($name)) {
			$b = array('-', '-', '-');
			$c = array(3, 4, 11);
			$barcode = $name;
			for ($i = count($c) - 1; $i >= 0; $i--) {
				$barcode = substr_replace($barcode, $b[$i], $c[$i], 0);
			}
			//    echo(substr($barcode, 0, -1));
			$bar = " OR (pos_products.barcode LIKE '" . (substr($barcode, 0, -1)) . "%' OR pos_products.barcode LIKE '" . $name . "%')";
			//  $query = "SELECT pos_products.* FROM pos_products $join WHERE " . $qw . " $bar AND (pos_products.qty>0) LIMIT 16";
		}
		if ($billing_settings['key1'] == 2) {

			$query = "SELECT pos_products.*,pos_product_serials.serial FROM pos_product_serials  LEFT JOIN pos_products  ON pos_products.pid=pos_product_serials.product_id $join WHERE " . $qw . "pos_product_serials.serial LIKE '" . strtoupper($name) . "%' AND (pos_products.merge=1 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) LIMIT 18";

		} else {
			$query = "SELECT pos_products.* $e FROM pos_products $join WHERE " . $qw . "(UPPER(pos_products.product_name) LIKE '%" . strtoupper($name) . "%' $bar OR pos_products.product_code LIKE '" . strtoupper($name) . "%') AND (pos_products.merge=1 OR (pos_products.merge=0 AND pos_products.pid NOT IN (SELECT DISTINCT sub FROM pos_products WHERE merge=1 AND sub IS NOT NULL AND sub>0))) ORDER BY pos_products.product_name LIMIT 18";
		}

		$query = $this->db->query($query);
		$result = $query->result_array();
		$i = 0;
		echo '<div class="row match-height">';
		foreach ($result as $row) {
			// Use default image if product image is empty or null
			$productImage = (!empty($row['image']) && $row['image'] !== null) ? $row['image'] : 'deskgoo.png';
			

					$out .= '    <div class="col-2 border mb-1"  ><div class=" rounded" >
                                 <a  id="posp' . $i . '"  class="v2_select_pos_item round"   data-name="' . $row['product_name'] . '"  data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '"  data-tax="' . amountFormat_general($row['taxrate']) . '"  data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '"   data-pid="' . $row['pid'] . '"  data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . @$row['serial'] . '">
                                        <img class="round"
                                             src="' . base_url('userfiles/product/' . $productImage) . '"  style="max-height: 100%;max-width: 100%">
                                        <div class="text-center" style="margin-top: 4px;">
                                       
                                            <small style="white-space: pre-wrap;">' . $row['product_name'] . '</small>

                                            
                                        </div></a>
                                  
                                </div></div>';

			$i++;

		}

		echo $out;

	}

}
