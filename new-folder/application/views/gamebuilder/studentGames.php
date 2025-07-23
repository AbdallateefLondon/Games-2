<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-gamepad"></i> Educational Games
            <small>Play games and earn points to level up!</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>user/user/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Games</li>
        </ol>
    </section>

    <section class="content">
        <!-- Student Progress Overview -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3><?php echo $student_points ? $student_points['current_level'] : 1; ?></h3>
                        <p>Current Level</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-trophy"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3><?php echo $student_points ? $student_points['total_points'] : 0; ?></h3>
                        <p>Total Points</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-star"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo $student_points ? $student_points['games_completed'] : 0; ?></h3>
                        <p>Games Completed</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo $student_points && $student_points['average_score'] ? number_format($student_points['average_score'], 1) : 0; ?>%</h3>
                        <p>Average Score</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level Progress Bar -->
        <?php if ($student_points) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-arrow-up"></i> Level Progress</h3>
                        </div>
                        <div class="box-body">
                            <?php 
                            $progress_percent = $student_points['points_to_next_level'] > 0 ? 
                                ((($student_points['current_level'] + 1) * ($student_points['current_level'] + 1) * 2) - $student_points['points_to_next_level']) / 
                                (($student_points['current_level'] + 1) * ($student_points['current_level'] + 1) * 2) * 100 : 100;
                            ?>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $progress_percent; ?>%">
                                    Level <?php echo $student_points['current_level']; ?>
                                </div>
                            </div>
                            <p class="text-center">
                                <strong><?php echo $student_points['points_to_next_level']; ?> points</strong> 
                                needed to reach Level <?php echo $student_points['current_level'] + 1; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <!-- Available Games -->
            <div class="col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-gamepad"></i> Available Games</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($gameList)) { ?>
                            <div class="row">
                                <?php foreach ($gameList as $game) { ?>
                                    <div class="col-md-6">
                                        <div class="panel panel-<?php echo $game['game_type'] == 'quiz' ? 'primary' : 'info'; ?>">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <i class="fa fa-<?php echo $game['game_type'] == 'quiz' ? 'question-circle' : 'puzzle-piece'; ?>"></i>
                                                    <?php echo htmlspecialchars($game['title']); ?>
                                                    <span class="pull-right">
                                                        <span class="label label-<?php 
                                                            echo $game['difficulty_level'] == 'easy' ? 'success' : 
                                                                 ($game['difficulty_level'] == 'medium' ? 'warning' : 'danger'); 
                                                        ?>">
                                                            <?php echo ucfirst($game['difficulty_level']); ?>
                                                        </span>
                                                    </span>
                                                </h4>
                                            </div>
                                            <div class="panel-body">
                                                <?php if ($game['description']) { ?>
                                                    <p class="text-muted"><?php echo htmlspecialchars($game['description']); ?></p>
                                                <?php } ?>
                                                
                                                <div class="game-info">
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <small><i class="fa fa-users"></i> 
                                                                <?php echo $game['class'] ? $game['class'] . ($game['section'] ? ' (' . $game['section'] . ')' : '') : 'All Classes'; ?>
                                                            </small>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <small><i class="fa fa-book"></i> <?php echo $game['subject_name'] ?: 'General'; ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-top: 10px;">
                                                        <div class="col-xs-6">
                                                            <small><i class="fa fa-repeat"></i> 
                                                                Attempts: <?php echo $game['attempts_made']; ?>/<?php echo $game['max_attempts']; ?>
                                                            </small>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <?php if ($game['time_limit']) { ?>
                                                                <small><i class="fa fa-clock-o"></i> <?php echo $game['time_limit']; ?> min</small>
                                                            <?php } else { ?>
                                                                <small><i class="fa fa-clock-o"></i> No time limit</small>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center" style="margin-top: 15px;">
                                                    <?php if ($game['attempts_made'] >= $game['max_attempts']) { ?>
                                                        <button class="btn btn-default btn-sm" disabled>
                                                            <i class="fa fa-ban"></i> Max Attempts Reached
                                                        </button>
                                                    <?php } else { ?>
                                                        <a href="<?php echo site_url('gamebuilder/play/' . $game['id']); ?>" 
                                                           class="btn btn-<?php echo $game['game_type'] == 'quiz' ? 'primary' : 'info'; ?> btn-sm">
                                                            <i class="fa fa-play"></i> Play Game
                                                        </a>
                                                    <?php } ?>
                                                    
                                                    <span class="pull-right">
                                                        <small class="text-success">
                                                            <i class="fa fa-star"></i> <?php echo $game['points_per_question']; ?> pts/answer
                                                        </small>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center text-muted">
                                <i class="fa fa-gamepad fa-3x"></i><br><br>
                                <h4>No Games Available</h4>
                                <p>There are currently no games assigned to your class. Check back later!</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Achievements -->
                <?php if (!empty($achievements)) { ?>
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-trophy"></i> Achievements</h3>
                        </div>
                        <div class="box-body">
                            <?php foreach ($achievements as $achievement) { ?>
                                <div class="achievement-item">
                                    <i class="fa <?php echo $achievement['icon']; ?>" style="color: <?php echo $achievement['color']; ?>;"></i>
                                    <strong><?php echo $achievement['name']; ?></strong><br>
                                    <small class="text-muted"><?php echo $achievement['description']; ?></small>
                                </div>
                                <hr>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Leaderboard -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-trophy"></i> Top Players</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($leaderboard)) { ?>
                            <div class="leaderboard">
                                <?php foreach (array_slice($leaderboard, 0, 5) as $index => $player) { ?>
                                    <div class="leaderboard-item <?php echo $player['student_id'] == $this->customlib->getStudentSessionUserID() ? 'my-rank' : ''; ?>">
                                        <div class="rank">
                                            <?php if ($index == 0) { ?>
                                                <i class="fa fa-trophy" style="color: gold;"></i>
                                            <?php } elseif ($index == 1) { ?>
                                                <i class="fa fa-trophy" style="color: silver;"></i>
                                            <?php } elseif ($index == 2) { ?>
                                                <i class="fa fa-trophy" style="color: #cd7f32;"></i>
                                            <?php } else { ?>
                                                <span class="rank-number"><?php echo $index + 1; ?></span>
                                            <?php } ?>
                                        </div>
                                        <div class="player-info">
                                            <strong><?php echo htmlspecialchars($player['firstname'] . ' ' . $player['lastname']); ?></strong>
                                            <br>
                                            <small>
                                                Level <?php echo $player['current_level']; ?> • 
                                                <?php echo $player['total_points']; ?> pts
                                            </small>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted text-center">No players yet. Be the first!</p>
                        <?php } ?>
                    </div>
                </div>

                <!-- Gaming Tips -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Gaming Tips</h3>
                    </div>
                    <div class="box-body">
                        <ul class="list-unstyled">
                            <li><i class="fa fa-check text-green"></i> Answer correctly to earn more points</li>
                            <li><i class="fa fa-clock-o text-blue"></i> Complete games faster for bonus points</li>
                            <li><i class="fa fa-star text-yellow"></i> Try harder difficulty levels for bonus multipliers</li>
                            <li><i class="fa fa-trophy text-purple"></i> Level up by earning points consistently</li>
                            <li><i class="fa fa-refresh text-orange"></i> You have limited attempts per game</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.achievement-item {
    padding: 10px 0;
}

.achievement-item i {
    font-size: 20px;
    margin-right: 10px;
}

.leaderboard-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.leaderboard-item:last-child {
    border-bottom: none;
}

.leaderboard-item.my-rank {
    background-color: #f9f9f9;
    border-radius: 4px;
    padding: 8px 10px;
    border: 2px solid #3c8dbc;
}

.leaderboard-item .rank {
    width: 30px;
    text-align: center;
    margin-right: 15px;
}

.leaderboard-item .rank-number {
    font-weight: bold;
    color: #666;
}

.leaderboard-item .player-info {
    flex: 1;
}

.panel {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.panel:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.game-info {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
}

.progress {
    height: 25px;
}

.progress-bar {
    line-height: 25px;
    font-weight: bold;
}
</style>