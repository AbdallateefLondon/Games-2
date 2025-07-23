<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-gamepad"></i> <?php echo isset($game) ? 'Edit Game' : 'Create Game'; ?>
            <small><?php echo isset($game) ? 'Modify existing game' : 'Create new educational game'; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/admin/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo site_url('gamebuilder'); ?>">Games</a></li>
            <li class="active"><?php echo isset($game) ? 'Edit' : 'Create'; ?></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-<?php echo isset($game) ? 'edit' : 'plus'; ?>"></i>
                            <?php echo isset($game) ? 'Edit Game: ' . htmlspecialchars($game['title']) : 'Create New Game'; ?>
                        </h3>
                    </div>

                    <form id="gameForm" method="post" action="<?php echo current_url(); ?>">
                        <?php if (isset($game)) { ?>
                            <input type="hidden" name="id" value="<?php echo $game['id']; ?>">
                        <?php } ?>

                        <div class="box-body">
                            <div class="row">
                                <!-- Basic Game Information -->
                                <div class="col-md-6">
                                    <h4><i class="fa fa-info-circle"></i> Basic Information</h4>
                                    
                                    <div class="form-group">
                                        <label for="title">Game Title <small class="req">*</small></label>
                                        <input type="text" class="form-control" name="title" id="title" 
                                               value="<?php echo set_value('title', isset($game) ? $game['title'] : ''); ?>" 
                                               placeholder="Enter game title" required>
                                        <span class="text-danger"><?php echo form_error('title'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" name="description" id="description" rows="3" 
                                                  placeholder="Enter game description"><?php echo set_value('description', isset($game) ? $game['description'] : ''); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="game_type">Game Type <small class="req">*</small></label>
                                        <select class="form-control" name="game_type" id="game_type" onchange="loadGameTemplate()" required>
                                            <option value="">Select Game Type</option>
                                            <option value="quiz" <?php echo set_select('game_type', 'quiz', (isset($game) && $game['game_type'] == 'quiz')); ?>>
                                                Quiz Game
                                            </option>
                                            <option value="matching" <?php echo set_select('game_type', 'matching', (isset($game) && $game['game_type'] == 'matching')); ?>>
                                                Matching Game
                                            </option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('game_type'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="difficulty_level">Difficulty Level</label>
                                        <select class="form-control" name="difficulty_level" id="difficulty_level">
                                            <option value="easy" <?php echo set_select('difficulty_level', 'easy', (isset($game) && $game['difficulty_level'] == 'easy')); ?>>
                                                Easy (1x points)
                                            </option>
                                            <option value="medium" <?php echo set_select('difficulty_level', 'medium', (isset($game) && $game['difficulty_level'] == 'medium') || !isset($game)); ?>>
                                                Medium (1.2x points)
                                            </option>
                                            <option value="hard" <?php echo set_select('difficulty_level', 'hard', (isset($game) && $game['difficulty_level'] == 'hard')); ?>>
                                                Hard (1.5x points)
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Assignment Settings -->
                                <div class="col-md-6">
                                    <h4><i class="fa fa-users"></i> Assignment Settings</h4>
                                    
                                    <div class="form-group">
                                        <label for="class_id">Class</label>
                                        <select class="form-control" name="class_id" id="class_id" onchange="getSections(this.value)">
                                            <option value="">All Classes (Global Game)</option>
                                            <?php foreach ($classes as $class) { ?>
                                                <option value="<?php echo $class['id']; ?>" 
                                                    <?php echo set_select('class_id', $class['id'], (isset($game) && $game['class_id'] == $class['id'])); ?>>
                                                    <?php echo $class['class']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <small class="text-muted">Leave empty to make game available to all classes</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="section_id">Section</label>
                                        <select class="form-control" name="section_id" id="section_id">
                                            <option value="">All Sections</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="subject_id">Subject</label>
                                        <select class="form-control" name="subject_id" id="subject_id">
                                            <option value="">General / No Subject</option>
                                            <?php foreach ($subjects as $subject) { ?>
                                                <option value="<?php echo $subject['id']; ?>" 
                                                    <?php echo set_select('subject_id', $subject['id'], (isset($game) && $game['subject_id'] == $subject['id'])); ?>>
                                                    <?php echo $subject['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="max_attempts">Max Attempts</label>
                                                <input type="number" class="form-control" name="max_attempts" id="max_attempts" 
                                                       value="<?php echo set_value('max_attempts', isset($game) ? $game['max_attempts'] : '3'); ?>" 
                                                       min="1" max="10">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="time_limit">Time Limit (minutes)</label>
                                                <input type="number" class="form-control" name="time_limit" id="time_limit" 
                                                       value="<?php echo set_value('time_limit', isset($game) ? $game['time_limit'] : ''); ?>" 
                                                       min="1" max="120" placeholder="No limit">
                                                <small class="text-muted">Leave empty for no time limit</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="points_per_question">Points per Correct Answer</label>
                                        <input type="number" class="form-control" name="points_per_question" id="points_per_question" 
                                               value="<?php echo set_value('points_per_question', isset($game) ? $game['points_per_question'] : '10'); ?>" 
                                               min="1" max="100">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Game Content Builder -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4><i class="fa fa-puzzle-piece"></i> Game Content</h4>
                                    <div id="gameContentBuilder">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            Please select a game type above to start building your game content.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for game content JSON -->
                            <input type="hidden" name="game_content" id="game_content" value="<?php echo isset($game) ? htmlspecialchars($game['game_content']) : ''; ?>">
                        </div>

                        <div class="box-footer">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?php echo isset($game) ? 'Update Game' : 'Create Game'; ?>
                            </button>
                            <a href="<?php echo site_url('gamebuilder'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Games
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Game Templates -->
<script id="quizTemplate" type="text/template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-question-circle"></i> Quiz Questions
                <div class="pull-right">
                    <button type="button" class="btn btn-success btn-xs" onclick="addQuizQuestion()">
                        <i class="fa fa-plus"></i> Add Question
                    </button>
                </div>
            </h4>
        </div>
        <div class="panel-body">
            <div id="quizQuestions">
                <!-- Questions will be added here -->
            </div>
        </div>
    </div>
</script>

<script id="matchingTemplate" type="text/template">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-puzzle-piece"></i> Matching Pairs
                <div class="pull-right">
                    <button type="button" class="btn btn-success btn-xs" onclick="addMatchingPair()">
                        <i class="fa fa-plus"></i> Add Pair
                    </button>
                </div>
            </h4>
        </div>
        <div class="panel-body">
            <div id="matchingPairs">
                <!-- Pairs will be added here -->
            </div>
        </div>
    </div>
</script>

<script>
let gameData = {
    questions: [],
    pairs: []
};

$(document).ready(function() {
    // Load existing game content if editing
    <?php if (isset($game)) { ?>
        $('#game_type').trigger('change');
        setTimeout(function() {
            loadExistingContent(<?php echo $game['game_content']; ?>);
        }, 500);
    <?php } ?>

    // Load sections if class is selected
    var selected_class = $('#class_id').val();
    if (selected_class) {
        getSections(selected_class);
        setTimeout(function() {
            $('#section_id').val('<?php echo isset($game) ? $game['section_id'] : ''; ?>');
        }, 500);
    }
});

function loadGameTemplate() {
    const gameType = $('#game_type').val();
    const builder = $('#gameContentBuilder');
    
    if (!gameType) {
        builder.html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Please select a game type above to start building your game content.</div>');
        return;
    }

    if (gameType === 'quiz') {
        builder.html($('#quizTemplate').html());
        gameData.questions = [];
        addQuizQuestion(); // Add first question
    } else if (gameType === 'matching') {
        builder.html($('#matchingTemplate').html());
        gameData.pairs = [];
        addMatchingPair(); // Add first pair
    }
}

function addQuizQuestion() {
    const questionIndex = gameData.questions.length;
    gameData.questions.push({
        question: '',
        options: ['', '', '', ''],
        correct_answer: 0,
        explanation: '',
        points: 10
    });

    const questionHtml = `
        <div class="panel panel-default question-panel" data-index="${questionIndex}">
            <div class="panel-heading">
                <h5 class="panel-title">
                    Question ${questionIndex + 1}
                    <div class="pull-right">
                        <button type="button" class="btn btn-danger btn-xs" onclick="removeQuizQuestion(${questionIndex})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </h5>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>Question Text:</label>
                    <textarea class="form-control" rows="2" placeholder="Enter your question"
                              onchange="updateQuizQuestion(${questionIndex}, 'question', this.value)"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Answer Options:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="correct_${questionIndex}" value="0" checked
                                           onchange="updateQuizQuestion(${questionIndex}, 'correct_answer', 0)">
                                </span>
                                <input type="text" class="form-control" placeholder="Option A"
                                       onchange="updateQuizOption(${questionIndex}, 0, this.value)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="correct_${questionIndex}" value="1"
                                           onchange="updateQuizQuestion(${questionIndex}, 'correct_answer', 1)">
                                </span>
                                <input type="text" class="form-control" placeholder="Option B"
                                       onchange="updateQuizOption(${questionIndex}, 1, this.value)">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="correct_${questionIndex}" value="2"
                                           onchange="updateQuizQuestion(${questionIndex}, 'correct_answer', 2)">
                                </span>
                                <input type="text" class="form-control" placeholder="Option C"
                                       onchange="updateQuizOption(${questionIndex}, 2, this.value)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="correct_${questionIndex}" value="3"
                                           onchange="updateQuizQuestion(${questionIndex}, 'correct_answer', 3)">
                                </span>
                                <input type="text" class="form-control" placeholder="Option D"
                                       onchange="updateQuizOption(${questionIndex}, 3, this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Explanation (Optional):</label>
                    <textarea class="form-control" rows="2" placeholder="Explain why this answer is correct"
                              onchange="updateQuizQuestion(${questionIndex}, 'explanation', this.value)"></textarea>
                </div>
            </div>
        </div>
    `;

    $('#quizQuestions').append(questionHtml);
    updateGameContent();
}

function removeQuizQuestion(index) {
    if (gameData.questions.length <= 1) {
        alert('You must have at least one question.');
        return;
    }
    
    gameData.questions.splice(index, 1);
    refreshQuizQuestions();
}

function refreshQuizQuestions() {
    $('#quizQuestions').empty();
    const questions = [...gameData.questions];
    gameData.questions = [];
    
    questions.forEach((question, index) => {
        addQuizQuestion();
        // Restore data
        const panel = $(`.question-panel[data-index="${index}"]`);
        panel.find('textarea').first().val(question.question);
        panel.find('input[type="text"]').each(function(i) {
            $(this).val(question.options[i] || '');
        });
        panel.find(`input[name="correct_${index}"][value="${question.correct_answer}"]`).prop('checked', true);
        panel.find('textarea').last().val(question.explanation);
    });
    
    updateGameContent();
}

function updateQuizQuestion(index, field, value) {
    if (!gameData.questions[index]) {
        gameData.questions[index] = {
            question: '',
            options: ['', '', '', ''],
            correct_answer: 0,
            explanation: '',
            points: 10
        };
    }
    
    gameData.questions[index][field] = field === 'correct_answer' ? parseInt(value) : value;
    updateGameContent();
}

function updateQuizOption(questionIndex, optionIndex, value) {
    if (!gameData.questions[questionIndex]) {
        gameData.questions[questionIndex] = {
            question: '',
            options: ['', '', '', ''],
            correct_answer: 0,
            explanation: '',
            points: 10
        };
    }
    
    gameData.questions[questionIndex].options[optionIndex] = value;
    updateGameContent();
}

function addMatchingPair() {
    const pairIndex = gameData.pairs.length;
    gameData.pairs.push({
        left: '',
        right: '',
        points: 10
    });

    const pairHtml = `
        <div class="panel panel-default pair-panel" data-index="${pairIndex}">
            <div class="panel-heading">
                <h5 class="panel-title">
                    Pair ${pairIndex + 1}
                    <div class="pull-right">
                        <button type="button" class="btn btn-danger btn-xs" onclick="removeMatchingPair(${pairIndex})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Left Side (Question/Term):</label>
                            <input type="text" class="form-control" placeholder="Enter left side content"
                                   onchange="updateMatchingPair(${pairIndex}, 'left', this.value)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Right Side (Answer/Definition):</label>
                            <input type="text" class="form-control" placeholder="Enter right side content"
                                   onchange="updateMatchingPair(${pairIndex}, 'right', this.value)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#matchingPairs').append(pairHtml);
    updateGameContent();
}

function removeMatchingPair(index) {
    if (gameData.pairs.length <= 1) {
        alert('You must have at least one matching pair.');
        return;
    }
    
    gameData.pairs.splice(index, 1);
    refreshMatchingPairs();
}

function refreshMatchingPairs() {
    $('#matchingPairs').empty();
    const pairs = [...gameData.pairs];
    gameData.pairs = [];
    
    pairs.forEach((pair, index) => {
        addMatchingPair();
        // Restore data
        const panel = $(`.pair-panel[data-index="${index}"]`);
        panel.find('input[type="text"]').first().val(pair.left);
        panel.find('input[type="text"]').last().val(pair.right);
    });
    
    updateGameContent();
}

function updateMatchingPair(index, field, value) {
    if (!gameData.pairs[index]) {
        gameData.pairs[index] = {
            left: '',
            right: '',
            points: 10
        };
    }
    
    gameData.pairs[index][field] = value;
    updateGameContent();
}

function updateGameContent() {
    const gameType = $('#game_type').val();
    let content = {};
    
    if (gameType === 'quiz') {
        content = { questions: gameData.questions };
    } else if (gameType === 'matching') {
        content = { pairs: gameData.pairs };
    }
    
    $('#game_content').val(JSON.stringify(content));
}

function loadExistingContent(content) {
    const gameType = $('#game_type').val();
    
    if (gameType === 'quiz' && content.questions) {
        gameData.questions = content.questions;
        $('#quizQuestions').empty();
        
        content.questions.forEach((question, index) => {
            addQuizQuestion();
            // Restore question data
            const panel = $(`.question-panel[data-index="${index}"]`);
            panel.find('textarea').first().val(question.question);
            panel.find('input[type="text"]').each(function(i) {
                $(this).val(question.options[i] || '');
            });
            panel.find(`input[name="correct_${index}"][value="${question.correct_answer}"]`).prop('checked', true);
            panel.find('textarea').last().val(question.explanation || '');
        });
    } else if (gameType === 'matching' && content.pairs) {
        gameData.pairs = content.pairs;
        $('#matchingPairs').empty();
        
        content.pairs.forEach((pair, index) => {
            addMatchingPair();
            // Restore pair data
            const panel = $(`.pair-panel[data-index="${index}"]`);
            panel.find('input[type="text"]').first().val(pair.left);
            panel.find('input[type="text"]').last().val(pair.right);
        });
    }
}

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

// Form validation
$('#gameForm').on('submit', function(e) {
    const gameType = $('#game_type').val();
    const title = $('#title').val().trim();
    
    if (!title) {
        alert('Please enter a game title.');
        e.preventDefault();
        return false;
    }
    
    if (!gameType) {
        alert('Please select a game type.');
        e.preventDefault();
        return false;
    }
    
    const content = $('#game_content').val();
    if (!content || content === '{}' || content === '{"questions":[],"pairs":[]}') {
        alert('Please add at least one question or matching pair.');
        e.preventDefault();
        return false;
    }
    
    // Validate content based on game type
    try {
        const parsedContent = JSON.parse(content);
        
        if (gameType === 'quiz') {
            if (!parsedContent.questions || parsedContent.questions.length === 0) {
                alert('Please add at least one quiz question.');
                e.preventDefault();
                return false;
            }
            
            for (let i = 0; i < parsedContent.questions.length; i++) {
                const q = parsedContent.questions[i];
                if (!q.question.trim()) {
                    alert(`Question ${i + 1} is missing the question text.`);
                    e.preventDefault();
                    return false;
                }
                
                const filledOptions = q.options.filter(opt => opt.trim() !== '').length;
                if (filledOptions < 2) {
                    alert(`Question ${i + 1} must have at least 2 answer options.`);
                    e.preventDefault();
                    return false;
                }
            }
        } else if (gameType === 'matching') {
            if (!parsedContent.pairs || parsedContent.pairs.length === 0) {
                alert('Please add at least one matching pair.');
                e.preventDefault();
                return false;
            }
            
            for (let i = 0; i < parsedContent.pairs.length; i++) {
                const p = parsedContent.pairs[i];
                if (!p.left.trim() || !p.right.trim()) {
                    alert(`Pair ${i + 1} is missing content on one or both sides.`);
                    e.preventDefault();
                    return false;
                }
            }
        }
    } catch (e) {
        alert('Invalid game content format. Please refresh and try again.');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>

<style>
.req {
    color: red;
}

.question-panel, .pair-panel {
    margin-bottom: 15px;
}

.input-group-addon input[type="radio"] {
    margin: 0;
}

#gameContentBuilder {
    min-height: 200px;
}

.panel-title {
    font-size: 14px;
}

.panel-title .pull-right {
    margin-top: -2px;
}
</style>