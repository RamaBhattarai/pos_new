<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Paymentmethods_model extends CI_Model
{
    protected $table = 'payment_methods';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $this->db->select('pm.*, COALESCE(pa.holder, "Not Linked") as account_name, COALESCE(pa.lastbal, 0) as balance');
        $this->db->from($this->table . ' pm');
        $this->db->join('pos_accounts pa', 'pa.id = pm.account_id', 'left');
        $this->db->order_by('pm.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    public function get_by_name($name)
    {
        $this->db->where('name', $name);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    public function add($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_with_balance()
    {
        $this->db->select('pm.*, COALESCE(pa.holder, "Not Linked") as account_name, COALESCE(pa.lastbal, 0) as balance, pa.account_type');
        $this->db->from($this->table . ' pm');
        $this->db->join('pos_accounts pa', 'pa.id = pm.account_id', 'left');
        $this->db->order_by('pm.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
