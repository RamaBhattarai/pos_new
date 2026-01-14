<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FiscalYear extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('FiscalYear_model');
        $this->load->helper('url');
    }

    public function index() {
        $data['fiscal_years'] = $this->FiscalYear_model->get_all();
        $this->load->view('fiscal_year/index', $data);
    }

    public function create() {
        $this->load->view('fiscal_year/create');
    }

    public function store() {
        $this->FiscalYear_model->insert($this->input->post());
        redirect('fiscal_year');
    }

    public function edit($id) {
        $data['fiscal_year'] = $this->FiscalYear_model->get($id);
        $this->load->view('fiscal_year/edit', $data);
    }

    public function update($id) {
        $this->FiscalYear_model->update($id, $this->input->post());
        redirect('fiscalyear');
    }

    public function delete($id) {
        $this->FiscalYear_model->delete($id);
        redirect('fiscalyear');
    }
}
