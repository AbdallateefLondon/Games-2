<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Educationalgame_model extends MY_Model
{
    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get educational games with optional filters
     * @param int $id
     * @param array $filters
     * @return mixed
     */
    public function get($id = null, $filters = array())
    {
        $this->db->select('educational_games.*, classes.class, sections.section, subjects.name as subject_name, 
                          staff.name as creator_name, staff.surname as creator_surname');
        $this->db->from('educational_games');
        $this->db->join('classes', 'classes.id = educational_games.class_id', 'left');
        $this->db->join('sections', 'sections.id = educational_games.section_id', 'left');
        $this->db->join('subjects', 'subjects.id = educational_games.subject_id', 'left');
        $this->db->join('staff', 'staff.id = educational_games.created_by', 'left');

        if ($id != null) {
            $this->db->where('educational_games.id', $id);
        }

        // Apply filters
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('educational_games.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('educational_games.section_id', $filters['section_id']);
        }
        if (isset($filters['game_type']) && $filters['game_type'] !== '') {
            $this->db->where('educational_games.game_type', $filters['game_type']);
        }
        if (isset($filters['created_by']) && $filters['created_by'] !== '') {
            $this->db->where('educational_games.created_by', $filters['created_by']);
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('educational_games.is_active', $filters['is_active']);
        }

        $this->db->where('educational_games.is_active', 1);
        $this->db->order_by('educational_games.created_at', 'DESC');

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get games for specific student based on their class/section
     * @param int $student_id
     * @return array
     */
    public function getStudentGames($student_id)
    {
        // Get student's current class and section
        $student_info = $this->getStudentClassSection($student_id);
        
        if (!$student_info) {
            return array();
        }

        $this->db->select('educational_games.*, classes.class, sections.section, subjects.name as subject_name,
                          staff.name as creator_name, staff.surname as creator_surname,
                          COALESCE(attempts.attempt_count, 0) as attempts_made');
        $this->db->from('educational_games');
        $this->db->join('classes', 'classes.id = educational_games.class_id', 'left');
        $this->db->join('sections', 'sections.id = educational_games.section_id', 'left');
        $this->db->join('subjects', 'subjects.id = educational_games.subject_id', 'left');
        $this->db->join('staff', 'staff.id = educational_games.created_by', 'left');
        
        // Left join to get attempt count
        $this->db->join('(SELECT game_id, COUNT(*) as attempt_count FROM game_results WHERE student_id = ' . $student_id . ' GROUP BY game_id) as attempts', 
                       'attempts.game_id = educational_games.id', 'left');

        $this->db->where('educational_games.is_active', 1);
        $this->db->group_start();
        $this->db->where('educational_games.class_id', $student_info['class_id']);
        $this->db->where('educational_games.section_id', $student_info['section_id']);
        $this->db->or_where('educational_games.class_id IS NULL'); // Global games
        $this->db->group_end();
        
        $this->db->order_by('educational_games.created_at', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student's class and section information
     * @param int $student_id
     * @return array|null
     */
    private function getStudentClassSection($student_id)
    {
        $this->db->select('student_session.class_id, student_session.section_id');
        $this->db->from('student_session');
        $this->db->where('student_session.student_id', $student_id);
        $this->db->where('student_session.session_id', $this->current_session);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Add or update educational game
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        if (isset($data['id']) && $data['id'] > 0) {
            // Update existing game
            $this->db->where('id', $data['id']);
            $this->db->update('educational_games', $data);
            $message = UPDATE_RECORD_CONSTANT . " On educational_games id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            // Insert new game
            unset($data['id']);
            $this->db->insert('educational_games', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On educational_games id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return isset($insert_id) ? $insert_id : $data['id'];
        }
    }

    /**
     * Delete educational game
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('educational_games');
        
        $message = DELETE_RECORD_CONSTANT . " On educational_games id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get game statistics for dashboard
     * @param int $created_by
     * @return array
     */
    public function getGameStats($created_by = null)
    {
        $stats = array();

        // Total games
        $this->db->select('COUNT(*) as total_games');
        $this->db->from('educational_games');
        if ($created_by) {
            $this->db->where('created_by', $created_by);
        }
        $this->db->where('is_active', 1);
        $query = $this->db->get();
        $stats['total_games'] = $query->row()->total_games;

        // Games by type
        $this->db->select('game_type, COUNT(*) as count');
        $this->db->from('educational_games');
        if ($created_by) {
            $this->db->where('created_by', $created_by);
        }
        $this->db->where('is_active', 1);
        $this->db->group_by('game_type');
        $query = $this->db->get();
        $stats['games_by_type'] = $query->result_array();

        // Most played games
        $this->db->select('educational_games.title, educational_games.game_type, COUNT(game_results.id) as play_count');
        $this->db->from('educational_games');
        $this->db->join('game_results', 'game_results.game_id = educational_games.id', 'left');
        if ($created_by) {
            $this->db->where('educational_games.created_by', $created_by);
        }
        $this->db->where('educational_games.is_active', 1);
        $this->db->group_by('educational_games.id');
        $this->db->order_by('play_count', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        $stats['most_played'] = $query->result_array();

        return $stats;
    }

    /**
     * Validate game data structure
     * @param string $game_type
     * @param string $game_content
     * @return bool
     */
    public function validateGameContent($game_type, $game_content)
    {
        $content = json_decode($game_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        switch ($game_type) {
            case 'quiz':
                return $this->validateQuizContent($content);
            case 'matching':
                return $this->validateMatchingContent($content);
            default:
                return false;
        }
    }

    /**
     * Validate quiz content structure
     * @param array $content
     * @return bool
     */
    private function validateQuizContent($content)
    {
        if (!isset($content['questions']) || !is_array($content['questions'])) {
            return false;
        }

        foreach ($content['questions'] as $question) {
            if (!isset($question['question']) || !isset($question['options']) || !isset($question['correct_answer'])) {
                return false;
            }
            if (!is_array($question['options']) || count($question['options']) < 2) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate matching content structure
     * @param array $content
     * @return bool
     */
    private function validateMatchingContent($content)
    {
        if (!isset($content['pairs']) || !is_array($content['pairs'])) {
            return false;
        }

        foreach ($content['pairs'] as $pair) {
            if (!isset($pair['left']) || !isset($pair['right'])) {
                return false;
            }
        }

        return count($content['pairs']) >= 3; // Minimum 3 pairs
    }

    /**
     * Get game template structure
     * @param string $game_type
     * @return array
     */
    public function getGameTemplate($game_type)
    {
        switch ($game_type) {
            case 'quiz':
                return array(
                    'questions' => array(
                        array(
                            'question' => '',
                            'options' => array('', '', '', ''),
                            'correct_answer' => 0,
                            'explanation' => '',
                            'points' => 10
                        )
                    )
                );
            case 'matching':
                return array(
                    'pairs' => array(
                        array(
                            'left' => '',
                            'right' => '',
                            'points' => 10
                        )
                    )
                );
            default:
                return array();
        }
    }
}