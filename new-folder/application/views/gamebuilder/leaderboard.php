<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-trophy"></i> Game Leaderboard
            <small>Top performing students and gaming statistics</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/admin/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Leaderboard</li>
        </ol>
    </section>

    <section class="content">
        <!-- Filter Controls -->
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                    </div>
                    <div class="box-body">
                        <form method="get" action="<?php echo site_url('gamebuilder/leaderboard'); ?>" class="form-inline">
                            <div class="form-group">
                                <label>Class:</label>
                                <select name="class_id" class="form-control" onchange="getSections(this.value)">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $class) { ?>
                                        <option value="<?php echo $class['id']; ?>" 
                                            <?php echo set_select('class_id', $class['id'], ($this->input->get('class_id') == $class['id'])); ?>>
                                            <?php echo $class['class']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Section:</label>
                                <select name="section_id" id="section_id" class="form-control">
                                    <option value="">All Sections</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                            <a href="<?php echo site_url('gamebuilder/leaderboard'); ?>" class="btn btn-default">Reset</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3><?php echo $activity_summary['total_active_students']; ?></h3>
                        <p>Active Players</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo number_format($activity_summary['average_points'], 1); ?></h3>
                        <p>Average Points</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-star"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo $activity_summary['top_level']; ?></h3>
                        <p>Highest Level</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-trophy"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3><?php echo $activity_summary['recent_players']; ?></h3>
                        <p>Recent Players</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Leaderboard -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-trophy"></i> Top Students</h3>
                        <div class="box-tools pull-right">
                            <span class="label label-primary">
                                <?php echo count($leaderboard); ?> students
                            </span>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($leaderboard)) { ?>
                            <div class="leaderboard-container">
                                <?php foreach ($leaderboard as $index => $student) { ?>
                                    <div class="leaderboard-item <?php echo $index < 3 ? 'top-three' : ''; ?>" data-rank="<?php echo $student['rank']; ?>">
                                        <div class="rank-section">
                                            <div class="rank-number">
                                                <?php if ($index == 0) { ?>
                                                    <i class="fa fa-trophy" style="color: #FFD700; font-size: 24px;"></i>
                                                <?php } elseif ($index == 1) { ?>
                                                    <i class="fa fa-trophy" style="color: #C0C0C0; font-size: 22px;"></i>
                                                <?php } elseif ($index == 2) { ?>
                                                    <i class="fa fa-trophy" style="color: #CD7F32; font-size: 20px;"></i>
                                                <?php } else { ?>
                                                    <span class="rank-badge"><?php echo $student['rank']; ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="student-avatar">
                                            <div class="avatar-circle level-<?php echo min($student['current_level'], 10); ?>">
                                                <?php echo strtoupper(substr($student['firstname'], 0, 1)); ?>
                                            </div>
                                        </div>

                                        <div class="student-info">
                                            <h4 class="student-name">
                                                <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                                            </h4>
                                            <p class="student-details">
                                                <i class="fa fa-graduation-cap"></i> <?php echo $student['class'] . ' - ' . $student['section']; ?>
                                                <br>
                                                <i class="fa fa-id-card"></i> <?php echo $student['admission_no']; ?>
                                            </p>
                                        </div>

                                        <div class="student-stats">
                                            <div class="stat-item">
                                                <div class="stat-value level-badge-<?php echo min($student['current_level'], 10); ?>">
                                                    <?php echo $student['current_level']; ?>
                                                </div>
                                                <div class="stat-label">Level</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value points-badge">
                                                    <?php echo number_format($student['total_points']); ?>
                                                </div>
                                                <div class="stat-label">Points</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value games-badge">
                                                    <?php echo $student['games_completed']; ?>
                                                </div>
                                                <div class="stat-label">Games</div>
                                            </div>
                                        </div>

                                        <div class="progress-section">
                                            <div class="level-progress">
                                                <?php 
                                                $next_level_points = ($student['current_level'] + 1) * ($student['current_level'] + 1) * 2;
                                                $current_level_base = 0;
                                                for ($i = 1; $i < $student['current_level']; $i++) {
                                                    $current_level_base += $i * $i * 2;
                                                }
                                                $progress_in_level = $student['total_points'] - $current_level_base;
                                                $points_needed_for_level = $next_level_points;
                                                $progress_percent = $points_needed_for_level > 0 ? ($progress_in_level / $points_needed_for_level) * 100 : 0;
                                                ?>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar progress-bar-success" style="width: <?php echo min($progress_percent, 100); ?>%"></div>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo $student['points_to_next_level']; ?> points to level <?php echo $student['current_level'] + 1; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center text-muted">
                                <i class="fa fa-trophy fa-3x"></i><br><br>
                                <h4>No Students Found</h4>
                                <p>No students have played games yet with the current filters.</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Level Statistics Sidebar -->
            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Level Distribution</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($level_stats)) { ?>
                            <div class="level-stats">
                                <?php foreach ($level_stats as $stat) { ?>
                                    <div class="level-stat-item">
                                        <div class="level-info">
                                            <span class="level-badge-<?php echo min($stat['current_level'], 10); ?>">
                                                Level <?php echo $stat['current_level']; ?>
                                            </span>
                                            <span class="student-count"><?php echo $stat['student_count']; ?> students</span>
                                        </div>
                                        <div class="level-details">
                                            <small class="text-muted">
                                                Avg: <?php echo number_format($stat['avg_points'], 1); ?> points
                                                • Max: <?php echo number_format($stat['max_points']); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center text-muted">
                                <i class="fa fa-bar-chart fa-2x"></i><br><br>
                                <p>No level data available</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Level Requirements Info -->
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> Level System</h3>
                    </div>
                    <div class="box-body">
                        <div class="level-requirements">
                            <h5>How Leveling Works:</h5>
                            <ul class="level-list">
                                <li><strong>Level 1:</strong> 0-10 points</li>
                                <li><strong>Level 2:</strong> 11-18 points</li>
                                <li><strong>Level 3:</strong> 19-34 points</li>
                                <li><strong>Level 4:</strong> 35-66 points</li>
                                <li><strong>Level 5:</strong> 67-116 points</li>
                                <li class="text-muted">And so on...</li>
                            </ul>
                            <div class="alert alert-info alert-sm">
                                <i class="fa fa-lightbulb-o"></i>
                                <strong>Formula:</strong> Level N requires N² × 2 points to complete
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-list"></i> Quick Stats</h3>
                    </div>
                    <div class="box-body">
                        <div class="quick-stats">
                            <div class="stat-row">
                                <span class="stat-icon"><i class="fa fa-users text-blue"></i></span>
                                <span class="stat-text">Total Active Students</span>
                                <span class="stat-number"><?php echo $activity_summary['total_active_students']; ?></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-icon"><i class="fa fa-star text-yellow"></i></span>
                                <span class="stat-text">Average Points</span>
                                <span class="stat-number"><?php echo number_format($activity_summary['average_points'], 1); ?></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-icon"><i class="fa fa-trophy text-gold"></i></span>
                                <span class="stat-text">Highest Level</span>
                                <span class="stat-number"><?php echo $activity_summary['top_level']; ?></span>
                            </div>
                            <div class="stat-row">
                                <span class="stat-icon"><i class="fa fa-clock-o text-green"></i></span>
                                <span class="stat-text">Recent Players (7d)</span>
                                <span class="stat-number"><?php echo $activity_summary['recent_players']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function getSections(class_id) {
    if (class_id === '') {
        $('#section_id').html('<option value="">All Sections</option>');
        return;
    }
    
    $.post('<?php echo site_url("gamebuilder/get_sections"); ?>', {
        class_id: class_id
    }, function(data) {
        const sections = JSON.parse(data);
        let options = '<option value="">All Sections</option>';
        
        $.each(sections, function(index, section) {
            options += '<option value="' + section.section_id + '">' + section.section + '</option>';
        });
        
        $('#section_id').html(options);
    });
}

// Load sections on page load if class is selected
$(document).ready(function() {
    var selected_class = $('select[name="class_id"]').val();
    if (selected_class) {
        getSections(selected_class);
        setTimeout(function() {
            $('#section_id').val('<?php echo $this->input->get('section_id'); ?>');
        }, 500);
    }
});
</script>

<style>
.leaderboard-container {
    padding: 10px 0;
}

.leaderboard-item {
    display: flex;
    align-items: center;
    padding: 20px;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.leaderboard-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.leaderboard-item.top-three {
    background: linear-gradient(135deg, #fff5e6 0%, #ffecd1 100%);
    border: 2px solid #f39c12;
}

.rank-section {
    width: 60px;
    text-align: center;
    margin-right: 20px;
}

.rank-badge {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    background: #6c757d;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 16px;
}

.student-avatar {
    margin-right: 20px;
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.avatar-circle.level-1, .avatar-circle.level-2 { background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); }
.avatar-circle.level-3, .avatar-circle.level-4 { background: linear-gradient(135deg, #00b894 0%, #00a085 100%); }
.avatar-circle.level-5, .avatar-circle.level-6 { background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%); }
.avatar-circle.level-7, .avatar-circle.level-8 { background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%); }
.avatar-circle.level-9, .avatar-circle.level-10 { background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%); }

.student-info {
    flex: 1;
    margin-right: 20px;
}

.student-name {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: bold;
    color: #2c3e50;
}

.student-details {
    margin: 0;
    color: #7f8c8d;
    line-height: 1.4;
}

.student-stats {
    display: flex;
    gap: 20px;
    margin-right: 20px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
}

.level-badge-1, .level-badge-2 { color: #3498db; }
.level-badge-3, .level-badge-4 { color: #2ecc71; }
.level-badge-5, .level-badge-6 { color: #f39c12; }
.level-badge-7, .level-badge-8 { color: #e74c3c; }
.level-badge-9, .level-badge-10 { color: #9b59b6; }

.points-badge { color: #f39c12; }
.games-badge { color: #2ecc71; }

.progress-section {
    width: 150px;
}

.level-progress small {
    font-size: 11px;
}

.level-stats {
    padding: 10px 0;
}

.level-stat-item {
    padding: 12px 0;
    border-bottom: 1px solid #ecf0f1;
}

.level-stat-item:last-child {
    border-bottom: none;
}

.level-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.student-count {
    font-weight: bold;
    color: #2c3e50;
}

.level-requirements .level-list {
    padding-left: 20px;
    margin-bottom: 15px;
}

.level-requirements .level-list li {
    margin-bottom: 5px;
}

.alert-sm {
    padding: 8px 12px;
    font-size: 12px;
}

.quick-stats .stat-row {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #ecf0f1;
}

.quick-stats .stat-row:last-child {
    border-bottom: none;
}

.quick-stats .stat-icon {
    width: 30px;
    text-align: center;
}

.quick-stats .stat-text {
    flex: 1;
    margin-left: 10px;
    font-size: 13px;
}

.quick-stats .stat-number {
    font-weight: bold;
    color: #2c3e50;
}

.text-gold {
    color: #f39c12 !important;
}
</style>