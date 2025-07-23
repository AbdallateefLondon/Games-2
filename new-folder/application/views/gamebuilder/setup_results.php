<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-check-circle"></i> Setup Results
            <small>Educational Game System Installation</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/admin/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo site_url('admin/gamesetup'); ?>">Game Setup</a></li>
            <li class="active">Results</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box <?php echo $results['overall_success'] ? 'box-success' : 'box-danger'; ?>">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-<?php echo $results['overall_success'] ? 'check-circle' : 'times-circle'; ?>"></i>
                            Installation <?php echo $results['overall_success'] ? 'Successful' : 'Failed'; ?>
                        </h3>
                    </div>
                    
                    <div class="box-body">
                        <?php if ($results['overall_success']) { ?>
                            <div class="alert alert-success">
                                <h4><i class="fa fa-check"></i> Success!</h4>
                                The Educational Game System has been successfully installed and is ready to use.
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-danger">
                                <h4><i class="fa fa-times"></i> Installation Failed!</h4>
                                There were errors during the installation process. Please check the details below.
                            </div>
                        <?php } ?>

                        <div class="installation-summary">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="summary-box success-box">
                                        <div class="summary-number"><?php echo $results['success']; ?></div>
                                        <div class="summary-label">Successful Operations</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="summary-box error-box">
                                        <div class="summary-number"><?php echo $results['errors']; ?></div>
                                        <div class="summary-label">Failed Operations</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="summary-box total-box">
                                        <div class="summary-number"><?php echo count($results['messages']); ?></div>
                                        <div class="summary-label">Total Messages</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="installation-log">
                            <h4><i class="fa fa-list"></i> Installation Log</h4>
                            <div class="log-container">
                                <?php foreach ($results['messages'] as $message) { ?>
                                    <div class="log-entry">
                                        <?php if (strpos($message, '✓') !== false) { ?>
                                            <i class="fa fa-check-circle text-success"></i>
                                        <?php } elseif (strpos($message, '✗') !== false) { ?>
                                            <i class="fa fa-times-circle text-danger"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-info-circle text-info"></i>
                                        <?php } ?>
                                        <span class="log-message"><?php echo htmlspecialchars($message); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if ($results['overall_success']) { ?>
                            <hr>
                            <div class="next-steps">
                                <h4><i class="fa fa-arrow-right"></i> Next Steps</h4>
                                <div class="steps-list">
                                    <div class="step-item">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <strong>Assign Permissions</strong>
                                            <p>Go to <a href="<?php echo site_url('admin/roles'); ?>">Role Management</a> to assign game permissions to staff and student roles.</p>
                                        </div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">2</div>
                                        <div class="step-content">
                                            <strong>Create Your First Game</strong>
                                            <p>Start creating educational games for your students using the game builder.</p>
                                        </div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">3</div>
                                        <div class="step-content">
                                            <strong>Monitor Performance</strong>
                                            <p>Use the analytics dashboard to track student engagement and performance.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <?php if ($results['overall_success']) { ?>
                            <a href="<?php echo site_url('gamebuilder'); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-gamepad"></i> Start Creating Games
                            </a>
                            <a href="<?php echo site_url('gamebuilder/dashboard'); ?>" class="btn btn-info">
                                <i class="fa fa-dashboard"></i> View Dashboard
                            </a>
                            <a href="<?php echo site_url('admin/roles'); ?>" class="btn btn-warning">
                                <i class="fa fa-key"></i> Manage Permissions
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo site_url('admin/gamesetup'); ?>" class="btn btn-danger">
                                <i class="fa fa-arrow-left"></i> Back to Setup
                            </a>
                            <button type="button" class="btn btn-info" onclick="location.reload();">
                                <i class="fa fa-refresh"></i> Retry Installation
                            </button>
                        <?php } ?>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <?php if (!$results['overall_success']) { ?>
                    <div class="box box-warning">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-wrench"></i> Troubleshooting</h3>
                        </div>
                        <div class="box-body">
                            <h5>Common Issues and Solutions:</h5>
                            <ul>
                                <li><strong>Database Connection Error:</strong> Check your database credentials in <code>application/config/database.php</code></li>
                                <li><strong>Permission Denied:</strong> Ensure your database user has CREATE and INSERT privileges</li>
                                <li><strong>Table Already Exists:</strong> If tables exist but installation failed, try the uninstall option first</li>
                                <li><strong>Foreign Key Errors:</strong> Make sure your existing tables (classes, sections, students, staff) are properly configured</li>
                            </ul>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Need Help?</strong> If you continue experiencing issues, please contact your system administrator or check the installation logs above for specific error messages.
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<style>
.installation-summary {
    margin: 20px 0;
}

.summary-box {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.success-box {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.error-box {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.total-box {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.summary-number {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 5px;
}

.summary-label {
    font-size: 14px;
    text-transform: uppercase;
}

.installation-log {
    margin: 20px 0;
}

.log-container {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    max-height: 300px;
    overflow-y: auto;
}

.log-entry {
    display: flex;
    align-items: center;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.log-entry:last-child {
    border-bottom: none;
}

.log-entry i {
    font-size: 16px;
    margin-right: 10px;
    width: 20px;
}

.log-message {
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

.next-steps {
    margin: 20px 0;
}

.steps-list {
    margin-top: 15px;
}

.step-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border-left: 4px solid #28a745;
}

.step-number {
    width: 40px;
    height: 40px;
    background-color: #28a745;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    margin-right: 15px;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.step-content strong {
    display: block;
    margin-bottom: 5px;
    font-size: 16px;
    color: #28a745;
}

.step-content p {
    margin: 0;
    color: #666;
}

.btn-lg {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>