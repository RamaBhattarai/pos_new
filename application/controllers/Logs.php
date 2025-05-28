<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load the model
        $this->load->model('Settings_model');
    }

    
    public function index()
    {
        // Set the number of logs per page
        $limit = 10;
        $start = $this->uri->segment(3, 0); // Get the starting point for pagination
    
        // Get logs with pagination
        $data['logs'] = $this->Settings_model->logs($limit, $start);
    
        // Load the pagination library and configure it
        $this->load->library('pagination');
        $config['base_url'] = base_url('logs/index');
        $config['total_rows'] = $this->db->count_all('pos_log');
        $config['per_page'] = $limit;
        $config['uri_segment'] = 3;
    
        // Initialize the pagination
        $this->pagination->initialize($config);
    
        // Get the pagination links
        $data['pagination'] = $this->pagination->create_links();
    
        // Load the view and pass the logs and pagination data
        $this->load->view('logs_view', $data);
    }
    

}

