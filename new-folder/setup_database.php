<?php
/**
 * Educational Game System Database Setup Script
 * Execute this file once to set up the required tables and permissions
 */

// Database configuration (from existing config)
$config = array(
    'hostname' => 'localhost',
    'username' => 'root', 
    'password' => 'Abdo0968890946',
    'database' => 'smartschool'
);

try {
    $mysqli = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "Connected to database successfully.\n";
    
    // Read and execute SQL setup
    $sql_file = dirname(__FILE__) . '/database_setup.sql';
    $sql_content = file_get_contents($sql_file);
    
    // Split SQL commands
    $sql_commands = explode(';', $sql_content);
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($sql_commands as $command) {
        $command = trim($command);
        if (empty($command) || substr($command, 0, 2) == '--') {
            continue;
        }
        
        if ($mysqli->query($command)) {
            $success_count++;
            echo "✓ Executed: " . substr($command, 0, 50) . "...\n";
        } else {
            $error_count++;
            echo "✗ Error: " . $mysqli->error . "\n";
            echo "Command: " . substr($command, 0, 100) . "...\n";
        }
    }
    
    echo "\n=== Database Setup Complete ===\n";
    echo "Successful commands: $success_count\n";
    echo "Failed commands: $error_count\n";
    
    // Verify tables were created
    echo "\n=== Verifying Tables ===\n";
    $tables_to_check = ['educational_games', 'game_results', 'student_points'];
    
    foreach ($tables_to_check as $table) {
        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' missing\n";
        }
    }
    
    // Verify permissions were added
    echo "\n=== Verifying Permissions ===\n";
    $result = $mysqli->query("SELECT * FROM permission_group WHERE short_code IN ('game_builder', 'student_games')");
    if ($result && $result->num_rows >= 2) {
        echo "✓ Game permissions added successfully\n";
        while ($row = $result->fetch_assoc()) {
            echo "  - " . $row['name'] . " (" . $row['short_code'] . ")\n";
        }
    } else {
        echo "✗ Game permissions may not have been added correctly\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>