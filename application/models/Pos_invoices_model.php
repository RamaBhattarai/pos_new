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

class Pos_invoices_model extends CI_Model
{
    var $table = 'pos_invoices';
    var $column_order = array(null, 'pos_invoices.tid', 'pos_customers.name', 'pos_invoices.invoicedate', 'pos_invoices.total', 'pos_invoices.status', null);
    var $column_search = array('pos_invoices.tid', 'pos_customers.name', 'pos_invoices.invoicedate', 'pos_invoices.total','pos_invoices.status');
    var $order = array('pos_invoices.tid' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }

    public function lastinvoice()
    {
        $this->db->select('tid');
        $this->db->from($this->table);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $this->db->where('i_class', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->tid;
        } else {
            return 1000;
        }
    }


    public function invoice_details($id, $eid = '',$loc=null)
    {

        $this->db->select('pos_invoices.*, SUM(pos_invoices.shipping + pos_invoices.ship_tax) AS shipping,pos_customers.*,pos_invoices.loc as loc,pos_invoices.id AS iid,pos_customers.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('pos_invoices.id', $id);
        if ($eid) {
            $this->db->where('pos_invoices.eid', $eid);
        }
        if (@$this->aauth->get_user()->loc) {
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA and !$loc) { $this->db->where('pos_invoices.loc', 0); }
        if($loc){ $this->db->where('pos_invoices.loc', $loc); }
        $this->db->join('pos_customers', 'pos_invoices.csd = pos_customers.id', 'left');
        $this->db->join('pos_terms', 'pos_terms.id = pos_invoices.term', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

    public function invoice_products($id)
    {

        $this->db->select('*');
        $this->db->from('pos_invoice_items');
        $this->db->where('tid', $id);
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

    public function invoice_transactions($id)
    {

        $this->db->select('*');
        $this->db->from('pos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 0);
        $query = $this->db->get();
        return $query->result_array();

    }


            public function items_with_product($id)
    {

        $this->db->select('pos_invoice_items.*,pos_products.qty AS alert');
        $this->db->from('pos_invoice_items');
        $this->db->where('tid', $id);
        $this->db->join('pos_products', 'pos_products.pid = pos_invoice_items.pid', 'left');
        $query = $this->db->get();
        return $query->result_array();

    }


    public function invoice_delete($id, $eid = '')
    {

        $this->db->trans_start();

        $this->db->select('status');
        $this->db->from('pos_invoices');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result = $query->row_array();

          if ($this->aauth->get_user()->loc) {
            if ($eid) {

                $res = $this->db->delete('pos_invoices', array('id' => $id, 'eid' => $eid, 'loc' => $this->aauth->get_user()->loc));


            } else {
                $res = $this->db->delete('pos_invoices', array('id' => $id, 'loc' => $this->aauth->get_user()->loc));
            }
        }

        else {
            if (BDATA) {
                if ($eid) {

                    $res = $this->db->delete('pos_invoices', array('id' => $id, 'eid' => $eid));


                } else {
                    $res = $this->db->delete('pos_invoices', array('id' => $id));
                }
            } else {


                if ($eid) {

                    $res = $this->db->delete('pos_invoices', array('id' => $id, 'eid' => $eid, 'loc' => 0));


                } else {
                    $res = $this->db->delete('pos_invoices', array('id' => $id, 'loc' => 0));
                }
            }
        }
        $affect = $this->db->affected_rows();
        if ($res) {
            if ($result['status'] != 'canceled') {
                $this->db->select('pid,qty');
                $this->db->from('pos_invoice_items');
                $this->db->where('tid', $id);
                $query = $this->db->get();
                $prevresult = $query->result_array();
                foreach ($prevresult as $prd) {
                    $amt = $prd['qty'];
                    $this->db->set('qty', "qty+$amt", FALSE);
                    $this->db->where('pid', $prd['pid']);
                    $this->db->update('pos_products');
                }
            }
            if ($affect) $this->db->delete('pos_invoice_items', array('tid' => $id));
            $data = array('type' => 9, 'rid' => $id);
            $this->db->delete('pos_metadata', $data);
            if ($this->db->trans_complete()) {
                return true;
            } else {
                return false;
            }
        }
    }


    private function _get_datatables_query($opt = '')
    {
        $this->db->select('pos_invoices.id,pos_invoices.tid,pos_invoices.invoicedate,pos_invoices.invoiceduedate,pos_invoices.total,pos_invoices.status,pos_customers.name');
        $this->db->from($this->table);
        $this->db->where('pos_invoices.i_class', 1);
        if ($opt) {
            $this->db->where('pos_invoices.eid', $opt);
        }
        if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(pos_invoices.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(pos_invoices.invoicedate) <=', datefordatabase($this->input->post('end_date')));
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
        }
          elseif(!BDATA) { $this->db->where('pos_invoices.loc', 0); }
        $this->db->join('pos_customers', 'pos_invoices.csd=pos_customers.id', 'left');

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

    function get_datatables($opt = '')
    {
        $this->_get_datatables_query($opt);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();
        $this->db->where('pos_invoices.i_class', 1);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
        }
          elseif(!BDATA) { $this->db->where('pos_invoices.loc', 0); }
        return $query->result();
    }

    function count_filtered($opt = '')
    {
        $this->_get_datatables_query($opt);
        if ($opt) {
            $this->db->where('eid', $opt);

        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('pos_invoices.loc', 0); }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($opt = '')
    {
        $this->db->select('pos_invoices.id');
        $this->db->from($this->table);
        $this->db->where('pos_invoices.i_class', 1);
        if ($opt) {
            $this->db->where('pos_invoices.eid', $opt);
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('pos_invoices.loc', 0); }
        return $this->db->count_all_results();
    }


    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('pos_terms');
        $this->db->where('type', 1);
        $this->db->or_where('type', 0);
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
        $this->db->where('pos_metadata.type', 1);
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

    public function gateway_list($enable = '')
    {

        $this->db->from('pos_gateways');
        if ($enable == 'Yes') {
            $this->db->where('enable', 'Yes');
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function drafts()
    {


        $this->db->select('pos_draft.id,pos_draft.tid,pos_draft.invoicedate');
        $this->db->from('pos_draft');
       $this->db->where('pos_draft.loc', $this->aauth->get_user()->loc);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(12);
        $query = $this->db->get();
        return $query->result_array();

    }

    public function draft_products($id)
    {

        $this->db->select('*');
        $this->db->from('pos_draft_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();

    }

    public function draft_details($id, $eid = '')
    {

        $this->db->select('pos_draft.*,SUM(pos_draft.shipping + pos_draft.ship_tax) AS shipping,pos_customers.*,pos_customers.id AS cid,pos_draft.id AS iid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from('pos_draft');
        $this->db->where('pos_draft.id', $id);
        if ($eid) {
            $this->db->where('pos_draft.eid', $eid);
        }
        $this->db->join('pos_customers', 'pos_draft.csd = pos_customers.id', 'left');
        $this->db->join('pos_terms', 'pos_terms.id = pos_draft.term', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

        public function accountslist()
    {
        $this->db->select('*');
        $this->db->from('pos_accounts');

        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
           if(BDATA) $this->db->or_where('loc', 0);
        }else{
             if(!BDATA) $this->db->where('loc', 0);
        }

        $query = $this->db->get();
        return $query->result_array();
    }
}
