<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('user_model');
    }

    // appel d'une page
    private function render($page, $headerType = 'header', $data ='')
    {
        $this->load->view('blocks/'.$headerType);
        $this->load->view('pages/'.$page, $data);
        $this->load->view('blocks/footer');
    }

    /**
      * Page de connexion
      */
    public function login() {
      if (!$form_data = $this->input->post())
      {
        $this->load->view('blocks/header_short');
        $this->load->view('pages/login');
        $this->load->view('blocks/footer');
      }
      else {
        $this->form_validation->set_rules([
            [
              'field' => 'name',
              'label' => '"identifiant"',
              'rules' => 'required',
              'errors' => ['required' => 'Le champ %s doit être rempli.']
            ],
            [
              'field' => 'password',
              'label' => '"mot de passe"',
              'rules' => 'required',
              'errors' => ['required' => 'Le champ %s doit être rempli.']
            ]
        ]);

        if ($this->form_validation->run() === FALSE)
        {
            $this->render('login', 'header_short');
            return;
        }

        $user = $this->user_model->getUserByName($form_data['name']);
        if ($user && password_verify($form_data['password'], $user->password))
        {
            $_SESSION['userId']     = $user->id;
            $_SESSION['userName']   = $user->name;
            $_SESSION['userMail']   = $user->mail;
            $_SESSION['userAlerts'] = $user->alerts;
            $_SESSION['userAdmin']  = $user->isAdmin;

            redirect('page/index');
        } else {
            $data['form_error'] = 'Couple nom/mot de passe incorrect';
            $this->render('login', 'header_short', $data);
        }
      }
    }

    /**
      * Mot de passe oublié
      */
    public function forgottenPwd() {
        $dest_mail = $this->input->post('mail');
        if ($idFromMail = $this->user_model->isValidEmail($dest_mail)) {
            // création nouveau mdp aléatoire
            $new_pwd = uniqid();
            $hash = password_hash($new_pwd, PASSWORD_BCRYPT);
            $this->user_model->updatePWD($idFromMail, $hash);

            // envoi du mail
            $this->load->library('email');

            $this->email->from('admin@pfv.fr', 'La porte du frigo virtuelle');
            $this->email->to($dest_mail);
            $this->email->subject('Votre Nouveau mot de passe');
            $this->email->message("Bonjour, \n Vous avez demandé un nouveau mot de passe pour le site de La porte du frigo virtuelle suite à un oubli.\n".
                "Votre nouveau mot de passe est : ".$new_pwd."\n".
                "N'hésitez pas à changer de mot de passe par la suite."
            );
            $this->email->send();

            $this->render('forgottenPwd', 'header_short');
        } else {
            $data['form_error'] = 'L\'adresse email n\'a pas été trouvée.';
            $this->render('login', 'header_short', $data);
        }
    }

    /**
      * Changement de mot de passe
      */
    public function editPwd() {
        $this->load->model('user_model');
        extract($this->input->post());

        $oldPwdOk = $this->user_model->isValidPassword($_SESSION['userId'], $old_pwd);

        if ($oldPwdOk && $new_pwd === $confirm_pwd) {
            $hash = password_hash($new_pwd, PASSWORD_BCRYPT);
            $this->user_model->updatePwd($_SESSION['userId'], $hash);
            $this->render('updatedPwd');
        } else {
            $data['form_error'] = 'Les mots de passe ne correspondent pas.';
            $this->render('account', 'header', $data);
        }
    }

    /**
      * Deconnexion
      */
    public function logout() {
        $this->session->sess_destroy();
        redirect('page/index');
    }
}
