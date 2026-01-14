<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model
{

    var $table = 'pos_supplier';
    var $column_order = array(null, 'name', 'address', 'email', 'phone', null);
    var $column_search = array('name', 'phone', 'address', 'city', 'email');
    var $trans_column_order = array('date', 'debit', 'credit', 'account', null);
    var $trans_column_search = array('id', 'date');
    var $inv_column_order = array(null, 'tid', 'name', 'invoicedate', 'total', 'status', null);
    var $inv_column_search = array('tid', 'name', 'invoicedate', 'total');
    var $order = array('id' => 'desc');
    var $purchase_order = array('pos_purchase.tid' => 'desc');


    private function _get_datatables_query($id = '')
    {

        $this->db->from($this->table);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        if ($id != '') {
            $this->db->where('gid', $id);
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

    function get_datatables($id = '')
    {
        $this->_get_datatables_query($id);
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($id = '')
    {
        $this->_get_datatables_query();
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        if ($id != '') {
            $this->db->where('gid', $id);
        }
        $query = $this->db->get();

        return $query->num_rows($id = '');
    }

    public function count_all($id = '')
    {
        $this->_get_datatables_query();
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        if ($id != '') {
            $this->db->where('gid', $id);
        }
        return $query->num_rows($id = '');
    }

    public function details($custid)
    {

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $custid);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function money_details($custid)
    {

        $this->db->select('SUM(debit) AS debit,SUM(credit) AS credit');
        $this->db->from('pos_transactions');
        $this->db->where('payerid', $custid);
        $this->db->where('ext', 1);
        $query = $this->db->get();
        return $query->row_array();
    }


    public function add($name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $taxid, $confirmation_threshold = null, $confirmation_method = null, $confirmation_contact = null)
    {
        $data = array(
            'name' => $name,
            'company' => $company,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'postbox' => $postbox,
            'taxid' => $taxid
        );

        // Add party confirmation fields if provided
        if ($confirmation_threshold !== null) {
            $data['confirmation_threshold'] = $confirmation_threshold;
        }
        if ($confirmation_method !== null) {
            $data['confirmation_method'] = $confirmation_method;
        }
        if ($confirmation_contact !== null) {
            $data['confirmation_contact'] = $confirmation_contact;
        }

        if ($this->aauth->get_user()->loc) {
            $data['loc'] = $this->aauth->get_user()->loc;
        }


        if ($this->db->insert('pos_supplier', $data)) {
            $cid = $this->db->insert_id();
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED') . ' <a href="' . base_url('supplier/view?id=' . $cid) . '" class="btn btn-info btn-sm"><span class="icon-eye"></span> ' . $this->lang->line('View') . '</a>', 'cid' => $cid));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


    public function edit($id, $name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $taxid, $confirmation_threshold = null, $confirmation_method = null, $confirmation_contact = null)
    {
        $data = array(
            'name' => $name,
            'company' => $company,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'postbox' => $postbox,
            'taxid' => $taxid

        );

        // Add party confirmation fields if provided
        if ($confirmation_threshold !== null) {
            $data['confirmation_threshold'] = $confirmation_threshold;
        }
        if ($confirmation_method !== null) {
            $data['confirmation_method'] = $confirmation_method;
        }
        if ($confirmation_contact !== null) {
            $data['confirmation_contact'] = $confirmation_contact;
        }


        $this->db->set($data);
        $this->db->where('id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }

        if ($this->db->update('pos_supplier')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function editpicture($id, $pic)
    {
        $this->db->select('picture');
        $this->db->from($this->table);
        $this->db->where('id', $id);

        $query = $this->db->get();
        $result = $query->row_array();


        $data = array(
            'picture' => $pic
        );


        $this->db->set($data);
        $this->db->where('id', $id);
        if ($this->db->update('pos_supplier')) {

            unlink(FCPATH . 'userfiles/supplier/' . $result['picture']);
            unlink(FCPATH . 'userfiles/supplier/thumbnail/' . $result['picture']);
        }


    }

    public function group_list()
    {
        $query = $this->db->query("SELECT c.*,p.pc FROM pos_cust_group AS c LEFT JOIN ( SELECT gid,COUNT(gid) AS pc FROM pos_supplier GROUP BY gid) AS p ON p.gid=c.id");
        return $query->result_array();
    }

    public function delete($id)
    {

        return $this->db->delete('pos_supplier', array('id' => $id));
    }


    //transtables

    function trans_table($id)
    {
        $this->_get_trans_table_query($id);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }


    private function _get_trans_table_query($id)
    {

        $this->db->from('pos_transactions');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }

        $this->db->where('payerid', $id);
        $this->db->where('ext', 1);

        $i = 0;

        foreach ($this->trans_column_search as $item) // loop column
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

                if (count($this->trans_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) // here order processing
        {
            $this->db->order_by($this->trans_column_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function trans_count_filtered($id = '')
    {
        $this->_get_trans_table_query($id);
        $query = $this->db->get();
        if ($id != '') {
            $this->db->where('payerid', $id);
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        return $query->num_rows($id = '');
    }

    public function trans_count_all($id = '')
    {
        $this->_get_trans_table_query($id);
        $query = $this->db->get();
        if ($id != '') {
            $this->db->where('payerid', $id);
        }


    }

    private function _inv_datatables_query($id)
    {
        $this->db->select('pos_purchase.*');
        $this->db->from('pos_purchase');
        $this->db->where('pos_purchase.csd', $id);
        $this->db->join('pos_supplier', 'pos_purchase.csd=pos_supplier.id', 'left');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('pos_purchase.loc', 0);
        }
        $i = 0;

        foreach ($this->inv_column_search as $item) // loop column
        {
            if ($this->input->post('search')['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $this->input->post('search')['value']);
                } else {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }

                if (count($this->inv_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->inv_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->inv_order)) {
            $order = $this->inv_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function inv_datatables($id)
    {
        $this->_inv_datatables_query($id);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function inv_count_filtered($id)
    {
        $this->_inv_datatables_query($id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('pos_purchase.loc', 0);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function inv_count_all($id)
    {
        $this->db->from('pos_purchase');
        $this->db->where('csd', $id);
        return $this->db->count_all_results();
    }

    public function group_info($id)
    {

        $this->db->from('pos_cust_group');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function sales_due($sdate, $edate, $csd, $trans_type, $pay = true, $amount = 0, $acc = 0, $pay_method = '', $note = '')
    {
        if ($pay) {
            $this->db->select_sum('total');
            $this->db->select_sum('pamnt');
            $this->db->from('pos_purchase');
            $this->db->where('DATE(invoicedate) >=', $sdate);
            $this->db->where('DATE(invoicedate) <=', $edate);
            $this->db->where('csd', $csd);
            $this->db->where('status', $trans_type);
            if ($this->aauth->get_user()->loc) {
                $this->db->where('loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }

            $query = $this->db->get();
            $result = $query->row_array();
            return $result;
        } else {
            if ($amount) {
                $this->db->select('id,tid,total,pamnt');
                $this->db->from('pos_purchase');
                $this->db->where('DATE(invoicedate) >=', $sdate);
                $this->db->where('DATE(invoicedate) <=', $edate);
                $this->db->where('csd', $csd);
                $this->db->where('status', $trans_type);
                if ($this->aauth->get_user()->loc) {
                    $this->db->where('loc', $this->aauth->get_user()->loc);
                } elseif (!BDATA) {
                    $this->db->where('loc', 0);
                }

                $query = $this->db->get();
                $result = $query->result_array();
                $amount_custom = $amount;

                foreach ($result as $row) {
                    $note .= ' #' . $row['tid'];
                    $due = $row['total'] - $row['pamnt'];
                    if ($amount_custom >= $due) {
                        $this->db->set('status', 'paid');
                        $this->db->set('pamnt', "pamnt+$due", FALSE);
                        $amount_custom = $amount_custom - $due;
                    } elseif ($amount_custom > 0 AND $amount_custom < $due) {
                        $this->db->set('status', 'partial');
                        $this->db->set('pamnt', "pamnt+$amount_custom", FALSE);
                        $amount_custom = 0;
                    }

                    $this->db->set('pmethod', $pay_method);
                    $this->db->where('id', $row['id']);
                    $this->db->update('pos_purchase');

                    if ($amount_custom == 0) break;

                }
                $this->db->select('id,holder');
                $this->db->from('pos_accounts');
                $this->db->where('id', $acc);
                $query = $this->db->get();
                $account = $query->row_array();

                $data = array(
                    'acid' => $account['id'],
                    'account' => $account['holder'],
                    'type' => 'Income',
                    'cat' => 'Sales',
                    'debit' => $amount,
                    'payer' => $this->lang->line('Bulk Payment'),
                    'payerid' => $csd,
                    'method' => $pay_method,
                    'date' => date('Y-m-d'),
                    'eid' => $this->aauth->get_user()->id,
                    'tid' => 0,
                    'ext' => 1,
                    'note' => $note,
                    'loc' => $this->aauth->get_user()->loc
                );

                $this->db->insert('pos_transactions', $data);
                $tttid = $this->db->insert_id();
                $this->db->set('lastbal', "lastbal-$amount", FALSE);
                $this->db->where('id', $account['id']);
                $this->db->update('pos_accounts');

            }

        }
    }

    
    // PARTY CONFIRMATION METHODS
    
    
 /**
     * Check if supplier needs confirmation based on total purchases
     */
public function check_confirmation_threshold($supplier_id)
{
    // Get supplier's threshold and check in one query
    $this->db->select('confirmation_threshold, confirmation_enabled, name, last_confirmation_date');
    $this->db->from('pos_supplier');
    $this->db->where('id', $supplier_id);
    $supplier = $this->db->get()->row_array();

    // Early returns for invalid cases
    if (!$supplier || 
        (isset($supplier['confirmation_enabled']) && !$supplier['confirmation_enabled']) ||
        !isset($supplier['confirmation_threshold']) || 
        $supplier['confirmation_threshold'] <= 0) {
        return false;
    }

    // Calculate period efficiently
    $since_date = $supplier['last_confirmation_date'] ?: '2020-01-01';
    
    // Single query for purchases total
    $this->db->select('COALESCE(SUM(total), 0) as total_purchases');
    $this->db->from('pos_purchase_entries');
    $this->db->where('csd', $supplier_id);
    $this->db->where('invoicedate >=', $since_date);
    $this->db->where('status !=', 'canceled');
    $result = $this->db->get()->row_array();

    // Simple comparison
    return ($result['total_purchases'] ?: 0) >= $supplier['confirmation_threshold'];
}


  
    /**
     * Update supplier's total purchase amount (called after each purchase)
     */
    public function update_purchase_total($supplier_id, $amount)
    {
        $this->db->set('total_purchase_amount', "total_purchase_amount + $amount", FALSE);
        $this->db->where('id', $supplier_id);
        $this->db->update('pos_supplier');
    }


    /**
     * Get party confirmation settings
     */
    public function get_confirmation_settings()
    {
        $this->db->select('setting_key, setting_value');
        $this->db->from('pos_party_confirmation_settings');
        $result = $this->db->get()->result_array();
        
        $settings = [];
        foreach ($result as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }

    
 // Get suppliers that need party confirmation alerts

public function get_suppliers_needing_confirmation_alerts()
{
    // Simplified version that excludes suppliers with existing confirmations
    $this->db->select('s.id, s.name, s.company, s.confirmation_threshold');
    $this->db->from('pos_supplier s');
    $this->db->where('s.confirmation_threshold >', 0);
    
    if ($this->aauth->get_user()->loc) {
        $this->db->where('s.loc', $this->aauth->get_user()->loc);
    } elseif (!BDATA) {
        $this->db->where('s.loc', 0);
    }
    
    $suppliers = $this->db->get()->result_array();
    
    if (empty($suppliers)) {
        return [];
    }
    
    $alerts = [];
    
    // Get current date
    $current_date = date('Y-m-d');
    
    foreach ($suppliers as $supplier) {
        // Check if supplier already has a confirmation letter generated for current period
        $this->db->select('COUNT(*) as existing_confirmations');
        $this->db->from('pos_party_confirmations');
        $this->db->where('supplier_id', $supplier['id']);
        $this->db->where('status IN (\'generated\', \'sent\')', null, false);
        // Check for current fiscal year or recent period
        $this->db->where('confirmation_period_end >=', date('Y-m-d', strtotime('-6 months')));
        $confirmation_check = $this->db->get()->row_array();
        
        // Skip this supplier if they already have a recent confirmation
        if ($confirmation_check['existing_confirmations'] > 0) {
            continue;
        }
        
        // Calculate total purchases (all time)
        $this->db->select('COALESCE(SUM(total), 0) as total_purchases, COUNT(*) as order_count');
        $this->db->from('pos_purchase_entries');
        $this->db->where('csd', $supplier['id']);
        $this->db->where('status !=', 'canceled');
        $purchase_result = $this->db->get()->row_array();
        
        $total_purchases = $purchase_result['total_purchases'] ?: 0;
        
        // Simple threshold check
        if ($total_purchases >= $supplier['confirmation_threshold']) {
            // Set confirmation period (current date)
            $supplier['period_start'] = $current_date;
            $supplier['period_end'] = $current_date;
            
            $supplier['total_purchases'] = $total_purchases;
            $supplier['order_count'] = $purchase_result['order_count'];
            $alerts[] = $supplier;
        }
    }
    
    return $alerts;
}


    /**
     * Get all party confirmations
     */ 

public function get_all_party_confirmations()
{
    try {
        // Check if table exists using more efficient method
        if (!$this->db->table_exists('pos_party_confirmations')) {
            return [];
        }
        
        $this->db->select('pc.*, s.name as supplier_name, s.company');
        $this->db->from('pos_party_confirmations pc');
        $this->db->join('pos_supplier s', 'pc.supplier_id = s.id', 'left');
        
        if ($this->aauth->get_user()->loc) {
            $this->db->where('s.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('s.loc', 0);
        }
        
        $this->db->order_by('pc.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    } catch (Exception $e) {
        log_message('error', 'Error in get_all_party_confirmations: ' . $e->getMessage());
        return [];
    }
}

    
 /**
     * Generate confirmation letter
     */
public function generate_confirmation_letter($supplier_id, $amount, $period_start, $period_end)
{
    // Delete any existing confirmations for this supplier and period first
    $this->db->where('supplier_id', $supplier_id);
    $this->db->where('confirmation_period_start', $period_start);
    $this->db->where('confirmation_period_end', $period_end);
    $this->db->delete('pos_party_confirmations');
    
    // Insert new confirmation record
    $now = new DateTime('now', new DateTimeZone('Asia/Kathmandu'));
    $data = [
        'supplier_id' => $supplier_id,
        'total_amount' => $amount,
        'confirmation_period_start' => $period_start,
        'confirmation_period_end' => $period_end,
        'status' => 'generated',
        'generated_date' => $now->format('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => $this->aauth->get_user()->id
    ];
    
    $this->db->insert('pos_party_confirmations', $data);
    $confirmation_id = $this->db->insert_id();
    
    return [
        'status' => 'success',
        'confirmation_id' => $confirmation_id,
        'message' => 'Confirmation letter generated successfully',
        'action' => 'created'
    ];
}

    
 // Mark confirmation as sent 
      
public function mark_confirmation_sent_by_id($confirmation_id)
{
    try {
        $now = new DateTime('now', new DateTimeZone('Asia/Kathmandu'));
        $data = [
            'status' => 'sent',
            'sent_date' => $now->format('Y-m-d H:i:s'),
            'created_by' => $this->aauth->get_user()->id
        ];
        
        $this->db->where('id', $confirmation_id);
        $updated = $this->db->update('pos_party_confirmations', $data);
        
        if ($updated) {
            // Also update the supplier's last_confirmation_date
            $this->db->select('supplier_id');
            $this->db->from('pos_party_confirmations');
            $this->db->where('id', $confirmation_id);
            $confirmation = $this->db->get()->row_array();
            
            if ($confirmation) {
                $this->db->where('id', $confirmation['supplier_id']);
                $this->db->update('pos_supplier', ['last_confirmation_date' => date('Y-m-d')]);
            }
            
            return ['status' => 'success', 'message' => 'Confirmation marked as sent'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update confirmation status'];
        }
    } catch (Exception $e) {
        log_message('error', 'Error in mark_confirmation_sent_by_id: ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
}

public function delete_confirmation_by_id($confirmation_id)
{
    try {
        // Validate confirmation ID
        if (empty($confirmation_id) || !is_numeric($confirmation_id)) {
            return ['status' => 'error', 'message' => 'Invalid confirmation ID'];
        }
        
        // Check if confirmation exists
        $this->db->select('id, supplier_id');
        $this->db->from('pos_party_confirmations');
        $this->db->where('id', $confirmation_id);
        $confirmation = $this->db->get()->row_array();
        
        if (!$confirmation) {
            return ['status' => 'error', 'message' => 'Confirmation record not found'];
        }
        
        // Delete the confirmation record
        $this->db->where('id', $confirmation_id);
        $deleted = $this->db->delete('pos_party_confirmations');
        
        if ($deleted) {
            return ['status' => 'success', 'message' => 'Confirmation deleted successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete confirmation'];
        }
    } catch (Exception $e) {
        log_message('error', 'Error in delete_confirmation_by_id: ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
}

    /**
     * Get supplier list for dropdown
     */
    public function supplier_list()
    {
        try {
            $this->db->select('id, name, company');
            $this->db->from('pos_supplier');
            
            if ($this->aauth->get_user()->loc) {
                $this->db->where('loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            
            $this->db->order_by('name', 'ASC');
            $query = $this->db->get();
            return $query->result_array();
        } catch (Exception $e) {
            // Log error and return empty array instead of failing
            log_message('error', 'Error in supplier_list: ' . $e->getMessage());
            return array();
        }
    }


    

}
