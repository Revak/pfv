<?php

class Suggestion_model extends CI_Model {

    const MODEL_TABLE = 'pfv_suggestion';

    public function __construct() {
        $this->load->database();
    }

    public function getById($id) {
        $query  = $this->db->get_where(self::MODEL_TABLE, ['id' => $id]);
        return $query->row_array();
    }

    public function getByTargetForCurrentYear($target_id) {
        $query  = $this->db->get_where(self::MODEL_TABLE, ['target' => $target_id, 'year' => date('Y')]);
        return $query->result_array();
    }

    public function add($postData) {
        $newSuggestion = [
            'text'   => $postData['suggestionText'],
            'author' => $postData['author_id'],
            'target' => $postData['target_id'],
            'year'   => date('Y')
        ];

        $this->db->insert(self::MODEL_TABLE, $newSuggestion);
    }

    public function delete($id) {
        $this->db->delete(self::MODEL_TABLE, ['id' => $id]);
    }
}
