<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Quote_model extends CI_Model
{
    var $table = 'pos_quotes';
    var $column_order = array(null, 'pos_quotes.tid', 'pos_customers.name', 'pos_quotes.invoicedate', 'pos_quotes.total', 'pos_quotes.status', null);
    var $column_search = array('pos_quotes.tid', 'pos_customers.name', 'pos_quotes.invoicedate', 'pos_quotes.total','pos_quotes.status',);
    var $order = array('pos_quotes.tid' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }

    public function lastquote()
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

    public function quote_details($id)
    {

        $this->db->select('pos_quotes.*,pos_quotes.id AS iid,SUM(pos_quotes.shipping + pos_quotes.ship_tax) AS shipping,pos_customers.*,pos_quotes.loc as loc,pos_customers.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('pos_quotes.id', $id);
         if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_quotes.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('pos_quotes.loc', 0);
        }
        $this->db->join('pos_customers', 'pos_quotes.csd = pos_customers.id', 'left');
        $this->db->join('pos_terms', 'pos_terms.id = pos_quotes.term', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

    public function quote_products($id)
    {

        $this->db->select('*');
        $this->db->from('pos_quotes_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();

    }


    public function quote_delete($id)
    {
        $this->db->trans_start();
          if ($this->aauth->get_user()->loc) {
                $res = $this->db->delete('pos_quotes', array('id' => $id, 'loc' => $this->aauth->get_user()->loc));
        }
        else {
            if (BDATA) {
                    $res = $this->db->delete('pos_quotes', array('id' => $id));

            } else {
                    $res = $this->db->delete('pos_quotes', array('id' => $id,'loc' => 0));
            }
        }
        if ($this->db->affected_rows()) $this->db->delete('pos_quotes_items', array('tid' => $id));
        if ($this->db->trans_complete()) {
            return true;
        } else {
            return false;
        }
    }


    private function _get_datatables_query($eid)
    {

        $this->db->select('pos_quotes.id,pos_quotes.tid,pos_quotes.invoicedate,pos_quotes.invoiceduedate,pos_quotes.total,pos_quotes.status,pos_customers.name');
        $this->db->from($this->table);
        if ($eid) $this->db->where('pos_quotes.eid', $eid);
                if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_quotes.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('pos_quotes.loc', 0); }
                        if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(pos_quotes.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(pos_quotes.invoicedate) <=', datefordatabase($this->input->post('end_date')));
        }

        $this->db->join('pos_customers', 'pos_quotes.csd=pos_customers.id', 'left');

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

    function get_datatables($eid)
    {
        $this->_get_datatables_query($eid);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_quotes.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('pos_quotes.loc', 0); }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($eid)
    {
        $this->_get_datatables_query($eid);
    if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_quotes.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('pos_quotes.loc', 0); }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($eid)
    {
        $this->db->select('pos_quotes.id');
        $this->db->from($this->table);
         if ($this->aauth->get_user()->loc) {
            $this->db->where('pos_quotes.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('pos_quotes.loc', 0); }
        if ($eid) $this->db->where('pos_quotes.eid', $eid);
        return $this->db->count_all_results();
    }


    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('pos_terms');
        $this->db->where('type', 2);
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

    public function convert($id)
    {

        $invoice = $this->quote_details($id);
        $products = $this->quote_products($id);
        $this->db->trans_start();
        $this->db->select('tid');
        $this->db->from('pos_invoices');
        $this->db->where('i_class', 0);
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $iid = $query->row()->tid + 1;
        } else {
            $iid = 1000;
        }
        $productlist = array();
        $prodindex = 0;
        if($invoice['loc']==$this->aauth->get_user()->loc) {
            $data = array('tid' => $iid, 'invoicedate' => $invoice['invoicedate'], 'invoiceduedate' => $invoice['invoicedate'], 'subtotal' => $invoice['invoicedate'], 'shipping' => $invoice['shipping'], 'discount' => $invoice['discount'], 'tax' => $invoice['tax'], 'total' => $invoice['total'], 'notes' => $invoice['notes'], 'csd' => $invoice['csd'], 'eid' => $invoice['eid'], 'items' => $invoice['items'], 'taxstatus' => $invoice['taxstatus'], 'discstatus' => $invoice['discstatus'], 'format_discount' => $invoice['format_discount'], 'refer' => $invoice['refer'], 'term' => $invoice['term'],'multi' => $invoice['multi'], 'loc' => $invoice['loc']);
            $this->db->insert('pos_invoices', $data);
            $iid = $this->db->insert_id();
            foreach ($products as $row) {
                $amt = $row['qty'];
                $data = array(
                    'tid' => $iid,
                    'pid' => $row['pid'],
                    'product' => $row['product'],
                    'code' => $row['code'],
                    'qty' => $amt,
                    'price' => $row['price'],
                    'tax' => $row['tax'],
                    'discount' => $row['discount'],
                    'subtotal' => $row['subtotal'],
                    'totaltax' => $row['totaltax'],
                    'totaldiscount' => $row['totaldiscount'],
                    'product_des' => $row['product_des'],
                    'unit' => $row['unit']
                );
                $productlist[$prodindex] = $data;
                $prodindex++;
                $this->db->set('qty', "qty-$amt", FALSE);
                $this->db->where('pid', $row['pid']);
                $this->db->update('pos_products');
            }


            $this->db->insert_batch('pos_invoice_items', $productlist);


            //profit calculation
            $t_profit = 0;
            $this->db->select('pos_invoice_items.pid, pos_invoice_items.price, pos_invoice_items.qty, pos_products.fproduct_price');
            $this->db->from('pos_invoice_items');
            $this->db->join('pos_products', 'pos_products.pid = pos_invoice_items.pid', 'left');
            $this->db->where('pos_invoice_items.tid', $iid);
            $query = $this->db->get();
            $pids = $query->result_array();
            foreach ($pids as $profit) {
                $t_cost = $profit['fproduct_price'] * $profit['qty'];
                $s_cost = $profit['price'] * $profit['qty'];
                $t_profit += $s_cost - $t_cost;
            }
            $data = array('type' => 9, 'rid' => $iid, 'col1' => rev_amountExchange_s($t_profit, $invoice['multi'], $this->aauth->get_user()->loc), 'd_date' => $invoice['invoicedate']);

            $this->db->insert('pos_metadata', $data);

            if ($this->db->trans_complete()) {
                $this->db->set('status', 'accepted');
                $this->db->where('id', $id);
                $this->db->update('pos_quotes');
                return true;
            } else {
                return false;
            }
        }else{

                return false;

        }

    }

     public function convert_po($id,$person)
    {

        $invoice = $this->quote_details($id);
        $products = $this->quote_products($id);
        $this->db->trans_start();
        $this->db->select('tid');
        $this->db->from('pos_purchase');
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $iid = $query->row()->tid + 1;
        } else {
            $iid = 1000;
        }
        $productlist = array();
        $prodindex = 0;
        if($invoice['loc']==$this->aauth->get_user()->loc) {
            $data = array('tid' => $iid, 'invoicedate' => $invoice['invoicedate'], 'invoiceduedate' => $invoice['invoicedate'], 'subtotal' => $invoice['invoicedate'], 'shipping' => $invoice['shipping'], 'discount' => $invoice['discount'], 'tax' => $invoice['tax'], 'total' => $invoice['total'], 'notes' => $invoice['notes'], 'csd' => $person, 'eid' => $invoice['eid'], 'items' => $invoice['items'], 'taxstatus' => $invoice['taxstatus'], 'discstatus' => $invoice['discstatus'], 'format_discount' => $invoice['format_discount'], 'refer' => $invoice['refer'], 'term' => $invoice['term'],'multi' => $invoice['multi'], 'loc' => $invoice['loc']);
            $this->db->insert('pos_purchase', $data);
            $iid = $this->db->insert_id();
            foreach ($products as $row) {
                $amt = $row['qty'];
                $data = array(
                    'tid' => $iid,
                    'pid' => $row['pid'],
                    'product' => $row['product'],
                    'code' => $row['code'],
                    'qty' => $amt,
                    'price' => $row['price'],
                    'tax' => $row['tax'],
                    'discount' => $row['discount'],
                    'subtotal' => $row['subtotal'],
                    'totaltax' => $row['totaltax'],
                    'totaldiscount' => $row['totaldiscount'],
                    'product_des' => $row['product_des'],
                    'unit' => $row['unit']
                );
                $productlist[$prodindex] = $data;
                $prodindex++;
                $this->db->set('qty', "qty+$amt", FALSE);
                $this->db->where('pid', $row['pid']);
                $this->db->update('pos_products');
            }


            $this->db->insert_batch('pos_purchase_items', $productlist);




            if ($this->db->trans_complete()) {
                $this->db->set('status', 'accepted');
                $this->db->where('id', $id);
                $this->db->update('pos_quotes');
                return true;
            } else {
                return false;
            }
        }else{

                return false;

        }

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
        $this->db->where('pos_metadata.type', 2);
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
