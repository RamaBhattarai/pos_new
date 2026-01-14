<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load auth if needed
        $this->load->library('aauth'); 
    }

    public function plans() {
        $user = $this->aauth->get_user();
        $head['usernm'] = $user ? $user->username : 'Guest';
        $head['title'] = 'Subscription Plans';

        $this->load->view('fixed/header', $head);
        $this->load->view('subscription/plan'); // make sure this view exists
        $this->load->view('fixed/footer');
    }
}
