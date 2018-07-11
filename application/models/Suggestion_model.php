<?php

class Suggestion_model extends CI_Model {
    
    public function __construct() {
        $this->load->database();
    }

    public function getById($id) {
        $query  = $this->db->get_where('pfv_suggestion', array('id' => $id));
        return $query->row_array();
    }

    public function getByTarget($target_id) {
        $query  = $this->db->get_where('pfv_suggestion', array('target' => $target_id));
        return $query->result_array();
    }

    public function add($postData) {
        $newSuggestion = array(
            'text'   => $postData['suggestionText'],
            'author' => $postData['author_id'],
            'target' => $postData['target_id'],
        );

        $this->db->insert('pfv_suggestion', $newSuggestion);
    }

    public function delete($id) {
        $this->db->delete('pfv_suggestion', ['id' => $id]);
    }
}