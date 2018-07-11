<?php

class User_model extends CI_Model {
    
    public function __construct()
    {
        $this->load->database();
    }

    public function getUserByName($name) {
        $query = $this->db->get_where('pfv_user', array('name' => $name));
        $result = $query->row();

        return (!isset($result)) ? false : $result;
    }

    public function login($name, $password) {
        $query = $this->db->get_where('pfv_user', array('name' => $name, 'password' => $password));
        $result = $query->row();

        return (!isset($result)) ? false : $result;
    }

    public function isValidEmail($mail) {
        $query = $this->db->get_where('pfv_user', array('mail' => $mail));
        $result = $query->row();

        return (!isset($result)) ? false : $result->id;
    }

    public function updatePwd($id, $new_pwd) {
        $this->db->set('password', $new_pwd);
        $this->db->where('id', $id);
        $this->db->update('pfv_user');
    }

    public function isValidPassword($id, $password) {
        $query = $this->db->get_where('pfv_user', array('id' => $id));
        $result = $query->row();
        return password_verify($password, $result->password);
    }

    public function getUserList() {
        $this->db->select('id, name');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get('pfv_user');
        $result = $query->result_array();

        foreach ($result as $user) {
            $users[$user['id']] = $user;
        }
        return $users;
    }

    public function toggleAlerts($user_id, $alerts)
    {
        $this->db->set('alerts', $alerts);
        $this->db->where('id', $user_id);
        $this->db->update('pfv_user');
        $_SESSION['userAlerts'] = $alerts;
    }

    public function getMailsForAlerts($user)
    {
        $this->db->select('mail');
        $query = $this->db->get_where('pfv_user', ['id!=' => $user]);
        return $result = $query->result_array();
    }
}