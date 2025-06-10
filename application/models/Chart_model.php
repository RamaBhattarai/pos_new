<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_model extends CI_Model
{

    public function productcat($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }
        $this->db->select_sum('pos_invoice_items.qty');
        $this->db->select_sum('pos_invoice_items.subtotal');
        $this->db->select('pos_invoice_items.pid');
        $this->db->select('pos_product_cat.title');
        $this->db->from('pos_invoice_items');
        $this->db->group_by('pos_product_cat.id');
        $this->db->join('pos_invoices', 'pos_invoices.id = pos_invoice_items.tid', 'left');
        $this->db->join('pos_products', 'pos_products.pid = pos_invoice_items.pid', 'left');
        $this->db->join('pos_product_cat', 'pos_product_cat.id = pos_products.pcat', 'left');
        $month = date('Y-m');
        $today = date('Y-m-d');
        $this->db->where('DATE(pos_invoices.invoicedate) >=', $day1);
        $this->db->where('DATE(pos_invoices.invoicedate) <=', $day2);
                    if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_invoices.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('pos_invoices.loc', 0);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function trendingproducts($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }

        $this->db->select_sum('pos_invoice_items.qty');
        $this->db->select('pos_products.product_name');
        $this->db->from('pos_invoice_items');
        $this->db->group_by('pos_invoice_items.pid');
        $this->db->join('pos_invoices', 'pos_invoices.id = pos_invoice_items.tid', 'left');
        $this->db->join('pos_products', 'pos_products.pid = pos_invoice_items.pid', 'left');

        $this->db->where('DATE(pos_invoices.invoicedate) >=', $day1);
        $this->db->where('DATE(pos_invoices.invoicedate) <=', $day2);
                            if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_invoices.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('pos_invoices.loc', 0);
        }
        $this->db->order_by('pos_invoice_items.qty', 'DESC');
        $this->db->limit(100);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function profitchart($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }

        $this->db->select_sum('pos_metadata.col1');
        $this->db->select('pos_metadata.d_date');
        $this->db->from('pos_metadata');
        $this->db->group_by('pos_metadata.d_date');
        $month = date('Y-m');
        $today = date('Y-m-d');
        $this->db->where('DATE(pos_metadata.d_date) >=', $day1);
        $this->db->where('DATE(pos_metadata.d_date) <=', $day2);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function customerchart($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }
        $this->db->select_sum('pos_invoices.total');
        $this->db->select('pos_customers.name');
        $this->db->from('pos_invoices');
        $this->db->group_by('pos_invoices.csd');
        $this->db->join('pos_customers', 'pos_customers.id = pos_invoices.csd', 'left');
        $month = date('Y-m');
        $today = date('Y-m-d');
        $this->db->where('DATE(pos_invoices.invoicedate) >=', $day1);
        $this->db->where('DATE(pos_invoices.invoicedate) <=', $day2);
                            if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('pos_invoices.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('pos_invoices.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('pos_invoices.loc', 0);
        }
        $this->db->order_by('pos_invoices.total', 'DESC');
        $this->db->limit(100);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }


    public function incomechart($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }
        $this->db->select_sum('credit');
        $this->db->select('date');
        $this->db->from('pos_transactions');
        $this->db->group_by('date');
        $month = date('Y-m');
        $today = date('Y-m-d');
        $this->db->where('DATE(date) >=', $day1);
        $this->db->where('DATE(date) <=', $day2);
        $this->db->where('type', 'Income');
                            if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function expenseschart($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }
        $this->db->select_sum('debit');
        $this->db->select('date');
        $this->db->from('pos_transactions');
        $this->db->group_by('date');
        $month = date('Y-m');
        $today = date('Y-m-d');
        $this->db->where('DATE(date) >=', $day1);
        $this->db->where('DATE(date) <=', $day2);
        $this->db->where('type', 'Expense');
                            if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function incexp($type, $c1 = '', $c2 = '')
    {
        switch ($type) {
            case 'week':
                $day1 = date("Y-m-d", strtotime(' - 7 days'));
                $day2 = date('Y-m-d');
                break;
            case 'month':
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
            case 'year':
                $day1 = date("Y-m-d", strtotime(' - 1 years'));
                $day2 = date('Y-m-d');
                break;

            case 'custom':
                $day1 = datefordatabase($c1);
                $day2 = datefordatabase($c2);
                break;

            default :
                $day1 = date("Y-m-d", strtotime(' - 30 days'));
                $day2 = date('Y-m-d');
                break;
        }
        $this->db->select_sum('debit');
        $this->db->select_sum('credit');
        $this->db->select('type');
        $this->db->from('pos_transactions');
        $this->db->group_by('type');
        $month = date('Y-m');
        $today = date('Y-m-d');
        $this->db->where('DATE(date) >=', $day1);
        $this->db->where('DATE(date) <=', $day2);
                    if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }


}
