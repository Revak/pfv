<?php

class User_model extends CI_Model {

    const MODEL_TABLE = 'pfv_user';

    public function __construct()
    {
        $this->load->database();
    }

    public function getUserByName($name) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['name' => $name]);
        $result = $query->row();

        return (!isset($result)) ? false : $result;
    }

    public function getUserById($id) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['id' => $id]);
        $result = $query->row();

        return (!isset($result)) ? false : $result;
    }

    public function getUserArrayById($id) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['id' => $id]);
        return $query->row_array();
    }

    public function getUsername($id) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['id' => $id]);
        $result = $query->row();

        return (!isset($result)) ? false : $result->name;
    }

    public function login($name, $password) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['name' => $name, 'password' => $password]);
        $result = $query->row();

        return (!isset($result)) ? false : $result;
    }

    public function isValidEmail($mail) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['mail' => $mail]);
        $result = $query->row();

        return (!isset($result)) ? false : $result->id;
    }

    public function updatePwd($id, $new_pwd) {
        $this->db->set('password', $new_pwd);
        $this->db->where('id', $id);
        $this->db->update(self::MODEL_TABLE);
    }

    public function isValidPassword($id, $password) {
        $query = $this->db->get_where(self::MODEL_TABLE, ['id' => $id]);
        $result = $query->row();
        return password_verify($password, $result->password);
    }

    public function add($postData) {
        $newUser = [
            'name' => $postData['name'],
            'mail' => $postData['email'],
            'hasList' => (int) $postData['hasList'],
            'password' => $postData['password'],
            'allowedViews' => $postData['allowedViews'] ?: null,
            'isAdmin' => (int) $postData['isAdmin'],
            'alerts' => 0
        ];

        $this->db->insert(self::MODEL_TABLE, $newUser);
    }

    public function update($postData) {
      $newUser = [
          'name' => $postData['name'],
          'mail' => $postData['email'],
          'hasList' => (int) $postData['hasList'],
          'allowedViews' => $postData['allowedViews'] ?: null,
          'isAdmin' => (int) $postData['isAdmin']
      ];

        $this->db->where('id', $postData['user_id']);
        $this->db->update(self::MODEL_TABLE, $newUser);
    }

    public function delete($id) {
        $this->db->delete(self::MODEL_TABLE, ['id' => $id]);
    }

    public function getUserList() {
        $this->db->select('id, name, hasList');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get(self::MODEL_TABLE);
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
        $this->db->update(self::MODEL_TABLE);
        $_SESSION['userAlerts'] = $alerts;
    }

    public function getMailsForAlerts($userId, $userName)
    {
        $this->db->select('mail, allowedViews');
        $query = $this->db->get_where(self::MODEL_TABLE, ['id!=' => $userId, 'alerts' => 1]);
        $results = $query->result_array();
        return $this->filterUserMailResults($results, $userName);
    }

    protected function filterUserMailResults($usersData, $userName)
    {
        $mails = [];
        foreach ($usersData as $data) {
            if (!$data['allowedViews'] || strpos($data['allowedViews'], strtolower($userName)) !== false)
            {
                $mails[] = $data['mail'];
            }
        }
        return $mails;
    }
}
