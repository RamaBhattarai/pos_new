<?php

class Custom
{
    function __construct()
    {
        $this->PI = &get_instance();
    }

    function add_fields($id = 0, $apply = 0)
    {
        $this->PI->db->where('f_module', $id);
        if($apply) $this->PI->db->where('f_view', $apply);
        $this->PI->db->order_by('id', 'DESC');
        $query = $this->PI->db->get('pos_custom_fields');
        $result = $query->result_array();
        return $result;
    }


    function view_fields($id = 0, $apply = 0)
    {

        $this->PI->db->where('f_module', $id);
        if($apply)  $this->PI->db->where('f_view', $apply);
        $this->PI->db->order_by('id', 'DESC');
        $query = $this->PI->db->get('pos_custom_fields');
        $result = $query->result_array();
        return $result;
    }

    function save_fields_data($rid = 0, $r_type = 0)
    {
        $custom = $this->PI->input->post('custom', true);
         if(is_array($custom)) {
             $datalist = array();
             $dindex = 0;
             foreach ($custom as $key => $value) {
                 if ($value) {
                     $data = array(
                         'field_id' => $key,
                         'rid' => $rid,
                         'module' => $r_type,
                         'data' => $value
                     );
                     $datalist[$dindex] = $data;
                     $dindex++;
                 }
             }
            if($dindex) $this->PI->db->insert_batch('pos_custom_data', $datalist);
         }
    }

        function edit_save_fields_data($rid = 0, $r_type = 0)
    {
        $custom = $this->PI->input->post('custom', true);
         if(is_array($custom)) {
             $datalist = array();
             $dindex = 0;
             $this->PI->db->delete('pos_custom_data', array('rid' => $rid, 'module' => $r_type));
             foreach ($custom as $key => $value) {
                 if ($value) {
                     $data = array(
                         'field_id' => $key,
                         'rid' => $rid,
                         'module' => $r_type,
                         'data' => $value
                     );
                     $datalist[$dindex] = $data;
                     $dindex++;
                 }
             }

             if($dindex) $this->PI->db->insert_batch('pos_custom_data', $datalist);
         }
    }

    function view_fields_data($rid = 0,$r_type = 0,$view = 0)
    {


        $this->PI->db->select("pos_custom_data.*,pos_custom_fields.name ");
        $this->PI->db->from('pos_custom_data');
        $this->PI->db->join('pos_custom_fields', 'pos_custom_data.field_id = pos_custom_fields.id', 'left');


            $this->PI->db->where('pos_custom_data.rid=', $rid);
             $this->PI->db->where('pos_custom_data.module=', $r_type);
              $this->PI->db->where('pos_custom_data.module=', $r_type);
              if($view)  $this->PI->db->where('pos_custom_fields.f_view=', $view);
          $query = $this->PI->db->get();
        $result = $query->result_array();
        return $result;

    }

        function view_edit_fields($id = 0, $apply = 0)
    {


          $query = $this->PI->db->query("SELECT `pos_custom_data`.`data`, `pos_custom_fields`.* FROM `pos_custom_fields` LEFT OUTER JOIN `pos_custom_data` ON `pos_custom_fields`.`id`=`pos_custom_data`.`field_id` AND (`pos_custom_data`.`rid` = '$id' OR `pos_custom_data`.`rid` IS NULL) WHERE  `pos_custom_fields`.`f_module` = $apply;");
        return $query->result_array();
    }

    function del_fields($rid,$r_type)
    {
        $this->PI->db->delete('pos_custom_data', array('rid' => $rid,'module'=>$r_type));

    }

       function api_config($id=0)
    {
        $this->PI->db->where('id', $id);
        $query = $this->PI->db->get('univarsal_api');
        $row1 = $query->row_array();
        return $row1;
    }


}