<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Units extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('units_model', 'units');
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if ($this->aauth->get_user()->roleid < 4) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }


    }

    public function index()
    {

        $head['title'] = "Measurement Units";
        $data['units'] = $this->units->units_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('units/index', $data);
        $this->load->view('fixed/footer');
    }


    public function create()
    {
        if ($this->input->post()) {
            $name = $this->input->post('name', true);
            $code = $this->input->post('code', true);

            $this->units->create($name, $code);
        } else {


            $head['title'] = "Add Unit";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('units/create');
            $this->load->view('fixed/footer');
        }
    }

    public function edit()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $name = $this->input->post('name', true);
            $code = $this->input->post('code', true);
            $this->units->edit($id, $name, $code);
        } else {


            $head['title'] = "Edit Unit";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data = $this->units->view($this->input->get('id'));
            $this->load->view('fixed/header', $head);
            $this->load->view('units/edit', $data);
            $this->load->view('fixed/footer');
        }
    }


    public function delete_i()
    {
        $id = $this->input->post('deleteid');
        if ($id) {

            $this->db->delete('pos_units', array('id' => $id));


            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
        }
    }

    //Options (formerly variations)
    public function options()
    {
        $head['title'] = "Product Options";
        $data['units'] = $this->units->options_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('units/options', $data);
        $this->load->view('fixed/footer');
    }

    public function create_option()
    {
        if ($this->input->post()) {
            $name = $this->input->post('name', true);
            $this->units->create_va($name, 1);
        } else {
            $head['title'] = "Add Product Option";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('units/create_option');
            $this->load->view('fixed/footer');
        }
    }

    public function edit_option()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $name = $this->input->post('name', true);
            $this->units->edit_va($id, $name);
        } else {
            $head['title'] = "Edit Product Option";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data = $this->units->view($this->input->get('id'));
            $this->load->view('fixed/header', $head);
            $this->load->view('units/edit_option', $data);
            $this->load->view('fixed/footer');
        }
    }

    //variations (renamed from old variations)
    public function variations()
    {
        $head['title'] = "Product Options";
        $data['units'] = $this->units->options_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('units/variations', $data);
        $this->load->view('fixed/footer');
    }

    public function create_va()
    {
        if ($this->input->post()) {
            $name = $this->input->post('name', true);


            $this->units->create_va($name, 1);
        } else {


            $head['title'] = "Add variation";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('units/create_va');
            $this->load->view('fixed/footer');
        }
    }

    public function edit_va()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $name = $this->input->post('name', true);

            $this->units->edit_va($id, $name);
        } else {


            $head['title'] = "Edit variation";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data = $this->units->view($this->input->get('id'));
            $this->load->view('fixed/header', $head);
            $this->load->view('units/edit_va', $data);
            $this->load->view('fixed/footer');
        }
    }


    public function delete_va_i()
    {
        $id = $this->input->post('deleteid');
        if ($id) {

            $this->db->delete('pos_units', array('id' => $id));


            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
        }
    }

    //Variation Options (formerly variables)
    public function variation_options()
    {
        $head['title'] = "Variation Options";
        $data['units'] = $this->units->variables_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('units/variation_options', $data);
        $this->load->view('fixed/footer');
    }

    public function create_variation_option()
    {
        if ($this->input->post()) {
            $name = $this->input->post('name', true);
            $option_id = $this->input->post('option_id');
            $level_type = $this->input->post('level_type', true) ?: 2; // Default to level 2
            $this->units->create_vb($name, $option_id, $level_type);
        } else {
            $head['title'] = "Add Variation Option";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data['options'] = $this->units->options_list();
            $data['variation_options'] = $this->units->variables_list(); // For level 3
            $this->load->view('fixed/header', $head);
            $this->load->view('units/create_variation_option', $data);
            $this->load->view('fixed/footer');
        }
    }

    public function edit_variation_option()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $name = $this->input->post('name', true);
            $option_id = $this->input->post('option_id');
            $level_type = $this->input->post('level_type', true) ?: 2;
            $this->units->edit_vb($id, $name, $option_id, $level_type);
        } else {
            $head['title'] = "Edit Variation Option";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data = $this->units->view($this->input->get('id'));
            $data['options'] = $this->units->options_list();
            $data['variation_options'] = $this->units->variables_list();
            $this->load->view('fixed/header', $head);
            $this->load->view('units/edit_variation_option', $data);
            $this->load->view('fixed/footer');
        }
    }

    // AJAX method to get variation options for a specific option
    public function get_variation_options_ajax()
    {
        $option_id = $this->input->post('option_id');
        $variation_options = $this->units->variation_options_by_option($option_id);
        echo json_encode($variation_options);
    }

    // AJAX method to get variations for a specific variation option
    public function get_variations_ajax()
    {
        $variation_option_id = $this->input->post('variation_option_id');
        $variations = $this->units->variations_by_variation_option($variation_option_id);
        echo json_encode($variations);
    }

    // New method: Create Product Variation (Level 3)
    public function create_product_variation()
    {
        if ($this->input->post()) {
            try {
                $variant_name = $this->input->post('variant_name', true);
                $option_ids = $this->input->post('option_id'); // Array of option IDs
                $option_value_ids = $this->input->post('option_value_id'); // Array of option value IDs
                $level_type = 3; // Product Variation level
                
                // Debug log
                log_message('debug', 'Variation data - Name: ' . $variant_name . ', Options: ' . print_r($option_ids, true) . ', Values: ' . print_r($option_value_ids, true));
                
                // Validate input
                if (empty($variant_name)) {
                    echo json_encode(array('status' => 'Error', 'message' => 'Variation name is required'));
                    return;
                }
                
                if (empty($option_ids) || !is_array($option_ids)) {
                    echo json_encode(array('status' => 'Error', 'message' => 'Please select at least one option'));
                    return;
                }
                
                if (empty($option_value_ids) || !is_array($option_value_ids)) {
                    echo json_encode(array('status' => 'Error', 'message' => 'Please select option values'));
                    return;
                }
                
                // Validate arrays have same length and no empty values
                $option_ids = array_filter($option_ids, function($val) { return !empty($val); });
                $option_value_ids = array_filter($option_value_ids, function($val) { return !empty($val); });
                
                if (count($option_ids) !== count($option_value_ids)) {
                    echo json_encode(array('status' => 'Error', 'message' => 'Mismatch between options and values count'));
                    return;
                }
                
                if (count($option_ids) == 0) {
                    echo json_encode(array('status' => 'Error', 'message' => 'Please select valid option-value combinations'));
                    return;
                }
                
                // Check for duplicate option types in the same variation
                if (count($option_ids) !== count(array_unique($option_ids))) {
                    echo json_encode(array('status' => 'Error', 'message' => 'Cannot use the same option type multiple times in one variation'));
                    return;
                }
                
                // Create the multi-attribute variation
                $this->units->create_multi_attribute_variation($variant_name, $option_ids, $option_value_ids, $level_type);
                
            } catch (Exception $e) {
                log_message('error', 'Variation creation error: ' . $e->getMessage());
                echo json_encode(array('status' => 'Error', 'message' => 'System error: ' . $e->getMessage()));
            }
        } else {
            $head['title'] = "Add Product Variation";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data['options'] = $this->units->options_list();
            $this->load->view('fixed/header', $head);
            $this->load->view('units/create_product_variation', $data);
            $this->load->view('fixed/footer');
        }
    }

    // Display all product variations 
    public function product_variations()
    {
        $head['title'] = "Product Variations";
        $data['units'] = $this->units->get_complete_variations();
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('units/product_variations', $data);
        $this->load->view('fixed/footer');
    }

    // Delete variation (both single and multi-attribute)
    public function delete_variation()
    {
        $variation_id = $this->input->post('variation_id');
        $type = $this->input->post('type', true);
        
        if (empty($variation_id)) {
            echo json_encode(array('status' => 'Error', 'message' => 'Variation ID is required'));
            return;
        }
        
        $this->units->delete_variation($variation_id, $type);
    }

    //variables (keep for backward compatibility)
    public function variables()
    {
        $head['title'] = "Variation Options";
        $data['units'] = $this->units->variables_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('units/variation_options', $data);
        $this->load->view('fixed/footer');
    }

    public function create_vb()
    {
        if ($this->input->post()) {
            $name = $this->input->post('name', true);
            $option_id = $this->input->post('pvars'); // parent option ID
            $level_type = 2; // Variation Option level
            $this->units->create_vb($name, $option_id, $level_type);
        } else {
            $head['title'] = "Add variation option";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data['variations'] = $this->units->options_list(); // Get options, not old variations
            $this->load->view('fixed/header', $head);
            $this->load->view('units/create_vb', $data);
            $this->load->view('fixed/footer');
        }
    }

    public function edit_vb()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $name = $this->input->post('name', true);
            $option_id = $this->input->post('var_id');
            $level_type = 2; // Variation Option level
            $this->units->edit_vb($id, $name, $option_id, $level_type);
        } else {
            $head['title'] = "Edit variation option";
            $head['usernm'] = $this->aauth->get_user()->username;
            $data = $this->units->view($this->input->get('id'));
            $data['variations'] = $this->units->options_list();
            $this->load->view('fixed/header', $head);
            $this->load->view('units/edit_vb', $data);
            $this->load->view('fixed/footer');
        }
    }


    public function delete_vb_i()
    {
        $id = $this->input->post('deleteid');
        if ($id) {

            $this->db->delete('pos_units', array('id' => $id));


            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
        }
    }


}
