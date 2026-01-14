<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Paymentmethods extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        $this->load->model('Paymentmethods_model', 'paymentmethods');
        $this->load->library('form_validation');
        $this->load->model('accounts_model', 'accounts');
        $this->li_a = 'settings';
    }

    public function index()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Payment Methods';
        $data['payment_methods'] = $this->paymentmethods->get_all();
        $this->load->view('fixed/header', $head);
        $this->load->view('paymentmethods/index', $data);
        $this->load->view('fixed/footer');
    }

    public function add()
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Payment Method Name', 'required|trim');
            $this->form_validation->set_rules('account_id', 'Account', 'required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'name' => $this->input->post('name', true),
                    'account_id' => $this->input->post('account_id')
                );
                
                if ($this->paymentmethods->add($data)) {
                    $this->session->set_flashdata('message', 'Payment method added successfully');
                } else {
                    $this->session->set_flashdata('error', 'Error adding payment method');
                }
                redirect('paymentmethods');
            }
        }
        
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Add Payment Method';
        $data['accounts'] = $this->accounts->accountslist();
        $this->load->view('fixed/header', $head);
        $this->load->view('paymentmethods/add', $data);
        $this->load->view('fixed/footer');
    }

    public function edit($id)
    {
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', 'Payment Method Name', 'required|trim');
            $this->form_validation->set_rules('account_id', 'Account', 'required|numeric');
            
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'name' => $this->input->post('name', true),
                    'account_id' => $this->input->post('account_id')
                );
                
                if ($this->paymentmethods->update($id, $data)) {
                    $this->session->set_flashdata('message', 'Payment method updated successfully');
                } else {
                    $this->session->set_flashdata('error', 'Error updating payment method');
                }
                redirect('paymentmethods');
            }
        }
        
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Edit Payment Method';
        $data['payment_method'] = $this->paymentmethods->get($id);
        $data['accounts'] = $this->accounts->accountslist();
        
        if (!$data['payment_method']) {
            $this->session->set_flashdata('error', 'Payment method not found');
            redirect('paymentmethods');
        }
        
        $this->load->view('fixed/header', $head);
        $this->load->view('paymentmethods/edit', $data);
        $this->load->view('fixed/footer');
    }

    public function delete($id)
    {
        if ($this->paymentmethods->delete($id)) {
            $this->session->set_flashdata('message', 'Payment method deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Error deleting payment method');
        }
        redirect('paymentmethods');
    }
}
