<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Educational Game System Setup Controller
 * Use this controller to install/setup the game system database
 * Access via: /admin/gamesetup
 */
class Gamesetup extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Setup page - displays installation status and options
     */
    public function index()
    {
        // Check if user has permission (only super admin should access this)
        if ($this->session->userdata('admin')['role_id'] != 7) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'system');
        $this->session->set_userdata('sub_menu', 'gamesetup');

        $data['title'] = 'Educational Game System Setup';
        $data['setup_status'] = $this->check_setup_status();

        $this->load->view('layout/header', $data);
        $this->load->view('gamebuilder/setup', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Execute the database setup
     */
    public function install()
    {
        // Check if user has permission (only super admin should access this)
        if ($this->session->userdata('admin')['role_id'] != 7) {
            access_denied();
        }

        if ($this->input->post('confirm_install')) {
            $results = $this->execute_setup();
            
            $data['title'] = 'Setup Results';
            $data['results'] = $results;
            
            $this->load->view('layout/header', $data);
            $this->load->view('gamebuilder/setup_results', $data);
            $this->load->view('layout/footer', $data);
        } else {
            redirect('admin/gamesetup');
        }
    }

    /**
     * Check current setup status
     */
    private function check_setup_status()
    {
        $status = array(
            'tables_exist' => false,
            'permissions_exist' => false,
            'missing_tables' => array(),
            'setup_complete' => false
        );

        // Check if tables exist
        $required_tables = array('educational_games', 'game_results', 'student_points');
        $missing_tables = array();

        foreach ($required_tables as $table) {
            if (!$this->db->table_exists($table)) {
                $missing_tables[] = $table;
            }
        }

        $status['missing_tables'] = $missing_tables;
        $status['tables_exist'] = empty($missing_tables);

        // Check if permissions exist
        $this->db->where_in('short_code', array('game_builder', 'student_games'));
        $permission_count = $this->db->count_all_results('permission_group');
        $status['permissions_exist'] = ($permission_count >= 2);

        $status['setup_complete'] = $status['tables_exist'] && $status['permissions_exist'];

        return $status;
    }

    /**
     * Execute the database setup
     */
    private function execute_setup()
    {
        $results = array(
            'success' => 0,
            'errors' => 0,
            'messages' => array(),
            'overall_success' => false
        );

        // Start transaction
        $this->db->trans_start();

        try {
            // 1. Create educational_games table
            $sql = "CREATE TABLE IF NOT EXISTS `educational_games` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `description` text,
                `game_type` enum('quiz','matching') NOT NULL,
                `game_content` longtext NOT NULL COMMENT 'JSON structure containing game data',
                `class_id` int(11) DEFAULT NULL,
                `section_id` int(11) DEFAULT NULL,
                `subject_id` int(11) DEFAULT NULL,
                `created_by` int(11) NOT NULL COMMENT 'Staff ID who created the game',
                `max_attempts` int(11) DEFAULT 3,
                `time_limit` int(11) DEFAULT NULL COMMENT 'Time limit in minutes, NULL for no limit',
                `points_per_question` int(11) DEFAULT 10,
                `is_active` tinyint(1) DEFAULT 1,
                `difficulty_level` enum('easy','medium','hard') DEFAULT 'medium',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_class_section` (`class_id`,`section_id`),
                KEY `idx_created_by` (`created_by`),
                KEY `idx_game_type` (`game_type`),
                KEY `idx_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            if ($this->db->query($sql)) {
                $results['success']++;
                $results['messages'][] = "✓ Created educational_games table";
            } else {
                $results['errors']++;
                $results['messages'][] = "✗ Failed to create educational_games table";
            }

            // 2. Create game_results table
            $sql = "CREATE TABLE IF NOT EXISTS `game_results` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `game_id` int(11) NOT NULL,
                `student_id` int(11) NOT NULL,
                `student_session_id` int(11) NOT NULL,
                `score` int(11) NOT NULL DEFAULT 0,
                `total_questions` int(11) NOT NULL,
                `correct_answers` int(11) NOT NULL DEFAULT 0,
                `time_taken` int(11) DEFAULT NULL COMMENT 'Time taken in seconds',
                `points_earned` int(11) NOT NULL DEFAULT 0,
                `attempt_number` int(11) NOT NULL DEFAULT 1,
                `game_data` text COMMENT 'JSON data of student answers and performance',
                `completed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_game_student` (`game_id`,`student_id`),
                KEY `idx_student_session` (`student_session_id`),
                KEY `idx_completed_at` (`completed_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            if ($this->db->query($sql)) {
                $results['success']++;
                $results['messages'][] = "✓ Created game_results table";
            } else {
                $results['errors']++;
                $results['messages'][] = "✗ Failed to create game_results table";
            }

            // 3. Create student_points table
            $sql = "CREATE TABLE IF NOT EXISTS `student_points` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `student_id` int(11) NOT NULL,
                `student_session_id` int(11) NOT NULL,
                `total_points` int(11) NOT NULL DEFAULT 0,
                `current_level` int(11) NOT NULL DEFAULT 1,
                `points_to_next_level` int(11) NOT NULL DEFAULT 10,
                `games_played` int(11) NOT NULL DEFAULT 0,
                `games_completed` int(11) NOT NULL DEFAULT 0,
                `average_score` decimal(5,2) DEFAULT 0.00,
                `best_score` int(11) DEFAULT 0,
                `total_time_played` int(11) DEFAULT 0 COMMENT 'Total time in seconds',
                `achievements` text COMMENT 'JSON array of earned achievements',
                `last_played` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_student_session` (`student_id`,`student_session_id`),
                KEY `idx_total_points` (`total_points`),
                KEY `idx_current_level` (`current_level`),
                KEY `idx_last_played` (`last_played`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

            if ($this->db->query($sql)) {
                $results['success']++;
                $results['messages'][] = "✓ Created student_points table";
            } else {
                $results['errors']++;
                $results['messages'][] = "✗ Failed to create student_points table";
            }

            // 4. Add Game Builder permission group
            $this->db->where('short_code', 'game_builder');
            $existing = $this->db->get('permission_group');
            
            if ($existing->num_rows() == 0) {
                $permission_group_data = array(
                    'name' => 'Game Builder',
                    'short_code' => 'game_builder',
                    'system' => 0,
                    'sort_order' => 100,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                if ($this->db->insert('permission_group', $permission_group_data)) {
                    $game_group_id = $this->db->insert_id();
                    $results['success']++;
                    $results['messages'][] = "✓ Added Game Builder permission group (ID: $game_group_id)";
                    
                    // Add permission categories for game builder
                    $categories = array(
                        array(
                            'perm_group_id' => $game_group_id,
                            'name' => 'Games Management',
                            'short_code' => 'games_management',
                            'enable_view' => 1,
                            'enable_add' => 1,
                            'enable_edit' => 1,
                            'enable_delete' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        ),
                        array(
                            'perm_group_id' => $game_group_id,
                            'name' => 'Game Results',
                            'short_code' => 'game_results',
                            'enable_view' => 1,
                            'enable_add' => 0,
                            'enable_edit' => 0,
                            'enable_delete' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ),
                        array(
                            'perm_group_id' => $game_group_id,
                            'name' => 'Student Gaming',
                            'short_code' => 'student_gaming',
                            'enable_view' => 1,
                            'enable_add' => 0,
                            'enable_edit' => 0,
                            'enable_delete' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        )
                    );
                    
                    if ($this->db->insert_batch('permission_category', $categories)) {
                        $results['success']++;
                        $results['messages'][] = "✓ Added Game Builder permission categories";
                        
                        // Grant all permissions to Super Admin (role_id = 7) automatically
                        $this->grantSuperAdminPermissions($game_group_id);
                        $results['messages'][] = "✓ Granted Game Builder permissions to Super Admin";
                    }
                } else {
                    $results['errors']++;
                    $results['messages'][] = "✗ Failed to add Game Builder permission group";
                }
            } else {
                $results['messages'][] = "- Game Builder permission group already exists";
                
                // Still grant Super Admin permissions if they don't exist
                $existing_group = $existing->row();
                $this->grantSuperAdminPermissions($existing_group->id);
                $results['messages'][] = "✓ Verified Super Admin Game Builder permissions";
            }

            // 5. Add Student Games permission group
            $this->db->where('short_code', 'student_games');
            $existing = $this->db->get('permission_group');
            
            if ($existing->num_rows() == 0) {
                $permission_group_data = array(
                    'name' => 'Student Games',
                    'short_code' => 'student_games',
                    'system' => 0,
                    'sort_order' => 101,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                if ($this->db->insert('permission_group', $permission_group_data)) {
                    $student_game_group_id = $this->db->insert_id();
                    $results['success']++;
                    $results['messages'][] = "✓ Added Student Games permission group (ID: $student_game_group_id)";
                    
                    // Add permission categories for student games
                    $categories = array(
                        array(
                            'perm_group_id' => $student_game_group_id,
                            'name' => 'Play Games',
                            'short_code' => 'play_games',
                            'enable_view' => 1,
                            'enable_add' => 0,
                            'enable_edit' => 0,
                            'enable_delete' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ),
                        array(
                            'perm_group_id' => $student_game_group_id,
                            'name' => 'View Results',
                            'short_code' => 'view_game_results',
                            'enable_view' => 1,
                            'enable_add' => 0,
                            'enable_edit' => 0,
                            'enable_delete' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ),
                        array(
                            'perm_group_id' => $student_game_group_id,
                            'name' => 'Leaderboard',
                            'short_code' => 'game_leaderboard',
                            'enable_view' => 1,
                            'enable_add' => 0,
                            'enable_edit' => 0,
                            'enable_delete' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        )
                    );
                    
                    if ($this->db->insert_batch('permission_category', $categories)) {
                        $results['success']++;
                        $results['messages'][] = "✓ Added Student Games permission categories";
                    }
                } else {
                    $results['errors']++;
                    $results['messages'][] = "✗ Failed to add Student Games permission group";
                }
            } else {
                $results['messages'][] = "- Student Games permission group already exists";
            }

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $results['overall_success'] = false;
                $results['messages'][] = "✗ Transaction failed - changes rolled back";
            } else {
                $results['overall_success'] = true;
                $results['messages'][] = "✓ Setup completed successfully!";
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $results['errors']++;
            $results['overall_success'] = false;
            $results['messages'][] = "✗ Exception: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Add menu items to admin navigation
     */
    public function add_menu_items()
    {
        // Check if user has permission (only super admin should access this)
        if ($this->session->userdata('admin')['role_id'] != 7) {
            access_denied();
        }

        $this->db->trans_start();
        
        $results = array(
            'success' => 0,
            'errors' => 0,
            'messages' => array()
        );

        try {
            // Check what menu table structure this system uses
            $tables = $this->db->list_tables();
            
            if (in_array('sidebar_menus', $tables)) {
                // Method 1: Standard sidebar_menus structure
                $this->addStandardMenus($results);
            } elseif (in_array('sidebar_list', $tables)) {
                // Method 2: Alternative sidebar_list structure  
                $this->addAlternativeMenus($results);
            } else {
                $results['errors']++;
                $results['messages'][] = "✗ Could not find compatible menu table structure";
            }

        } catch (Exception $e) {
            $results['errors']++;
            $results['messages'][] = "✗ Error adding menus: " . $e->getMessage();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $results['overall_success'] = false;
        } else {
            $results['overall_success'] = true;
        }

        // Display results
        $data['title'] = 'Menu Integration Results';
        $data['results'] = $results;
        
        $this->load->view('layout/header', $data);
        $this->load->view('gamebuilder/setup_results', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Add menus using standard structure
     */
    private function addStandardMenus(&$results)
    {
        // Check if main menu already exists
        $this->db->where('short_code', 'educational_games');
        $existing = $this->db->get('sidebar_menus');
        
        if ($existing->num_rows() == 0) {
            // Insert main menu
            $menu_data = array(
                'name' => 'Educational Games',
                'lang_key' => 'educational_games', 
                'icon' => 'fa fa-gamepad',
                'url' => '#',
                'activate_menu' => 'games',
                'short_code' => 'educational_games',
                'sort_order' => 28,
                'system' => 0,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            if ($this->db->insert('sidebar_menus', $menu_data)) {
                $menu_id = $this->db->insert_id();
                $results['success']++;
                $results['messages'][] = "✓ Added Educational Games main menu";
                
                // Add submenus
                $submenus = array(
                    array('name' => 'Game Management', 'lang_key' => 'game_management', 'url' => 'gamebuilder', 'controller' => 'gamebuilder', 'methods' => 'index,create,edit,delete', 'permission' => 'games_management', 'sort' => 1),
                    array('name' => 'Game Results', 'lang_key' => 'game_results', 'url' => 'gamebuilder/results', 'controller' => 'gamebuilder', 'methods' => 'results', 'permission' => 'game_results', 'sort' => 2),
                    array('name' => 'Analytics Dashboard', 'lang_key' => 'game_analytics', 'url' => 'gamebuilder/dashboard', 'controller' => 'gamebuilder', 'methods' => 'dashboard', 'permission' => 'games_management', 'sort' => 3),
                    array('name' => 'Leaderboard', 'lang_key' => 'game_leaderboard', 'url' => 'gamebuilder/leaderboard', 'controller' => 'gamebuilder', 'methods' => 'leaderboard', 'permission' => 'game_leaderboard', 'sort' => 4)
                );
                
                foreach ($submenus as $submenu) {
                    $submenu_data = array(
                        'sidebar_menu_id' => $menu_id,
                        'name' => $submenu['name'],
                        'lang_key' => $submenu['lang_key'],
                        'url' => $submenu['url'],
                        'activate_controller' => $submenu['controller'],
                        'activate_methods' => $submenu['methods'],
                        'permission_key' => $submenu['permission'],
                        'sort_order' => $submenu['sort'],
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    
                    if ($this->db->insert('sidebar_sub_menus', $submenu_data)) {
                        $results['success']++;
                        $results['messages'][] = "✓ Added " . $submenu['name'] . " submenu";
                    } else {
                        $results['errors']++;
                        $results['messages'][] = "✗ Failed to add " . $submenu['name'] . " submenu";
                    }
                }
            } else {
                $results['errors']++;
                $results['messages'][] = "✗ Failed to add main Educational Games menu";
            }
        } else {
            $results['messages'][] = "- Educational Games menu already exists";
        }
    }

    /**
     * Add menus using alternative structure
     */
    private function addAlternativeMenus(&$results)
    {
        // Alternative method for different menu structures
        $results['messages'][] = "- Using alternative menu structure (manual setup may be required)";
        
        // You would implement this based on your specific menu table structure
        // This is a placeholder for systems that use different menu organization
    }
    }

    /**
     * Reset/uninstall the game system (for development purposes)
     */
    public function uninstall()
    {
        // Check if user has permission (only super admin should access this)
        if ($this->session->userdata('admin')['role_id'] != 7) {
            access_denied();
        }

        if ($this->input->post('confirm_uninstall')) {
            $this->db->trans_start();

            // Remove tables
            $this->db->query("DROP TABLE IF EXISTS game_results");
            $this->db->query("DROP TABLE IF EXISTS student_points");  
            $this->db->query("DROP TABLE IF EXISTS educational_games");

            // Remove permission categories
            $this->db->where_in('short_code', array('games_management', 'game_results', 'student_gaming', 'play_games', 'view_game_results', 'game_leaderboard'));
            $this->db->delete('permission_category');

            // Remove permission groups
            $this->db->where_in('short_code', array('game_builder', 'student_games'));
            $this->db->delete('permission_group');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">Uninstall failed!</div>');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-success">Game system uninstalled successfully!</div>');
            }
        }

        redirect('admin/gamesetup');
    }

    /**
     * Grant Super Admin permissions automatically
     */
    private function grantSuperAdminPermissions($permission_group_id)
    {
        // Get all permission categories for this group
        $this->db->where('perm_group_id', $permission_group_id);
        $categories = $this->db->get('permission_category')->result();
        
        foreach ($categories as $category) {
            // Check if Super Admin already has this permission
            $this->db->where('role_id', 7); // Super Admin role ID
            $this->db->where('perm_cat_id', $category->id);
            $existing = $this->db->get('roles_permissions');
            
            if ($existing->num_rows() == 0) {
                // Grant full permissions to Super Admin
                $permission_data = array(
                    'role_id' => 7,
                    'perm_cat_id' => $category->id,
                    'can_view' => 1,
                    'can_add' => $category->enable_add,
                    'can_edit' => $category->enable_edit,
                    'can_delete' => $category->enable_delete,
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                $this->db->insert('roles_permissions', $permission_data);
            }
        }
    }
}