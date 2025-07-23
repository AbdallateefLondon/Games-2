<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentpoint_model extends MY_Model
{
    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get student points with optional filters
     * @param int $id
     * @param array $filters
     * @return mixed
     */
    public function get($id = null, $filters = array())
    {
        $this->db->select('student_points.*, students.firstname, students.lastname, students.admission_no,
                          classes.class, sections.section');
        $this->db->from('student_points');
        $this->db->join('students', 'students.id = student_points.student_id');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'left');

        if ($id != null) {
            $this->db->where('student_points.id', $id);
        }

        // Apply filters
        if (isset($filters['student_id']) && $filters['student_id'] !== '') {
            $this->db->where('student_points.student_id', $filters['student_id']);
        }
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }
        if (isset($filters['level']) && $filters['level'] !== '') {
            $this->db->where('student_points.current_level', $filters['level']);
        }

        $this->db->order_by('student_points.total_points', 'DESC');

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get student points by student ID
     * @param int $student_id
     * @return array|null
     */
    public function getByStudentId($student_id)
    {
        $this->db->select('student_points.*, students.firstname, students.lastname, students.admission_no');
        $this->db->from('student_points');
        $this->db->join('students', 'students.id = student_points.student_id');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        $this->db->where('student_points.student_id', $student_id);
        $this->db->where('student_session.session_id', $this->current_session);

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get leaderboard with ranking
     * @param array $filters
     * @param int $limit
     * @param int $student_id (to get specific student's rank)
     * @return array
     */
    public function getLeaderboard($filters = array(), $limit = 10, $student_id = null)
    {
        // First, get all students with rankings
        $this->db->select('student_points.*, students.firstname, students.lastname, students.admission_no,
                          classes.class, sections.section,
                          @rank := @rank + 1 as rank');
        $this->db->from('student_points, (SELECT @rank := 0) r');
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

        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->order_by('student_points.total_points', 'DESC');
        $this->db->order_by('student_points.current_level', 'DESC');
        $this->db->order_by('student_points.games_completed', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit);
        }

        $query = $this->db->get();
        $leaderboard = $query->result_array();

        // If specific student requested, find their rank if not in top results
        if ($student_id && $limit > 0) {
            $student_found = false;
            foreach ($leaderboard as $entry) {
                if ($entry['student_id'] == $student_id) {
                    $student_found = true;
                    break;
                }
            }

            if (!$student_found) {
                $student_rank = $this->getStudentRank($student_id, $filters);
                if ($student_rank) {
                    $leaderboard[] = $student_rank;
                }
            }
        }

        return $leaderboard;
    }

    /**
     * Get specific student's rank
     * @param int $student_id
     * @param array $filters
     * @return array|null
     */
    public function getStudentRank($student_id, $filters = array())
    {
        // Get student's points
        $student_points = $this->getByStudentId($student_id);
        if (!$student_points) {
            return null;
        }

        // Count how many students have higher points
        $this->db->select('COUNT(*) + 1 as rank');
        $this->db->from('student_points');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        
        // Apply same filters as leaderboard
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }

        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('student_points.total_points >', $student_points['total_points']);

        $query = $this->db->get();
        $rank_result = $query->row_array();

        // Combine student data with rank
        $student_points['rank'] = $rank_result['rank'];
        return $student_points;
    }

    /**
     * Get level statistics
     * @param array $filters
     * @return array
     */
    public function getLevelStats($filters = array())
    {
        $this->db->select('current_level, COUNT(*) as student_count, 
                          AVG(total_points) as avg_points,
                          MAX(total_points) as max_points,
                          MIN(total_points) as min_points');
        $this->db->from('student_points');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');

        // Apply filters
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }

        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->group_by('current_level');
        $this->db->order_by('current_level', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get achievement badges based on student performance
     * @param int $student_id
     * @return array
     */
    public function getStudentAchievements($student_id)
    {
        $student_points = $this->getByStudentId($student_id);
        if (!$student_points) {
            return array();
        }

        $achievements = array();

        // Level-based achievements
        if ($student_points['current_level'] >= 5) {
            $achievements[] = array(
                'name' => 'Level Master',
                'description' => 'Reached Level 5',
                'icon' => 'fa-crown',
                'color' => 'gold'
            );
        }
        if ($student_points['current_level'] >= 10) {
            $achievements[] = array(
                'name' => 'Gaming Expert',
                'description' => 'Reached Level 10',
                'icon' => 'fa-trophy',
                'color' => 'gold'
            );
        }

        // Points-based achievements
        if ($student_points['total_points'] >= 100) {
            $achievements[] = array(
                'name' => 'Century Scorer',
                'description' => 'Earned 100+ points',
                'icon' => 'fa-star',
                'color' => 'blue'
            );
        }
        if ($student_points['total_points'] >= 500) {
            $achievements[] = array(
                'name' => 'Point Champion',
                'description' => 'Earned 500+ points',
                'icon' => 'fa-medal',
                'color' => 'purple'
            );
        }

        // Game completion achievements
        if ($student_points['games_completed'] >= 10) {
            $achievements[] = array(
                'name' => 'Game Finisher',
                'description' => 'Completed 10+ games',
                'icon' => 'fa-check-circle',
                'color' => 'green'
            );
        }
        if ($student_points['games_completed'] >= 25) {
            $achievements[] = array(
                'name' => 'Dedicated Player',
                'description' => 'Completed 25+ games',
                'icon' => 'fa-gem',
                'color' => 'red'
            );
        }

        // Performance achievements
        if ($student_points['average_score'] >= 90) {
            $achievements[] = array(
                'name' => 'Excellence Award',
                'description' => 'Average score 90%+',
                'icon' => 'fa-award',
                'color' => 'gold'
            );
        }
        if ($student_points['best_score'] == 100) {
            $achievements[] = array(
                'name' => 'Perfect Score',
                'description' => 'Achieved 100% score',
                'icon' => 'fa-bullseye',
                'color' => 'gold'
            );
        }

        return $achievements;
    }

    /**
     * Get points progression data for charts
     * @param int $student_id
     * @param int $days
     * @return array
     */
    public function getPointsProgression($student_id, $days = 30)
    {
        $this->db->select('DATE(game_results.completed_at) as play_date, 
                          SUM(game_results.points_earned) as daily_points');
        $this->db->from('game_results');
        $this->db->where('game_results.student_id', $student_id);
        $this->db->where('game_results.completed_at >=', date('Y-m-d', strtotime("-{$days} days")));
        $this->db->group_by('DATE(game_results.completed_at)');
        $this->db->order_by('play_date', 'ASC');

        $query = $this->db->get();
        $progression = $query->result_array();

        // Fill in missing dates with 0 points
        $result = array();
        $current_date = date('Y-m-d', strtotime("-{$days} days"));
        $end_date = date('Y-m-d');

        while ($current_date <= $end_date) {
            $found = false;
            foreach ($progression as $day) {
                if ($day['play_date'] == $current_date) {
                    $result[] = $day;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $result[] = array(
                    'play_date' => $current_date,
                    'daily_points' => 0
                );
            }
            $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
        }

        return $result;
    }

    /**
     * Calculate level requirements
     * @param int $level
     * @return array
     */
    public function getLevelRequirements($level)
    {
        $points_required = 0;
        for ($i = 1; $i <= $level; $i++) {
            $points_required += ($i * $i * 2);
        }

        $next_level_points = $points_required + (($level + 1) * ($level + 1) * 2);

        return array(
            'level' => $level,
            'points_required' => $points_required,
            'next_level_points' => $next_level_points,
            'points_for_next' => (($level + 1) * ($level + 1) * 2)
        );
    }

    /**
     * Get all level requirements up to a certain level
     * @param int $max_level
     * @return array
     */
    public function getAllLevelRequirements($max_level = 20)
    {
        $levels = array();
        $cumulative_points = 0;

        for ($level = 1; $level <= $max_level; $level++) {
            $points_for_level = $level * $level * 2;
            if ($level > 1) {
                $cumulative_points += $points_for_level;
            } else {
                $cumulative_points = 10; // Level 1 requires 10 points
            }

            $levels[] = array(
                'level' => $level,
                'points_required' => $cumulative_points,
                'points_for_this_level' => $points_for_level
            );
        }

        return $levels;
    }

    /**
     * Update student achievements
     * @param int $student_id
     * @param array $achievements
     * @return bool
     */
    public function updateAchievements($student_id, $achievements)
    {
        $this->db->where('student_id', $student_id);
        $this->db->update('student_points', array(
            'achievements' => json_encode($achievements),
            'updated_at' => date('Y-m-d H:i:s')
        ));

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get gaming activity summary
     * @param array $filters
     * @return array
     */
    public function getActivitySummary($filters = array())
    {
        $summary = array();

        // Total active students
        $this->db->select('COUNT(*) as total_active_students');
        $this->db->from('student_points');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }
        
        $this->db->where('student_session.session_id', $this->current_session);
        $query = $this->db->get();
        $summary['total_active_students'] = $query->row()->total_active_students;

        // Average points per student
        $this->db->select('AVG(total_points) as avg_points');
        $this->db->from('student_points');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }
        
        $this->db->where('student_session.session_id', $this->current_session);
        $query = $this->db->get();
        $summary['average_points'] = round($query->row()->avg_points, 2);

        // Top level achieved
        $this->db->select('MAX(current_level) as top_level');
        $this->db->from('student_points');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }
        
        $this->db->where('student_session.session_id', $this->current_session);
        $query = $this->db->get();
        $summary['top_level'] = $query->row()->top_level;

        // Students who played in last 7 days
        $this->db->select('COUNT(*) as recent_players');
        $this->db->from('student_points');
        $this->db->join('student_session', 'student_session.id = student_points.student_session_id');
        
        if (isset($filters['class_id']) && $filters['class_id'] !== '') {
            $this->db->where('student_session.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && $filters['section_id'] !== '') {
            $this->db->where('student_session.section_id', $filters['section_id']);
        }
        
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('student_points.last_played >=', date('Y-m-d', strtotime('-7 days')));
        $query = $this->db->get();
        $summary['recent_players'] = $query->row()->recent_players;

        return $summary;
    }
}