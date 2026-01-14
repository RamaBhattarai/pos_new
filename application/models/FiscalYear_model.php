<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FiscalYear_model extends CI_Model {

    private $table = 'fiscal_years';

    public function get_all() {
        return $this->db->order_by('start_date', 'DESC')->get($this->table)->result();
    }

    public function get($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function insert($data) {
        if(isset($data['is_current']) && $data['is_current'] == 1){
            $this->db->update($this->table, ['is_current' => 0]); // deactivate others
        }
        $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        if(isset($data['is_current']) && $data['is_current'] == 1){
            $this->db->update($this->table, ['is_current' => 0]);
        }
        $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('id', $id)->delete($this->table);
    }
}
