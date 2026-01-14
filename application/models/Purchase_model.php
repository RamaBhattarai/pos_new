<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model
{
    var $table = 'pos_purchase';
    var $column_order = array(null, 'pos_purchase.tid', 'pos_supplier.name', 'pos_purchase.invoicedate', 'pos_purchase.total', 'pos_purchase.status', null);
    var $column_search = array('pos_purchase.tid', 'pos_supplier.name', 'pos_purchase.invoicedate', 'pos_purchase.total','pos_purchase.status');
    var $order = array('pos_purchase.tid' => 'desc');

    // Purchase Entry specific properties
    var $table_entries = 'pos_purchase_entries';
    var $column_order_entries = array(null, 'pos_purchase_entries.tid', 'pos_supplier.name', 'pos_purchase_entries.invoicedate', 'pos_purchase_entries.total', 'pos_purchase_entries.status', null);
    var $column_search_entries = array('pos_purchase_entries.tid', 'pos_supplier.name', 'pos_purchase_entries.invoicedate', 'pos_purchase_entries.total','pos_purchase_entries.status');
    var $order_entries = array('pos_purchase_entries.tid' => 'desc');


    public function __construct()
    {
        parent::__construct();
    }

    public function lastpurchase()
    {
        $this->db->select('tid');
        $this->db->from($this->table);
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->tid;
        } else {
            return 1000;
        }
    }

    public function warehouses()
    {
        $this->db->select('*');
        $this->db->from('pos_warehouse');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->result_array();

    }

    public function purchase_details($id)
    {

        $this->db->select('pos_purchase.*,pos_purchase.id AS iid,SUM(pos_purchase.shipping + pos_purchase.ship_tax) AS shipping,pos_supplier.*,pos_supplier.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('pos_purchase.id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_purchase.loc', 0);
        } elseif (!BDATA) {
            $this->db->where('pos_purchase.loc', 0);
        }
        $this->db->join('pos_supplier', 'pos_purchase.csd = pos_supplier.id', 'left');
        $this->db->join('pos_terms', 'pos_terms.id = pos_purchase.term', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

    public function purchase_products($id)
{
    $this->db->select('
        p.product_name        AS product,
        pi.pid,
        pi.qty,
        pi.price              AS rate,
        pi.price              AS purchase_price,
        pi.selling_price,
        pi.tax                AS tax_percent,
        pi.discount,
        pi.profit,
        pi.profit_margin,
        pi.batch_no           AS batch,
        pi.expiry_date        AS expiry
    ');
    $this->db->from('pos_purchase_items pi');
    $this->db->join('pos_products p', 'p.pid = pi.pid', 'left');
    $this->db->where('pi.tid', $id);

    $query = $this->db->get();
    return $query->result_array();
}


    public function purchase_transactions($id)
    {
        $this->db->select('*');
        $this->db->from('pos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function purchase_delete($id)
    {
        $this->db->trans_start();
        $this->db->select('pid,qty');
        $this->db->from('pos_purchase_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        $prevresult = $query->result_array();
        foreach ($prevresult as $prd) {
            $amt = $prd['qty'];
            $this->db->set('qty', "qty-$amt", FALSE);
            $this->db->where('pid', $prd['pid']);
            $this->db->update('pos_products');
        }
        $whr = array('id' => $id);
        if ($this->aauth->get_user()->loc) {
            $whr = array('id' => $id, 'loc' => $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
               $whr = array('id' => $id, 'loc' =>0);
        }
        $this->db->delete('pos_purchase', $whr);
        if ($this->db->affected_rows()) $this->db->delete('pos_purchase_items', array('tid' => $id));
        if ($this->db->trans_complete()) {
            return true;
        } else {
            return false;
        }
    }


    private function _get_datatables_query()
    {
        $this->db->select('pos_purchase.id,pos_purchase.tid,pos_purchase.invoicedate,pos_purchase.invoiceduedate,pos_purchase.total,pos_purchase.status,pos_supplier.name');
        $this->db->from($this->table);
        $this->db->join('pos_supplier', 'pos_purchase.csd=pos_supplier.id', 'left');
            if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('pos_purchase.loc', 0); }
                    if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(pos_purchase.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(pos_purchase.invoicedate) <=', datefordatabase($this->input->post('end_date')));
        }
        $i = 0;
        foreach ($this->column_search as $item) // loop column
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

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
           if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('pos_purchase.loc', 0); }
        return $this->db->count_all_results();
    }

    // Custom query for Purchase entries
    public function get_datatables_entries()
    {
        $this->_get_datatables_query_entries(); // Custom query for entries
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    private function _get_datatables_query_entries()
    {
        $this->db->select('pos_purchase_entries.id,pos_purchase_entries.tid,pos_purchase_entries.invoicedate,pos_purchase_entries.invoiceduedate,pos_purchase_entries.total,pos_purchase_entries.status,pos_supplier.name');
        $this->db->from('pos_purchase_entries');
        $this->db->join('pos_supplier', 'pos_purchase_entries.csd=pos_supplier.id', 'left');
        
        if ($this->aauth->get_user()->loc) {
            if (BDATA) {
                $this->db->group_start();
                $this->db->where('pos_purchase_entries.loc', $this->aauth->get_user()->loc);
                $this->db->or_where('pos_purchase_entries.loc', 0);
                $this->db->group_end();
            } else {
                $this->db->where('pos_purchase_entries.loc', $this->aauth->get_user()->loc);
            }
        } elseif (!BDATA) {
            $this->db->where('pos_purchase_entries.loc', 0);
        }

        // Optional: Date range filter
        if ($this->input->post('start_date') && $this->input->post('end_date')) {
            $this->db->where('DATE(pos_purchase_entries.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(pos_purchase_entries.invoicedate) <=', datefordatabase($this->input->post('end_date')));
        }

        $i = 0;
        foreach ($this->column_search_entries as $item) { // Use column search for entries
            if ($this->input->post('search')['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $this->input->post('search')['value']);
                } else {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }

                if (count($this->column_search_entries) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order_entries[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order_entries)) {
            $order = $this->order_entries;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function count_filtered_entries()
    {
        $this->_get_datatables_query_entries(); // use the same query structure as purchase orders
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_entries()
    {
        $this->db->from('pos_purchase_entries');
        
        // Optional location filter
        if ($this->aauth->get_user()->loc) {
            if (BDATA) {
                $this->db->group_start();
                $this->db->where('pos_purchase_entries.loc', $this->aauth->get_user()->loc);
                $this->db->or_where('pos_purchase_entries.loc', 0);
                $this->db->group_end();
            } else {
                $this->db->where('pos_purchase_entries.loc', $this->aauth->get_user()->loc);
            }
        } elseif (!BDATA) {
            $this->db->where('pos_purchase_entries.loc', 0);
        }

        return $this->db->count_all_results();
    }



    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('pos_terms');
        $this->db->where('type', 4);
        $this->db->or_where('type', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function currencies()
    {

        $this->db->select('*');
        $this->db->from('pos_currencies');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function currency_d($id)
    {
        $this->db->select('*');
        $this->db->from('pos_currencies');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function employee($id)
    {
        $this->db->select('pos_employees.name,pos_employees.sign,pos_users.roleid');
        $this->db->from('pos_employees');
        $this->db->where('pos_employees.id', $id);
        $this->db->join('pos_users', 'pos_employees.id = pos_users.id', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function meta_insert($id, $type, $meta_data)
    {

        $data = array('type' => $type, 'rid' => $id, 'col1' => $meta_data);
        if ($id) {
            return $this->db->insert('pos_metadata', $data);
        } else {
            return 0;
        }
    }

    public function attach($id)
    {
        $this->db->select('pos_metadata.*');
        $this->db->from('pos_metadata');
        $this->db->where('pos_metadata.type', 4);
        $this->db->where('pos_metadata.rid', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function meta_delete($id, $type, $name)
    {
        if (@unlink(FCPATH . 'userfiles/attach/' . $name)) {
            return $this->db->delete('pos_metadata', array('rid' => $id, 'type' => $type, 'col1' => $name));
        }
    }

    /* ===================================================================
     * PURCHASE ENTRY METHODS
     * Uses pos_purchase_entries and pos_purchase_invoice_items tables
     * Separate from purchase orders (pos_purchase and pos_purchase_items)
     * ================================================================= */
    
    public function lastpurchase_entry()
    {
        $this->db->select('tid');
        $this->db->from('pos_purchase_entries');
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row()->tid;
        } else {
            return 1000;
        }
    }

    public function purchase_entry_details($id)
    {
        $this->db->select('pos_purchase_entries.*,pos_purchase_entries.id AS iid,SUM(pos_purchase_entries.shipping + pos_purchase_entries.ship_tax) AS shipping,pos_supplier.*,pos_supplier.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from('pos_purchase_entries');
        $this->db->where('pos_purchase_entries.id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase_entries.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_purchase_entries.loc', 0);
        } elseif (!BDATA) {
            $this->db->where('pos_purchase_entries.loc', 0);
        }
        $this->db->join('pos_supplier', 'pos_purchase_entries.csd = pos_supplier.id', 'left');
        $this->db->join('pos_terms', 'pos_terms.id = pos_purchase_entries.term', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function purchase_entry_products($id)
    {
        $this->db->select('pos_purchase_invoice_items.*');
        $this->db->from('pos_purchase_invoice_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function purchase_entry_transactions($id)
    {
        $this->db->select('*');
        $this->db->from('pos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 2); // Different ext value for purchase entries
        $query = $this->db->get();
        return $query->result_array();
    }

    public function attach_entry($id)
    {
        $this->db->select('pos_metadata.*');
        $this->db->from('pos_metadata');
        $this->db->where('pos_metadata.type', 5); // Different type for purchase entries
        $this->db->where('pos_metadata.rid', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function purchase_entry_delete($id)
    {
        $this->db->trans_start();
        
        // Get items to update stock
        $this->db->select('pid,qty');
        $this->db->from('pos_purchase_invoice_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        $items = $query->result_array();
        
        // Update stock for each item
        foreach ($items as $item) {
            $qty = $item['qty'];
            $this->db->set('qty', "qty-$qty", FALSE);
            $this->db->where('pid', $item['pid']);
            $this->db->update('pos_products');
        }
        
        // Delete entry and items
        $where_entry = array('id' => $id);
        if ($this->aauth->get_user()->loc) {
            $where_entry = array('id' => $id, 'loc' => $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $where_entry = array('id' => $id, 'loc' => 0);
        }
        
        $this->db->delete('pos_purchase_entries', $where_entry);
        if ($this->db->affected_rows()) {
            $this->db->delete('pos_purchase_invoice_items', array('tid' => $id));
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_purchase_entry_by_tid($tid)
    {
        $this->db->select('pos_purchase_entries.*,pos_supplier.name as supplier_name');
        $this->db->from('pos_purchase_entries');
        $this->db->join('pos_supplier', 'pos_purchase_entries.csd = pos_supplier.id', 'left');
        $this->db->where('pos_purchase_entries.tid', $tid);
        
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_purchase_entries.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('pos_purchase_entries.loc', 0);
        }
        
        $query = $this->db->get();
        return $query->row_array();
    }

}
