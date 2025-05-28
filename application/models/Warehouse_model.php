<?php
class Warehouse_model extends CI_Model {

    public function getAll() {
        return $this->db->get('pos_warehouse')->result_array();
    }

}
