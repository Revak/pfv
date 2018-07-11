<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('gift_model');
    }

    // page d'accueil
    public function index()
    {
        redirect('page/giftList');
    }

    // appel d'une page
    private function render($page, $header = 'header', $data ='')
    {
        if (isset($_SESSION['userId']) && $_SESSION['userId'] != '') {
            $this->load->view('blocks/'.$header);
            $this->load->view('pages/'.$page, $data);
            $this->load->view('blocks/footer');
        } else {
            $this->load->view('blocks/header_short');
            $this->load->view('pages/login');
            $this->load->view('blocks/footer');
        }
    }

    // page de connexion
    public function login() {
        $this->load->model('user_model');

        // validator
        $this->form_validation->set_rules(array(
            array(
                'field' => 'name',
                'label' => '"nom"',
                'rules' => 'required',
                'errors' => array(
                    'required' => 'Le champ %s doit être rempli.'
                )
            ),
            array(
                'field' => 'password',
                'label' => '"mot de passe"',
                'rules' => 'required',
                'errors' => array(
                    'required' => 'Le champ %s doit être rempli.'
                )
            )
        ));

        if ($this->form_validation->run() === FALSE)
        {
            $this->render('login', 'header_short');
        }

        $form_data = $this->input->post();
        $user = $this->user_model->getUserByName($form_data['name']);

        if ($user && password_verify($form_data['password'], $user->password)) {
            $_SESSION['userId']     = $user->id;
            $_SESSION['userName']   = $user->name;
            $_SESSION['userMail']   = $user->mail;
            $_SESSION['userAlerts'] = $user->alerts;
            $_SESSION['userAdmin']  = $user->isAdmin;

            $this->index();
        } else {
            $data['form_error'] = 'Couple nom/mot de passe incorrect';
            $this->render('login', 'header_short', $data);
        }
    }

    // mdp oublié
    public function forgottenPwd() {
        $this->load->model('user_model');
        $dest_mail = $this->input->post('mail');
        $idFromMail = $this->user_model->isValidEmail($dest_mail);
        if ($idFromMail) {
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

            $this->render('forgottenPwd');
        } else {
            $data['form_error'] = 'L\'adresse email n\'a pas été trouvée.';
            $this->render('login', 'header_short', $data);
        }
    }

    public function editAccount() {
        $this->render('account');
    }

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

    public function news() {
        $this->render('news');
    }

    public function admin() {
        $this->render('admin');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('page');
    }

    public function giftList() {
        $this->load->model('user_model');

        // get users
        $data['users'] = $this->user_model->getUserList();
        // get lists
        $data['gifts'] = $this->gift_model->getGiftsByYear();

        $this->render('list', 'header', $data);
    }

    public function history() {
        $this->load->model('user_model');

        // get users
        $data['users'] = $this->user_model->getUserList();
        // get lists
        $last_year = date('Y') - 1;
        $data['gifts'] = $this->gift_model->getGiftsByYear($last_year);
        // get preceding years
        $data['years'] = $this->gift_model->getPastYearsList();

        $this->render('history', 'header', $data);
    }

    public function listByYear() {
        if ($this->input->is_ajax_request()) {
            $year = $this->input->post('year');
            $data['gifts'] = $this->gift_model->getGiftsByYear($year);
            echo  $this->load->view('pages/historyList', $data, true);
        }
    }

    public function getModalForm() {
        if ($this->input->is_ajax_request()) {
            $formType = $this->input->post('formType');
            $data = '';

            if ($formType == 'gift') {
                $giftId = $this->input->post('elementId');
                // cas de l'update
                if ($giftId != 0) {
                    $data = $this->gift_model->getById($giftId);
                    $data['operation'] = 'Modification';
                } else {
                // cas de l'insert
                    $data['title']       = '';
                    $data['url']         = '';
                    $data['description'] = '';
                    $data['operation']   = 'Ajout';
                }
                $data['gift_id']  = $this->input->post('elementId');
                $data['owner_id'] = $_SESSION['userId'];
                
                echo $this->load->view('modals/gift_form', $data, true);
            } elseif ($formType == 'suggestion') {
                $data['target'] = $this->input->post('elementId');
                $data['author'] = $_SESSION['userId'];
                
                echo $this->load->view('modals/suggestion_form', $data, true);
            }
        }
    }

    public function upsertGift() {
        $params = $this->input->post();

        if ($params['gift_id'] == 0) {
            $this->gift_model->add($params);
            $this->send_alerts($params);
        } else {
            $this->gift_model->update($params);
        }
        redirect('page/giftList');
    }

    public function getModalConfirm() {
        if ($this->input->is_ajax_request()) {
            $data['gift'] = $this->gift_model->getById($this->input->post('elementId'), true);
            echo $this->load->view('modals/del_confirm', $data, true);
        }
    }

    public function deleteGift() {
        if ($this->input->is_ajax_request()) {
           $gift_id = $this->input->post('giftId');

           $this->gift_model->delete($gift_id);
       }
    }

    public function reserveGift() {
        if ($this->input->is_ajax_request()) {
           $gift_id = $this->input->post('giftId');
           $reserver_id = $this->input->post('reserver');

           $this->gift_model->reserve($gift_id, $reserver_id);
       }
    }

    public function addSuggestion() {
        $this->load->model('suggestion_model');
        $params = $this->input->post();
        $this->suggestion_model->add($params);
    }

    public function deleteSuggestion() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('suggestion_model');
            $suggestion_id = $this->input->post('suggestionId');
            $this->suggestion_model->delete($suggestion_id);
       }
    }

    public function getSuggestionList() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('suggestion_model');
            $this->load->model('user_model');

            $data['suggestions'] = $this->suggestion_model->getByTarget($this->input->post('targetId'));
            $data['users'] = $this->user_model->getUserList();
            echo $this->load->view('pages/suggestionList', $data, true);
        }
    }

    public function changeAlertSettings() {
        $this->load->model('user_model');

        $user = $this->input->post('user_id');
        $alerts = ($this->input->post('alerts') == 1) ? $this->input->post('alerts') : 0;
        $this->user_model->toggleAlerts($user, $alerts);
        $this->session->set_flashdata('form_msg', 'Paramêtre mis à jour');
        redirect('page/editAccount');
    }

    private function send_alerts($new_gift) {
        // get mails
        $this->load->model('user_model');
        $mails = $this->user_model->getMailsForAlerts($new_gift['owner_id']);

        // user data
        $user = $this->user_model-getUserById($new_gift['owner_id']);
        $username = ucfirst($user['name']);

        // send
        $this->load->library('email');

        foreach ($mails as $mail) {
            $this->email->clear();
            $this->email->from('admin@pfv.fr', 'La porte du frigo virtuelle');
            $this->email->to($mail);
            $this->email->subject('Nouveau cadeau ajouté');
            $this->email->message("Bonjour, \n ".$username." vient d'ajouter le cadeau suivant à sa liste : \n"
                .$new_gift['name']."\n"
                ."Connectez-vous sur le site pour consulter les listes.");
            // $this->email->send();
        }
    }
}
