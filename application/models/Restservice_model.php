<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Restservice_model extends CI_Model
{

    public function customers($id = '')
    {

        $this->db->select('*');
        $this->db->from('pos_customers');
        if ($id != '') {

            $this->db->where('id', $id);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete_customer($id)
    {
        return $this->db->delete('pos_customers', array('id' => $id));
    }

    public function products($id = '')
    {

        $this->db->select('*');
        $this->db->from('pos_products');
        if ($id != '') {

            $this->db->where('id', $id);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function invoice($id)
    {
        $this->db->select('pos_invoices.*,pos_customers.*,pos_invoices.id AS iid,pos_customers.id AS cid,pos_terms.id AS termid,pos_terms.title AS termtit,pos_terms.terms AS terms');
        $this->db->from('pos_invoices');
        $this->db->where('pos_invoices.id', $id);
        $this->db->join('pos_customers', 'pos_invoices.csd = pos_customers.id', 'left');
        $this->db->join('pos_terms', 'pos_terms.id = pos_invoices.term', 'left');
        $query = $this->db->get();
        $invoice = $query->row_array();
        $loc = location($invoice['loc']);
        $this->db->select('pos_invoice_items.*');
        $this->db->from('pos_invoice_items');
        $this->db->where('pos_invoice_items.tid', $id);
        $query = $this->db->get();
        $items = $query->result_array();
        return array(array('invoice' => $invoice, 'company' => $loc, 'items' => $items, 'currency' => currency($invoice['loc'])));
    }


}
