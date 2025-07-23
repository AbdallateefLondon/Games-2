# Educational Game System - Menu Integration SQL

-- This SQL adds the Educational Game system to the admin sidebar menu
-- Execute this after the main system installation

-- 1. Insert the main "Educational Games" menu group
INSERT INTO `sidebar_menus` (`id`, `name`, `lang_key`, `icon`, `url`, `activate_menu`, `short_code`, `sort_order`, `system`, `is_active`, `created_at`) 
VALUES (NULL, 'Educational Games', 'educational_games', 'fa fa-gamepad', '#', 'games', 'educational_games', 28, 0, 1, NOW());

-- Get the inserted menu ID
SET @games_menu_id = LAST_INSERT_ID();

-- 2. Insert submenu items for Educational Games
INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `name`, `lang_key`, `url`, `activate_controller`, `activate_methods`, `permission_key`, `sort_order`, `is_active`, `created_at`) VALUES
(NULL, @games_menu_id, 'Game Management', 'game_management', 'gamebuilder', 'gamebuilder', 'index,create,edit,delete', 'games_management', 1, 1, NOW()),
(NULL, @games_menu_id, 'Game Results', 'game_results', 'gamebuilder/results', 'gamebuilder', 'results', 'game_results', 2, 1, NOW()),
(NULL, @games_menu_id, 'Analytics Dashboard', 'game_analytics', 'gamebuilder/dashboard', 'gamebuilder', 'dashboard', 'games_management', 3, 1, NOW()),
(NULL, @games_menu_id, 'Leaderboard', 'game_leaderboard', 'gamebuilder/leaderboard', 'gamebuilder', 'leaderboard', 'game_leaderboard', 4, 1, NOW()),
(NULL, @games_menu_id, 'Setup System', 'game_setup', 'admin/gamesetup', 'gamesetup', 'index,install,uninstall', 'system_settings', 5, 1, NOW());

-- 3. Update language keys (if your system uses language files)
-- Add these to your language file manually:
-- $lang['educational_games'] = 'Educational Games';
-- $lang['game_management'] = 'Game Management';  
-- $lang['game_results'] = 'Game Results';
-- $lang['game_analytics'] = 'Analytics Dashboard';
-- $lang['game_leaderboard'] = 'Leaderboard';
-- $lang['game_setup'] = 'Setup System';

-- 4. Alternative method if your system uses the sidebar_list table structure
-- (Check your actual table names and structure)

-- INSERT INTO `sidebar_list` (`id`, `module`, `sidebar_link`, `lang_key`, `icon`, `level`, `sort`, `is_active`, `created_at`) VALUES
-- (NULL, 'educational_games', '', 'educational_games', 'fa fa-gamepad', 0, 28, 1, NOW());

-- SET @main_id = LAST_INSERT_ID();

-- INSERT INTO `sidebar_list` (`id`, `parent_id`, `module`, `sidebar_link`, `lang_key`, `icon`, `level`, `sort`, `is_active`, `created_at`) VALUES  
-- (NULL, @main_id, 'educational_games', 'gamebuilder', 'game_management', 'fa fa-angle-double-right', 1, 1, 1, NOW()),
-- (NULL, @main_id, 'educational_games', 'gamebuilder/results', 'game_results', 'fa fa-angle-double-right', 1, 2, 1, NOW()),
-- (NULL, @main_id, 'educational_games', 'gamebuilder/dashboard', 'game_analytics', 'fa fa-angle-double-right', 1, 3, 1, NOW()),
-- (NULL, @main_id, 'educational_games', 'gamebuilder/leaderboard', 'game_leaderboard', 'fa fa-angle-double-right', 1, 4, 1, NOW()),
-- (NULL, @main_id, 'educational_games', 'admin/gamesetup', 'game_setup', 'fa fa-angle-double-right', 1, 5, 1, NOW());

-- NOTE: The exact table structure may vary depending on your CodeIgniter school management system
-- Please check your database and adjust table/column names accordingly