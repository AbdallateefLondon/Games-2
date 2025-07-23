<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-gamepad"></i> <?php echo $this->lang->line('educational_games'); ?>
            <small><?php echo $this->lang->line('manage_educational_games'); ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/admin/dashboard"><i class="fa fa-dashboard"></i> <?php echo $this->lang->line('dashboard'); ?></a></li>
            <li class="active"><?php echo $this->lang->line('educational_games'); ?></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $game_stats['total_games']; ?></h3>
                        <p>Total Games</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-game-controller-b"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo count(array_filter($game_stats['games_by_type'], function($g) { return $g['game_type'] == 'quiz'; })); ?></h3>
                        <p>Quiz Games</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-question-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo count(array_filter($game_stats['games_by_type'], function($g) { return $g['game_type'] == 'matching'; })); ?></h3>
                        <p>Matching Games</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-puzzle-piece"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo count($game_stats['most_played']); ?></h3>
                        <p>Popular Games</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-trophy"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list"></i> Game Management</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('games_management', 'can_add')) { ?>
                                <a href="<?php echo site_url('gamebuilder/create'); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Create New Game
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Filters -->
                        <div class="row">
                            <div class="col-md-12">
                                <form method="get" action="<?php echo site_url('gamebuilder'); ?>" class="form-inline">
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

                                    <div class="form-group">
                                        <label>Game Type:</label>
                                        <select name="game_type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="quiz" <?php echo set_select('game_type', 'quiz', ($this->input->get('game_type') == 'quiz')); ?>>Quiz</option>
                                            <option value="matching" <?php echo set_select('game_type', 'matching', ($this->input->get('game_type') == 'matching')); ?>>Matching</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="<?php echo site_url('gamebuilder'); ?>" class="btn btn-default">Reset</a>
                                </form>
                            </div>
                        </div>

                        <hr>

                        <!-- Games Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="gamesTable">
                                <thead>
                                    <tr>
                                        <th>Game Title</th>
                                        <th>Type</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Difficulty</th>
                                        <th>Max Attempts</th>
                                        <th>Created By</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($gameList)) { ?>
                                        <?php foreach ($gameList as $game) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($game['title']); ?></strong>
                                                    <?php if ($game['description']) { ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($game['description'], 0, 100)); ?>...</small>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <span class="label label-<?php echo $game['game_type'] == 'quiz' ? 'primary' : 'info'; ?>">
                                                        <?php echo ucfirst($game['game_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo $game['class'] ? $game['class'] : '<span class="text-muted">All Classes</span>'; ?>
                                                    <?php if ($game['section']) { ?>
                                                        (<?php echo $game['section']; ?>)
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $game['subject_name'] ?: '<span class="text-muted">General</span>'; ?></td>
                                                <td>
                                                    <span class="label label-<?php 
                                                        echo $game['difficulty_level'] == 'easy' ? 'success' : 
                                                             ($game['difficulty_level'] == 'medium' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <?php echo ucfirst($game['difficulty_level']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $game['max_attempts']; ?></td>
                                                <td><?php echo htmlspecialchars($game['creator_name'] . ' ' . $game['creator_surname']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($game['created_at'])); ?></td>
                                                <td>
                                                    <?php if ($game['is_active']) { ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php } else { ?>
                                                        <span class="label label-default">Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <?php if ($this->rbac->hasPrivilege('game_results', 'can_view')) { ?>
                                                            <a href="<?php echo site_url('gamebuilder/results/' . $game['id']); ?>" 
                                                               class="btn btn-default btn-xs" title="View Results">
                                                                <i class="fa fa-bar-chart"></i>
                                                            </a>
                                                        <?php } ?>
                                                        
                                                        <?php if ($this->rbac->hasPrivilege('games_management', 'can_edit')) { ?>
                                                            <?php if ($this->session->userdata('admin')['role_id'] == 7 || 
                                                                     $game['created_by'] == $this->session->userdata('admin')['id']) { ?>
                                                                <a href="<?php echo site_url('gamebuilder/create/' . $game['id']); ?>" 
                                                                   class="btn btn-primary btn-xs" title="Edit Game">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                        <?php if ($this->rbac->hasPrivilege('games_management', 'can_delete')) { ?>
                                                            <?php if ($this->session->userdata('admin')['role_id'] == 7 || 
                                                                     $game['created_by'] == $this->session->userdata('admin')['id']) { ?>
                                                                <a href="<?php echo site_url('gamebuilder/delete/' . $game['id']); ?>" 
                                                                   class="btn btn-danger btn-xs" title="Delete Game"
                                                                   onclick="return confirm('Are you sure you want to delete this game?');">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                <i class="fa fa-gamepad fa-3x"></i><br><br>
                                                No games found. <a href="<?php echo site_url('gamebuilder/create'); ?>">Create your first game</a>
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

        <!-- Most Played Games -->
        <?php if (!empty($game_stats['most_played'])) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-fire"></i> Most Played Games</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Game Title</th>
                                            <th>Type</th>
                                            <th>Play Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($game_stats['most_played'] as $popular_game) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($popular_game['title']); ?></td>
                                                <td>
                                                    <span class="label label-<?php echo $popular_game['game_type'] == 'quiz' ? 'primary' : 'info'; ?>">
                                                        <?php echo ucfirst($popular_game['game_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-green"><?php echo $popular_game['play_count']; ?></span>
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
        <?php } ?>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#gamesTable').DataTable({
        "ordering": true,
        "searching": true,
        "paging": true,
        "info": true,
        "responsive": true
    });
});

function getSections(class_id) {
    if (class_id === '') {
        $('#section_id').html('<option value="">All Sections</option>');
        return;
    }
    
    $.post('<?php echo site_url("gamebuilder/get_sections"); ?>', {
        class_id: class_id
    }, function(data) {
        var sections = JSON.parse(data);
        var options = '<option value="">All Sections</option>';
        
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