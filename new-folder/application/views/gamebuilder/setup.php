<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-cog"></i> Educational Game System Setup
            <small>Install and configure the gaming system</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/admin/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Game Setup</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-gamepad"></i> Educational Game System Setup
                        </h3>
                    </div>
                    
                    <div class="box-body">
                        <?php if (isset($msg)) echo $msg; ?>
                        
                        <div class="setup-status">
                            <h4><i class="fa fa-info-circle"></i> Current Setup Status</h4>
                            
                            <div class="status-item">
                                <div class="status-icon">
                                    <?php if ($setup_status['tables_exist']) { ?>
                                        <i class="fa fa-check-circle text-success"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-times-circle text-danger"></i>
                                    <?php } ?>
                                </div>
                                <div class="status-info">
                                    <strong>Database Tables</strong>
                                    <p><?php echo $setup_status['tables_exist'] ? 'All required tables exist' : 'Missing tables: ' . implode(', ', $setup_status['missing_tables']); ?></p>
                                </div>
                            </div>

                            <div class="status-item">
                                <div class="status-icon">
                                    <?php if ($setup_status['permissions_exist']) { ?>
                                        <i class="fa fa-check-circle text-success"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-times-circle text-danger"></i>
                                    <?php } ?>
                                </div>
                                <div class="status-info">
                                    <strong>Permission System</strong>
                                    <p><?php echo $setup_status['permissions_exist'] ? 'Game permissions are configured' : 'Game permissions need to be installed'; ?></p>
                                </div>
                            </div>

                            <div class="overall-status">
                                <?php if ($setup_status['setup_complete']) { ?>
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i>
                                        <strong>Setup Complete!</strong> The Educational Game System is ready to use.
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <strong>Setup Required!</strong> The Educational Game System needs to be installed.
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <hr>

                        <div class="setup-info">
                            <h4><i class="fa fa-list"></i> What will be installed:</h4>
                            <ul class="installation-list">
                                <li><i class="fa fa-database text-blue"></i> <strong>educational_games</strong> - Stores game content and metadata</li>
                                <li><i class="fa fa-database text-blue"></i> <strong>game_results</strong> - Records student game attempts and scores</li>
                                <li><i class="fa fa-database text-blue"></i> <strong>student_points</strong> - Tracks student points, levels, and achievements</li>
                                <li><i class="fa fa-key text-green"></i> <strong>game_builder</strong> - Permission group for staff to manage games</li>
                                <li><i class="fa fa-key text-green"></i> <strong>student_games</strong> - Permission group for students to play games</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="features-info">
                            <h4><i class="fa fa-star"></i> Game System Features:</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="feature-list">
                                        <li><i class="fa fa-question-circle text-primary"></i> Quiz Games</li>
                                        <li><i class="fa fa-puzzle-piece text-info"></i> Matching Games</li>
                                        <li><i class="fa fa-trophy text-warning"></i> Points & Leveling System</li>
                                        <li><i class="fa fa-users text-success"></i> Class-based Game Assignment</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="feature-list">
                                        <li><i class="fa fa-bar-chart text-purple"></i> Performance Analytics</li>
                                        <li><i class="fa fa-clock-o text-orange"></i> Time Limits & Attempts</li>
                                        <li><i class="fa fa-shield text-red"></i> Role-based Access Control</li>
                                        <li><i class="fa fa-medal text-blue"></i> Student Leaderboards</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <?php if (!$setup_status['setup_complete']) { ?>
                            <form method="post" action="<?php echo site_url('admin/gamesetup/install'); ?>" style="display: inline;">
                                <input type="hidden" name="confirm_install" value="1">
                                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Are you sure you want to install the Educational Game System? This will create new database tables.');">
                                    <i class="fa fa-download"></i> Install Game System
                                </button>
                            </form>
                        <?php } else { ?>
                            <a href="<?php echo site_url('gamebuilder'); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-gamepad"></i> Go to Game Management
                            </a>
                            <a href="<?php echo site_url('gamebuilder/dashboard'); ?>" class="btn btn-info btn-lg">
                                <i class="fa fa-dashboard"></i> View Analytics
                            </a>
                        <?php } ?>

                        <?php if ($setup_status['setup_complete']) { ?>
                            <div class="pull-right">
                                <form method="post" action="<?php echo site_url('admin/gamesetup/uninstall'); ?>" style="display: inline;">
                                    <input type="hidden" name="confirm_uninstall" value="1">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('WARNING: This will completely remove the Educational Game System and ALL game data. This action cannot be undone! Are you sure?');">
                                        <i class="fa fa-trash"></i> Uninstall System
                                    </button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- System Requirements -->
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> System Requirements</h3>
                    </div>
                    <div class="box-body">
                        <div class="requirements-list">
                            <div class="requirement-item">
                                <i class="fa fa-check text-success"></i>
                                <strong>CodeIgniter Framework</strong> - Already installed and running
                            </div>
                            <div class="requirement-item">
                                <i class="fa fa-check text-success"></i>
                                <strong>MySQL Database</strong> - Connected and operational
                            </div>
                            <div class="requirement-item">
                                <i class="fa fa-check text-success"></i>
                                <strong>RBAC System</strong> - Permission system is active
                            </div>
                            <div class="requirement-item">
                                <i class="fa fa-check text-success"></i>
                                <strong>Student Management</strong> - Classes and sections configured
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.setup-status {
    margin-bottom: 30px;
}

.status-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.status-icon {
    font-size: 24px;
    margin-right: 15px;
    width: 30px;
}

.status-info {
    flex: 1;
}

.status-info strong {
    display: block;
    margin-bottom: 5px;
    font-size: 16px;
}

.status-info p {
    margin: 0;
    color: #666;
}

.overall-status {
    margin-top: 20px;
}

.installation-list {
    list-style: none;
    padding: 0;
}

.installation-list li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.installation-list li:last-child {
    border-bottom: none;
}

.installation-list i {
    width: 20px;
    margin-right: 10px;
}

.feature-list {
    list-style: none;
    padding: 0;
}

.feature-list li {
    padding: 5px 0;
}

.feature-list i {
    width: 20px;
    margin-right: 10px;
}

.requirements-list {
    padding: 10px 0;
}

.requirement-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.requirement-item:last-child {
    border-bottom: none;
}

.requirement-item i {
    font-size: 18px;
    margin-right: 15px;
    width: 20px;
}

.btn-lg {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>