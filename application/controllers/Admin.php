<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
    }

    // page d'accueil
    public function index()
    {
      if (isset($_SESSION['userId']) && $_SESSION['userId'] != '' && $_SESSION['userAdmin'] == 1) {
          $this->load->view('blocks/header');
          $this->load->view('pages/admin');
          $this->load->view('blocks/footer');
      } else {
          redirect('access/login');
      }
    }

}
