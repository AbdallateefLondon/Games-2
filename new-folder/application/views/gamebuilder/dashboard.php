<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-dashboard"></i> Game Analytics Dashboard
            <small>Comprehensive gaming performance insights</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/admin/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Game Analytics</li>
        </ol>
    </section>

    <section class="content">
        <!-- Overview Statistics -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $game_stats['total_games']; ?></h3>
                        <p>Total Games</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-gamepad"></i>
                    </div>
                    <a href="<?php echo site_url('gamebuilder'); ?>" class="small-box-footer">
                        Manage Games <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo $activity_summary['total_active_students']; ?></h3>
                        <p>Active Players</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="<?php echo site_url('gamebuilder/leaderboard'); ?>" class="small-box-footer">
                        View Leaderboard <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo number_format($activity_summary['average_points'], 1); ?></h3>
                        <p>Avg Points/Student</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-star"></i>
                    </div>
                    <a href="<?php echo site_url('gamebuilder/results'); ?>" class="small-box-footer">
                        View Results <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo $activity_summary['recent_players']; ?></h3>
                        <p>Recent Players (7d)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        Last 7 Days <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Game Types Distribution -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Games by Type</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="gameTypesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Level Distribution -->
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-trophy"></i> Highest Level Reached</h3>
                    </div>
                    <div class="box-body">
                        <div class="level-display text-center">
                            <div class="level-badge">
                                <i class="fa fa-crown fa-3x text-yellow"></i>
                                <h2 class="level-number">Level <?php echo $activity_summary['top_level']; ?></h2>
                                <p class="text-muted">Highest achieved level</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Popular Games -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-fire"></i> Most Popular Games</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Game Title</th>
                                        <th>Type</th>
                                        <th>Play Count</th>
                                        <th>Avg Score</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($game_stats['most_played'])) { ?>
                                        <?php foreach ($game_stats['most_played'] as $index => $game) { ?>
                                            <tr>
                                                <td>
                                                    <?php if ($index == 0) { ?>
                                                        <i class="fa fa-trophy text-yellow"></i>
                                                    <?php } elseif ($index == 1) { ?>
                                                        <i class="fa fa-trophy" style="color: silver;"></i>
                                                    <?php } elseif ($index == 2) { ?>
                                                        <i class="fa fa-trophy" style="color: #cd7f32;"></i>
                                                    <?php } else { ?>
                                                        <span class="badge bg-gray"><?php echo $index + 1; ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($game['title']); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="label label-<?php echo $game['game_type'] == 'quiz' ? 'primary' : 'info'; ?>">
                                                        <?php echo ucfirst($game['game_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-green"><?php echo $game['play_count']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-blue">--</span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo site_url('gamebuilder/results/' . $game['id']); ?>" 
                                                       class="btn btn-xs btn-primary" title="View Details">
                                                        <i class="fa fa-bar-chart"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No games have been played yet.
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-history"></i> Recent Game Activity</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($recent_results)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Game</th>
                                            <th>Score</th>
                                            <th>Points Earned</th>
                                            <th>Time</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recent_results, 0, 10) as $result) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($result['firstname'] . ' ' . $result['lastname']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo $result['class'] . ' - ' . $result['section']; ?></small>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($result['game_title']); ?>
                                                    <br>
                                                    <span class="label label-<?php echo $result['game_type'] == 'quiz' ? 'primary' : 'info'; ?>">
                                                        <?php echo ucfirst($result['game_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $result['score'] >= 90 ? 'green' : 
                                                             ($result['score'] >= 70 ? 'yellow' : 'red'); 
                                                    ?>">
                                                        <?php echo $result['score']; ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fa fa-star text-yellow"></i>
                                                    <?php echo $result['points_earned']; ?>
                                                </td>
                                                <td>
                                                    <?php if ($result['time_taken']) { ?>
                                                        <i class="fa fa-clock-o"></i>
                                                        <?php echo gmdate("i:s", $result['time_taken']); ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">--</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <small><?php echo date('M d, H:i', strtotime($result['completed_at'])); ?></small>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center text-muted">
                                <i class="fa fa-history fa-3x"></i><br><br>
                                <h4>No Recent Activity</h4>
                                <p>Game results will appear here once students start playing.</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid bg-light-blue-gradient">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-rocket"></i> Quick Actions</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="<?php echo site_url('gamebuilder/create'); ?>" class="btn btn-app bg-green">
                                    <i class="fa fa-plus"></i> Create Game
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo site_url('gamebuilder/results'); ?>" class="btn btn-app bg-blue">
                                    <i class="fa fa-bar-chart"></i> View Results
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo site_url('gamebuilder/leaderboard'); ?>" class="btn btn-app bg-yellow">
                                    <i class="fa fa-trophy"></i> Leaderboard
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo site_url('gamebuilder'); ?>" class="btn btn-app bg-purple">
                                    <i class="fa fa-gamepad"></i> Manage Games
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Game Types Chart
    const gameTypesData = <?php echo json_encode($game_stats['games_by_type']); ?>;
    
    if (gameTypesData.length > 0) {
        const ctx = document.getElementById('gameTypesChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: gameTypesData.map(item => item.game_type.charAt(0).toUpperCase() + item.game_type.slice(1) + ' Games'),
                datasets: [{
                    data: gameTypesData.map(item => item.count),
                    backgroundColor: [
                        '#3c8dbc',
                        '#00c0ef',
                        '#00a65a',
                        '#f39c12',
                        '#dd4b39'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } else {
        $('#gameTypesChart').parent().html('<div class="text-center text-muted"><i class="fa fa-pie-chart fa-3x"></i><br><br>No games created yet</div>');
    }
});
</script>

<style>
.level-display {
    padding: 30px;
}

.level-badge {
    display: inline-block;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.level-number {
    margin: 20px 0 10px 0;
    font-size: 36px;
    font-weight: bold;
}

.btn-app {
    margin: 5px;
    min-width: 120px;
}

.small-box .icon {
    top: 10px;
}

.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}

.badge {
    font-size: 11px;
}

.bg-light-blue-gradient {
    background: linear-gradient(45deg, #3c8dbc, #67a3c1) !important;
    color: white;
}

.bg-light-blue-gradient .box-title {
    color: white;
}

#gameTypesChart {
    max-height: 300px;
}
</style>