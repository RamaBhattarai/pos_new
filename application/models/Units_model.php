<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Units_model extends CI_Model
{


    public function units_list()
    {
        $query = $this->db->query("SELECT * FROM pos_units WHERE type=0 ORDER BY id DESC");
        return $query->result_array();
    }


    public function view($id)
    {

        $this->db->from('pos_units');
        $this->db->where('id', $id);

        $query = $this->db->get();
        $result = $query->row_array();
        return $result;


    }

    public function create($name, $code)
    {
        $data = array(
            'name' => $name,
            'code' => $code
        );

        if ($this->db->insert('pos_units', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit($id, $name, $code)
    {
        $data = array(
            'name' => $name,
            'code' => $code
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('pos_units')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function variations_list()
    {
        $query = $this->db->query("SELECT * FROM pos_units WHERE level_type=1 ORDER BY id DESC");
        return $query->result_array();
    }
    
    // Get Options (formerly Variations)
    public function options_list()
    {
        $query = $this->db->query("SELECT * FROM pos_units WHERE level_type=1 ORDER BY id DESC");
        return $query->result_array();
    }

    public function create_va($name, $type = 1)
    {
        $data = array(
            'name' => $name,
            'type' => $type,
            'level_type' => 1  // Options level
        );

        if ($this->db->insert('pos_units', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit_va($id, $name)
    {
        $data = array(
            'name' => $name
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('pos_units')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function variables_list()
    {
        // Get Variation Options (formerly Variables) with their parent Options
        $this->db->select('u.id,u.name,u2.name AS option_name');
        $this->db->join('pos_units u2', 'u.parent_id = u2.id', 'left');
        $this->db->where('u.level_type', 2);
        $this->db->order_by('u.name', 'asc');
        $query = $this->db->get('pos_units u');
        return $query->result_array();
    }
    
    // Get Variation Options for a specific Option
    public function variation_options_by_option($option_id)
    {
        $this->db->select('*');
        $this->db->where('parent_id', $option_id);
        $this->db->where('level_type', 2);
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('pos_units');
        return $query->result_array();
    }
    
    // Get Variations for a specific Variation Option
    public function variations_by_variation_option($variation_option_id)
    {
        $this->db->select('*');
        $this->db->where('level_type', 3);
        $this->db->where('parent_id', 0); // Multi-attribute variations
        $this->db->order_by('name', 'asc');
        $query = $this->db->get('pos_units');
        return $query->result_array();
    }
    
    // Get complete hierarchy for product variations
    public function get_complete_variations($option_id = null)
    {
        // Get traditional single-attribute variations
        $this->db->select('u1.id as option_id, u1.name as option_name, 
                          u2.id as variation_option_id, u2.name as variation_option_name,
                          u3.id as variation_id, u3.name as variation_name, "single" as type');
        $this->db->from('pos_units u1');
        $this->db->join('pos_units u2', 'u1.id = u2.parent_id AND u2.level_type = 2', 'left');
        $this->db->join('pos_units u3', 'u2.id = u3.parent_id AND u3.level_type = 3', 'left');
        $this->db->where('u1.level_type', 1);
        
        if ($option_id) {
            $this->db->where('u1.id', $option_id);
        }
        
        $this->db->order_by('u1.name, u2.name, u3.name', 'asc');
        $single_variations = $this->db->get()->result_array();

        // Get multi-attribute variations
        $multi_variations = array();
        if ($this->db->table_exists('pos_variation_attributes')) {
            $this->db->select('v.id as variation_id, v.name as variation_name, "multi" as type');
            $this->db->from('pos_units v');
            $this->db->where('v.level_type', 3);
            $this->db->where('v.parent_id', 0); // Multi-attribute variations have parent_id = 0
            $query = $this->db->get();
            
            foreach ($query->result_array() as $variation) {
                // Get attributes for this variation
                $attributes = $this->get_variation_attributes($variation['variation_id']);
                
                $attribute_display = array();
                foreach ($attributes as $attr) {
                    $attribute_display[] = $attr['option_name'] . ': ' . $attr['option_value_name'];
                }
                
                $multi_variations[] = array(
                    'option_id' => 0,
                    'option_name' => 'Multi-Attribute',
                    'variation_option_id' => 0,
                    'variation_option_name' => implode(' + ', $attribute_display),
                    'variation_id' => $variation['variation_id'],
                    'variation_name' => $variation['variation_name'],
                    'type' => 'multi'
                );
            }
        }

        // Combine both types
        return array_merge($single_variations, $multi_variations);
    }

    public function create_vb($name, $option_id, $level_type = 2)
    {
        $data = array(
            'name' => $name,
            'type' => 2,
            'level_type' => $level_type,  // 2 = Variation Option, 3 = Variation
            'parent_id' => $option_id
        );

        if ($this->db->insert('pos_units', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }
    }

    public function create_multi_attribute_variation($name, $option_ids, $option_value_ids, $level_type = 3)
    {
        // Start transaction
        $this->db->trans_start();
        
        try {
            // Debug logging
            log_message('debug', 'Creating variation: ' . $name);
            log_message('debug', 'Option IDs: ' . print_r($option_ids, true));
            log_message('debug', 'Option Value IDs: ' . print_r($option_value_ids, true));
            
            // First, create the main variation record
            $variation_data = array(
                'name' => $name,
                'type' => 2,
                'level_type' => $level_type,  // 3 = Multi-attribute variation
                'parent_id' => 0 // No single parent for multi-attribute variations
            );

            if (!$this->db->insert('pos_units', $variation_data)) {
                throw new Exception('Failed to insert variation record');
            }
            
            $variation_id = $this->db->insert_id();
            if (!$variation_id) {
                throw new Exception('Failed to get variation ID');
            }

            // Create attributes string for storage in name field for compatibility
            $attribute_names = array();
            $formatted_parts = array();
            for ($i = 0; $i < count($option_ids); $i++) {
                // Get option name
                $this->db->select('name');
                $this->db->where('id', $option_ids[$i]);
                $this->db->where('level_type', 1); // Ensure it's an option
                $option_query = $this->db->get('pos_units');
                if ($option_query->num_rows() == 0) {
                    throw new Exception('Invalid option ID: ' . $option_ids[$i]);
                }
                $option_name = $option_query->row()->name;
                
                // Get option value name  
                $this->db->select('name');
                $this->db->where('id', $option_value_ids[$i]);
                $this->db->where('level_type', 2); // Ensure it's an option value
                $value_query = $this->db->get('pos_units');
                if ($value_query->num_rows() == 0) {
                    throw new Exception('Invalid option value ID: ' . $option_value_ids[$i]);
                }
                $value_name = $value_query->row()->name;
                
                $attribute_names[] = $value_name;
                $formatted_parts[] = $option_name . ' ' . $value_name;
            }

            // Update the main variation with formatted name
            $formatted_name = implode(' & ', $formatted_parts);
            $this->db->where('id', $variation_id);
            if (!$this->db->update('pos_units', array('name' => $formatted_name))) {
                throw new Exception('Failed to update variation name');
            }

            // Check if variation attributes table exists, if not create it
            if (!$this->db->table_exists('pos_variation_attributes')) {
                $this->create_variation_attributes_table();
            }

            // Insert individual attributes into the junction table
            for ($i = 0; $i < count($option_ids); $i++) {
                $attribute_data = array(
                    'variation_id' => $variation_id,
                    'option_id' => $option_ids[$i],
                    'option_value_id' => $option_value_ids[$i]
                );
                
                if (!$this->db->insert('pos_variation_attributes', $attribute_data)) {
                    throw new Exception('Failed to insert attribute data for option: ' . $option_ids[$i]);
                }
            }

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            log_message('debug', 'Variation created successfully with ID: ' . $variation_id);
            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('ADDED')));
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Variation creation error: ' . $e->getMessage());
            echo json_encode(array('status' => 'Error', 'message' => 'Database error: ' . $e->getMessage()));
        }
    }

    private function create_variation_attributes_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `pos_variation_attributes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `variation_id` int(11) NOT NULL,
            `option_id` int(11) NOT NULL,
            `option_value_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `variation_id` (`variation_id`),
            KEY `option_id` (`option_id`),
            KEY `option_value_id` (`option_value_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
        
        $this->db->query($sql);
    }

    public function get_variation_attributes($variation_id)
    {
        $this->db->select('va.*, o.name as option_name, ov.name as option_value_name');
        $this->db->from('pos_variation_attributes va');
        $this->db->join('pos_units o', 'va.option_id = o.id', 'left');
        $this->db->join('pos_units ov', 'va.option_value_id = ov.id', 'left');
        $this->db->where('va.variation_id', $variation_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete_variation($variation_id, $type = 'single')
    {
        $this->db->trans_start();
        
        try {
            if ($type === 'multi' && $this->db->table_exists('pos_variation_attributes')) {
                // Delete multi-attribute variation and its attributes
                $this->db->where('variation_id', $variation_id);
                $this->db->delete('pos_variation_attributes');
            }
            
            // Delete the main variation record
            $this->db->where('id', $variation_id);
            $this->db->delete('pos_units');
            
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode(array('status' => 'Error', 'message' => 'Failed to delete variation'));
            } else {
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
            }
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => 'Error', 'message' => 'Database error: ' . $e->getMessage()));
        }
    }

    public function edit_vb($id, $name, $option_id, $level_type = 2)
    {
        $data = array(
            'name' => $name,
            'parent_id' => $option_id,
            'level_type' => $level_type
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('pos_units')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


}
