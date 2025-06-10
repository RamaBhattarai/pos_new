<?php


defined('BASEPATH') OR exit('No direct script access allowed');


class Restapi_model extends CI_Model
{
    var $table = 'pos_accounts';

    public function __construct()
    {
        parent::__construct();
    }

    public function keylist()
    {
        $this->db->select('*');
        $this->db->from('pos_restkeys');
        $query = $this->db->get();
        return $query->result_array();
    }


    public function addnew()
    {

        $random = substr(md5(mt_rand()), 0, 24);
        $data = array(
            'user_id' => 0,
            'key' => $random,
            'level' => 0,
            'date_created' => date('Y-m-d')


        );

        if ($this->db->insert('pos_restkeys', $data)) {
            return true;
        } else {
            return false;

        }

    }


}
