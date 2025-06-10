<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Message_model extends CI_Model
{


    public function employee_details($id)
    {

        $this->db->select('pos_employees.*');
        $this->db->from('pos_employees');
        $this->db->where('pos_pms.id', $id);
        $this->db->join('pos_pms', 'pos_employees.id = pos_pms.sender_id', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }


}
