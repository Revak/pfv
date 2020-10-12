<?php

class Gift_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getById($id, $short = false) {
        if ($short)
        {
            $this->db->select('id, title');
        }
        $query  = $this->db->get_where('pfv_gift', ['id' => $id]);
        return $query->row_array();
    }

    public function add($postData) {
        $newGift = [
            'title'         => $postData['name'],
            'url'           => $postData['url'],
            'description'   => $postData['description'],
            'owner'         => $postData['owner_id'],
            'year'          => date('Y'),
        ];

        $this->db->insert('pfv_gift', $newGift);
    }

    public function update($postData) {
        $newGift = [
            'title'         => $postData['name'],
            'url'           => $postData['url'],
            'description'   => $postData['description'],
        ];

        $this->db->where('id', $postData['gift_id']);
        $this->db->update('pfv_gift', $newGift);
    }

    public function reserve($gift_id, $reserver_id) {
        $newGift = ['reserver' => $reserver_id];

        $this->db->where('id', $gift_id);
        $this->db->update('pfv_gift', $newGift);
    }

    public function delete($id) {
        $this->db->delete('pfv_gift', ['id' => $id]);
    }

    public function getGiftsByYear($year = '') {
        // current year by default
        if ($year == '')
        {
          $year = date('Y');
        }

        $this->db->order_by('owner ASC, id ASC');
        $query  = $this->db->get_where('pfv_gift', ['year' => $year]);
        $gifts = $query->result_array();

        $grouped_gifts = [];
        foreach ($gifts as $gift)
        {
            $grouped_gifts[$gift['owner']][$gift['id']] = $gift;
        }
        return $grouped_gifts;
    }

    public function getPastYearsList() {
        $this->db->distinct();
        $this->db->select('year');
        $this->db->order_by('year', 'DESC');
        $query = $this->db->get_where('pfv_gift', ['year !=' => date('Y')]);
        return $query->result();
    }
}
