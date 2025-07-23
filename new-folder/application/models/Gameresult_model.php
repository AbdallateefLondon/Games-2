<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Gameresult_model extends MY_Model
{
    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get game results with optional filters
     * @param int $id
     * @param array $filters
     * @return mixed
     */
    public function get($id = null, $filters = array())
    {
        $this->db->select('game_results.*, educational_games.title as game_title, educational_games.game_type,
                          students.firstname, students.lastname, students.admission_no,
                          classes.class, sections.section');
        $this->db->from('game_results');
        $this->db->join('educational_games', 'educational_games.id = game_results.game_id');
        $this->db->join('students', 'students.id = game_results.student_id');
        $this->db->join('student_session', 'student_session.id = game_results.student_session_id');
        $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'left');

        if ($id != null) {
            $this->db->where('game_results.id', $id);
        }

        // Apply filters
        if (isset($filters['game_id']) && $filters['game_id'] !== '') {
            $this->db->where('game_results.game_id', $filters['game_id']);
        }
        if (isset($filters['student_id']) && $filters['student_id'] !== '') {
            $this->db->where('game_results.student_id', $filters['student_id']);
        }
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }
        if (isset($filters['date_from']) && $filters['date_from'] !== '') {
            $this->db->where('DATE(game_results.completed_at) >=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && $filters['date_to'] !== '') {
            $this->db->where('DATE(game_results.completed_at) <=', $filters['date_to']);
        }

        $this->db->order_by('game_results.completed_at', 'DESC');

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Add game result
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Insert game result
        $this->db->insert('game_results', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Update student points
            $this->updateStudentPoints($data['student_id'], $data['student_session_id'], $data['points_earned']);
            
            $message = INSERT_RECORD_CONSTANT . " On game_results id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    /**
     * Update student points after game completion
     * @param int $student_id
     * @param int $student_session_id
     * @param int $points_earned
     */
    private function updateStudentPoints($student_id, $student_session_id, $points_earned)
    {
        // Check if student points record exists
        $this->db->select('*');
        $this->db->from('student_points');
        $this->db->where('student_id', $student_id);
        $this->db->where('student_session_id', $student_session_id);
        $query = $this->db->get();
        $student_points = $query->row_array();

        if ($student_points) {
            // Update existing record
            $new_total = $student_points['total_points'] + $points_earned;
            $new_level = $this->calculateLevel($new_total);
            $points_to_next = $this->getPointsToNextLevel($new_level, $new_total);
            
            // Get updated stats
            $stats = $this->getStudentGameStats($student_id);
            
            $update_data = array(
                'total_points' => $new_total,
                'current_level' => $new_level,
                'points_to_next_level' => $points_to_next,
                'games_played' => $stats['games_played'],
                'games_completed' => $stats['games_completed'],
                'average_score' => $stats['average_score'],
                'best_score' => $stats['best_score'],
                'total_time_played' => $stats['total_time_played'],
                'last_played' => date('Y-m-d H:i:s')
            );

            $this->db->where('id', $student_points['id']);
            $this->db->update('student_points', $update_data);
        } else {
            // Create new record
            $level = $this->calculateLevel($points_earned);
            $points_to_next = $this->getPointsToNextLevel($level, $points_earned);
            
            $stats = $this->getStudentGameStats($student_id);
            
            $insert_data = array(
                'student_id' => $student_id,
                'student_session_id' => $student_session_id,
                'total_points' => $points_earned,
                'current_level' => $level,
                'points_to_next_level' => $points_to_next,
                'games_played' => $stats['games_played'],
                'games_completed' => $stats['games_completed'],
                'average_score' => $stats['average_score'],
                'best_score' => $stats['best_score'],
                'total_time_played' => $stats['total_time_played'],
                'last_played' => date('Y-m-d H:i:s')
            );

            $this->db->insert('student_points', $insert_data);
        }
    }

    /**
     * Calculate level based on total points using non-linear progression
     * Level 1: 0-10, Level 2: 11-25, Level 3: 26-45, Level 4: 46-70, Level 5: 71-100
     * Formula: Level N requires N² * 2 points to complete
     * @param int $total_points
     * @return int
     */
    private function calculateLevel($total_points)
    {
        $level = 1;
        $points_required = 0;
        
        while ($points_required < $total_points) {
            $level++;
            $points_required += ($level * $level * 2);
        }
        
        return $level - 1; // Return the completed level
    }

    /**
     * Get points required to reach next level
     * @param int $current_level
     * @param int $current_points
     * @return int
     */
    private function getPointsToNextLevel($current_level, $current_points)
    {
        $next_level = $current_level + 1;
        $points_for_next_level = 0;
        
        for ($i = 1; $i <= $next_level; $i++) {
            $points_for_next_level += ($i * $i * 2);
        }
        
        return $points_for_next_level - $current_points;
    }

    /**
     * Get student game statistics
     * @param int $student_id
     * @return array
     */
    private function getStudentGameStats($student_id)
    {
        $this->db->select('COUNT(*) as games_played, 
                          COUNT(CASE WHEN score > 0 THEN 1 END) as games_completed,
                          COALESCE(AVG(score), 0) as average_score,
                          COALESCE(MAX(score), 0) as best_score,
                          COALESCE(SUM(time_taken), 0) as total_time_played');
        $this->db->from('game_results');
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();
        
        $result = $query->row_array();
        return array(
            'games_played' => (int)$result['games_played'],
            'games_completed' => (int)$result['games_completed'],
            'average_score' => round($result['average_score'], 2),
            'best_score' => (int)$result['best_score'],
            'total_time_played' => (int)$result['total_time_played']
        );
    }

    /**
     * Get student attempts for a specific game
     * @param int $game_id
     * @param int $student_id
     * @return int
     */
    public function getStudentAttempts($game_id, $student_id)
    {
        $this->db->select('COUNT(*) as attempts');
        $this->db->from('game_results');
        $this->db->where('game_id', $game_id);
        $this->db->where('student_id', $student_id);
        $query = $this->db->get();
        return $query->row()->attempts;
    }

    /**
     * Get leaderboard for a specific game
     * @param int $game_id
     * @param int $limit
     * @return array
     */
    public function getGameLeaderboard($game_id, $limit = 10)
    {
        $this->db->select('students.firstname, students.lastname, students.admission_no,
                          classes.class, sections.section,
                          MAX(game_results.score) as best_score,
                          MAX(game_results.points_earned) as best_points,
                          MIN(game_results.time_taken) as best_time,
                          COUNT(game_results.id) as attempts');
        $this->db->from('game_results');
        $this->db->join('students', 'students.id = game_results.student_id');
        $this->db->join('student_session', 'student_session.id = game_results.student_session_id');
        $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'left');
        $this->db->where('game_results.game_id', $game_id);
        $this->db->group_by('game_results.student_id');
        $this->db->order_by('best_score', 'DESC');
        $this->db->order_by('best_time', 'ASC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get overall leaderboard based on student points
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function getOverallLeaderboard($filters = array(), $limit = 10)
    {
        $this->db->select('student_points.*, students.firstname, students.lastname, students.admission_no,
                          classes.class, sections.section');
        $this->db->from('student_points');
        $this->db->join('students', 'students.id = student_points.student_id');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'left');

        // Apply filters
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }

        $this->db->order_by('student_points.total_points', 'DESC');
        $this->db->order_by('student_points.current_level', 'DESC');
        $this->db->order_by('student_points.games_completed', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get game performance analytics
     * @param int $game_id
     * @return array
     */
    public function getGameAnalytics($game_id)
    {
        $analytics = array();

        // Basic stats
        $this->db->select('COUNT(*) as total_attempts,
                          COUNT(DISTINCT student_id) as unique_players,
                          AVG(score) as average_score,
                          MAX(score) as highest_score,
                          MIN(score) as lowest_score,
                          AVG(time_taken) as average_time');
        $this->db->from('game_results');
        $this->db->where('game_id', $game_id);
        $query = $this->db->get();
        $analytics['basic_stats'] = $query->row_array();

        // Score distribution
        $this->db->select('CASE 
                          WHEN score >= 90 THEN "Excellent (90-100%)"
                          WHEN score >= 80 THEN "Good (80-89%)" 
                          WHEN score >= 70 THEN "Average (70-79%)"
                          WHEN score >= 60 THEN "Below Average (60-69%)"
                          ELSE "Poor (0-59%)"
                          END as score_range,
                          COUNT(*) as count');
        $this->db->from('game_results');
        $this->db->where('game_id', $game_id);
        $this->db->group_by('score_range');
        $this->db->order_by('MIN(score)', 'DESC');
        $query = $this->db->get();
        $analytics['score_distribution'] = $query->result_array();

        // Daily play stats (last 7 days)
        $this->db->select('DATE(completed_at) as play_date, COUNT(*) as play_count');
        $this->db->from('game_results');
        $this->db->where('game_id', $game_id);
        $this->db->where('completed_at >=', date('Y-m-d', strtotime('-7 days')));
        $this->db->group_by('DATE(completed_at)');
        $this->db->order_by('play_date', 'ASC');
        $query = $this->db->get();
        $analytics['daily_stats'] = $query->result_array();

        return $analytics;
    }

    /**
     * Delete game result
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Get result data before deletion for point adjustment
        $result = $this->get($id);
        
        if ($result) {
            $this->db->where('id', $id);
            $this->db->delete('game_results');
            
            // Adjust student points (subtract the points that were earned)
            $this->adjustStudentPoints($result['student_id'], $result['student_session_id'], -$result['points_earned']);
            
            $message = DELETE_RECORD_CONSTANT . " On game_results id " . $id;
            $action = "Delete";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Adjust student points (used for deletions or corrections)
     * @param int $student_id
     * @param int $student_session_id
     * @param int $point_adjustment
     */
    private function adjustStudentPoints($student_id, $student_session_id, $point_adjustment)
    {
        $this->db->select('*');
        $this->db->from('student_points');
        $this->db->where('student_id', $student_id);
        $this->db->where('student_session_id', $student_session_id);
        $query = $this->db->get();
        $student_points = $query->row_array();

        if ($student_points) {
            $new_total = max(0, $student_points['total_points'] + $point_adjustment);
            $new_level = $this->calculateLevel($new_total);
            $points_to_next = $this->getPointsToNextLevel($new_level, $new_total);
            
            $stats = $this->getStudentGameStats($student_id);
            
            $update_data = array(
                'total_points' => $new_total,
                'current_level' => $new_level,
                'points_to_next_level' => $points_to_next,
                'games_played' => $stats['games_played'],
                'games_completed' => $stats['games_completed'],
                'average_score' => $stats['average_score'],
                'best_score' => $stats['best_score'],
                'total_time_played' => $stats['total_time_played']
            );

            $this->db->where('id', $student_points['id']);
            $this->db->update('student_points', $update_data);
        }
    }
}