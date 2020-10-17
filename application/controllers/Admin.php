<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('user_model');
        $this->load->helper('form');
    }

    // page d'accueil
    public function index()
    {
      if (isset($_SESSION['userId']) && $_SESSION['userId'] != '' && $_SESSION['userAdmin'] == 1) {
        $data['users'] = $this->user_model->getUserList();

        $this->load->view('blocks/header');
        $this->load->view('pages/admin', $data);
        $this->load->view('blocks/footer');
      } else {
          redirect('access/login');
      }
    }

    public function getModalForm() {
        if ($this->input->is_ajax_request()) {
            $formType = $this->input->post('formType');
            $data = [];

            if ($formType == 'user') {
                $userId = $this->input->post('elementId');
                // cas de l'update
                if ($userId != 0)
                {
                    $data = $this->user_model->getUserArrayById($userId);
                    $data['operation'] = 'Modification';
                }
                else
                {
                // cas de l'insert
                    $data = [
                      'name' => '',
                      'mail' => '',
                      'hasList' => 0,
                      'allowedViews' => '',
                      'isAdmin' => 0,
                      'operation' => 'Ajout'
                    ];
                }
                $data['user_id']  = $userId;

                echo $this->load->view('modals/user_form', $data, true);
            }
        }
    }

    public function getModalConfirm() {
        if ($this->input->is_ajax_request())
        {
            $data['element'] = $this->user_model->getUserArrayById($this->input->post('elementId'), true);
            $data['element']['title'] = $data['element']['name'];
            $data['element']['type'] = 'user';
            echo $this->load->view('modals/del_confirm', $data, true);
        }
    }

    public function deleteUser() {
        if ($this->input->is_ajax_request())
        {
           $this->user_model->delete($this->input->post('elementId'));
       }
    }

    public function upsertUser() {
        $params = $this->input->post();

        if ($params['user_id'] == 0)
        {
            // generate a first password
            $name = strtolower(preg_replace('/[^a-zA-Z]/','0',$params['name']));
            $pwd = substr($name, 0, 3) . strlen($name);
            $hash = password_hash($pwd, PASSWORD_BCRYPT);
            $params['password'] = $hash;

            $this->user_model->add($params);
        }
        else
        {
            $this->user_model->update($params);
        }
        redirect('admin');
    }
}
