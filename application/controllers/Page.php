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
    private function render($page, $headerType = 'header', $data ='')
    {
        if (isset($_SESSION['userId']) && $_SESSION['userId'] != '') {
            $this->load->view('blocks/'.$headerType);
            $this->load->view('pages/'.$page, $data);
            $this->load->view('blocks/footer');
        } else {
            redirect('access/login');
        }
    }

    public function editAccount() {
        $this->render('account');
    }

    public function news() {
        $this->render('news');
    }

    public function giftList() {
        $this->load->model('user_model');
        $data = [
          'users' => $this->user_model->getUserList(),
          'gifts' => $this->gift_model->getGiftsByYear()
        ];

        $this->render('list', 'header', $data);
    }

    public function history() {
        $this->load->model('user_model');
        $last_year = date('Y') - 1;
        $data = [
          'users' => $this->user_model->getUserList(),
          'gifts' => $this->gift_model->getGiftsByYear($last_year),
          'years' => $this->gift_model->getPastYearsList()
        ];

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
            $data = [];

            if ($formType == 'gift') {
                $giftId = $this->input->post('elementId');
                // cas de l'update
                if ($giftId != 0)
                {
                    $data = $this->gift_model->getById($giftId);
                    $data['operation'] = 'Modification';
                }
                else
                {
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

        if ($params['gift_id'] == 0)
        {
            $this->gift_model->add($params);
            $this->send_alerts($params);
        }
        else
        {
            $this->gift_model->update($params);
        }
        redirect('page/giftList');
    }

    public function getModalConfirm() {
        if ($this->input->is_ajax_request())
        {
            $data['element'] = $this->gift_model->getById($this->input->post('elementId'), true);
            $data['element']['type'] = 'gift';
            echo $this->load->view('modals/del_confirm', $data, true);
        }
    }

    public function deleteGift() {
        if ($this->input->is_ajax_request())
        {
           $this->gift_model->delete($this->input->post('elementId'));
       }
    }

    public function reserveGift() {
        if ($this->input->is_ajax_request())
        {
           $gift_id = $this->input->post('giftId');
           $reserver_id = $this->input->post('reserver');
           $this->gift_model->reserve($gift_id, $reserver_id);
       }
    }

    public function addSuggestion() {
        $this->load->model('suggestion_model');
        $this->suggestion_model->add($this->input->post());
    }

    public function deleteSuggestion() {
        if ($this->input->is_ajax_request())
        {
            $this->load->model('suggestion_model');
            $suggestion_id = $this->input->post('suggestionId');
            $this->suggestion_model->delete($suggestion_id);
       }
    }

    public function getSuggestionList() {
        if ($this->input->is_ajax_request())
        {
            $this->load->model('suggestion_model');
            $this->load->model('user_model');

            $data['suggestions'] = $this->suggestion_model->getByTargetForCurrentYear($this->input->post('targetId'));
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
        $this->load->model('user_model');
        $userName = $this->user_model->getUsername($new_gift['owner_id']);
        $mails = $this->user_model->getMailsForAlerts($new_gift['owner_id'], $userName);

        // send
        $this->load->library('email');

        foreach ($mails as $mail) {
            $this->email->clear();
            $this->email->from('admin@pfv.fr', 'La porte du frigo virtuelle');
            $this->email->to($mail);
            $this->email->subject('Nouveau cadeau ajouté');
            $this->email->message("Bonjour, \n ".ucfirst($userName)." vient d'ajouter le cadeau suivant à sa liste : ".$new_gift['name']."\n"
                ."Connectez-vous sur le site pour consulter les listes.");

            $this->email->send();
        }
    }
}
