<?php
/**
 * Geo POS -  Accounting,  Invoicing  and CRM Application
 * Copyright (c) UltimateKode. All Rights Reserved
 * ***********************************************************************
 *
 *  Email: support@ultimatekode.com
 *  Website: https://www.ultimatekode.com
 *
 *  ************************************************************************
 *  * This software is furnished under a license and may be used and copied
 *  * only  in  accordance  with  the  terms  of such  license and with the
 *  * inclusion of the above copyright notice.
 *  * If you Purchased from Codecanyon, Please read the full License from
 *  * here- http://codecanyon.net/licenses/standard/
 * ***********************************************************************
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Stockreturn_model extends CI_Model
{
    var $table = 'pos_stock_r';
    var $column_order = array(null, 'pos_stock_r.tid', 'name', 'pos_stock_r.invoicedate', 'pos_stock_r.total', 'pos_stock_r.status', null);
    var $column_search = array('pos_stock_r.tid', 'name', 'pos_stock_r.invoicedate', 'pos_stock_r.total','pos_stock_r.status');
    var $order = array('pos_stock_r.tid' => 'desc');

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
          if(BDATA)  $this->db->or_where('loc', 0);
        }  elseif(!BDATA) { $this->db->where('loc', 0); }
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

    public function purchase_details($id)
    {

        $this->db->select('pos_stock_r.i_class');
        $this->db->from($this->table);
        $this->db->where('pos_stock_r.id', $id);
        $query = $this->db->get();
        $out = $query->row_array();

        if ($out['i_class']) {
            $this->db->select('pos_stock_r.*,pos_stock_r.id AS iid,SUM(pos_stock_r.shipping + pos_stock_r.ship_tax) AS shipping,pos_customers.*,pos_stock_r.loc as loc,pos_customers.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
            $this->db->from($this->table);
            $this->db->where('pos_stock_r.id', $id);
            $this->db->join('pos_customers', 'pos_stock_r.csd = pos_customers.id', 'left');
            $this->db->join('pos_terms', 'pos_terms.id = pos_stock_r.term', 'left');
               if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_stock_r.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('pos_stock_r.loc', 0);
        }
            $query = $this->db->get();
            return $query->row_array();
        } else {

            $this->db->select('pos_stock_r.*,pos_stock_r.id AS iid,SUM(pos_stock_r.shipping + pos_stock_r.ship_tax) AS shipping,pos_supplier.*,pos_stock_r.loc as loc,pos_supplier.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
            $this->db->from($this->table);
            $this->db->where('pos_stock_r.id', $id);
            $this->db->join('pos_supplier', 'pos_stock_r.csd = pos_supplier.id', 'left');
            $this->db->join('pos_terms', 'pos_terms.id = pos_stock_r.term', 'left');
              if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_stock_r.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('pos_stock_r.loc', 0);
        }
            $query = $this->db->get();
            return $query->row_array();
        }

    }

    public function purchase_products($id)
    {

        $this->db->select('*');
        $this->db->from('pos_stock_r_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();

    }

    public function purchase_transactions($id)
    {

        $this->db->select('*');
        $this->db->from('pos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 6);
        $query = $this->db->get();
        return $query->result_array();

    }

    public function purchase_delete($id)
    {

        $this->db->trans_start();

        $this->db->select('pid,qty');
        $this->db->from('pos_stock_r_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        $prevresult = $query->result_array();

        $this->db->select('i_class');
        $this->db->from('pos_stock_r');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $stock = $query->row_array();
        if (($stock['i_class'] != 2 && $this->aauth->premission(2)) OR ($stock['i_class'] == 2 && $this->aauth->premission(1))) {
            foreach ($prevresult as $prd) {
                $amt = $prd['qty'];
                $this->db->set('qty', "qty+$amt", FALSE);
                $this->db->where('pid', $prd['pid']);
                $this->db->update('pos_products');
            }
            $whr = array('id' => $id);
            if ($this->aauth->get_user()->loc) {
                $whr = array('id' => $id, 'loc' => $this->aauth->get_user()->loc);
            }
            $this->db->delete('pos_stock_r', $whr);
            if ($this->db->affected_rows()) $this->db->delete('pos_stock_r_items', array('tid' => $id));
            if ($this->db->trans_complete()) {
                return true;
            } else {
                return false;
            }
        }
    }


    private function _get_datatables_query($type = 0)
    {
        if ($type) {

            $this->db->select('pos_stock_r.id,pos_stock_r.tid,pos_stock_r.invoicedate,pos_stock_r.invoiceduedate,pos_stock_r.total,pos_stock_r.status,pos_customers.name');
            $this->db->from($this->table);
            $this->db->where('pos_stock_r.i_class', $type);
            $this->db->join('pos_customers', 'pos_stock_r.csd=pos_customers.id', 'left');
                  if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_stock_r.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('pos_stock_r.loc', 0); }
        } else {
            $this->db->select('pos_stock_r.id,pos_stock_r.tid,pos_stock_r.invoicedate,pos_stock_r.invoiceduedate,pos_stock_r.total,pos_stock_r.status,pos_supplier.name');
            $this->db->from($this->table);
            $this->db->where('pos_stock_r.i_class', $type);
            $this->db->join('pos_supplier', 'pos_stock_r.csd=pos_supplier.id', 'left');
                 if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_stock_r.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('pos_stock_r.loc', 0); }
        }
                    if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(pos_stock_r.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(pos_stock_r.invoicedate) <=', datefordatabase($this->input->post('end_date')));
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

    function get_datatables($type = 0)
    {
        $this->_get_datatables_query($type);
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
            $this->db->where('pos_stock_r.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('pos_stock_r.loc', 0); }
        return $this->db->count_all_results();
    }


    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('pos_terms');
        $query = $this->db->get();
        return $query->result_array();
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


}
