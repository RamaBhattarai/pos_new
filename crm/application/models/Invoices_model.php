<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices_model extends CI_Model
{
    var $table = 'pos_invoices';
    var $column_order = array(null, 'tid', 'name', 'invoicedate', 'total', 'status', null);
    var $column_search = array('tid', 'name', 'invoicedate', 'total');
    var $order = array('tid' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }




        public function invoice_details($id)
    {
        $this->db->select('pos_invoices.*,pos_customers.*,pos_invoices.loc as loc,pos_invoices.id AS iid,pos_customers.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('pos_invoices.id', $id);


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

    public function invoice_transactions($id)
    {

        $this->db->select('*');
        $this->db->from('pos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 0);
        $query = $this->db->get();
        return $query->result_array();

    }


    private function _get_datatables_query()
    {

         $this->db->select('pos_invoices.id,pos_invoices.tid,pos_invoices.invoicedate,pos_invoices.invoiceduedate,pos_invoices.total,pos_invoices.status,pos_invoices.multi,pos_customers.name');
        $this->db->from($this->table);
        $this->db->where('pos_invoices.csd', $this->session->userdata('user_details')[0]->cid);
     //     $this->db->where('pos_invoices.i_class');
        $this->db->join('pos_customers', 'pos_invoices.csd=pos_customers.id', 'left');

        $i = 0;

        foreach ($this->column_search as $item) // loop column
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
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
        $this->db->where('pos_invoices.csd', $this->session->userdata('user_details')[0]->cid);
         // $this->db->where('pos_invoices.i_class', 0);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $this->db->where('pos_invoices.csd', $this->session->userdata('user_details')[0]->cid);
     //     $this->db->where('pos_invoices.i_class', 0);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        $this->db->where('pos_invoices.csd', $this->session->userdata('user_details')[0]->cid);
    //      $this->db->where('pos_invoices.i_class', 0);
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
        $this->db->join('pos_users', 'pos_employees.id =pos_users.id', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }


}
