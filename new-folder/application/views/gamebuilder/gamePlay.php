<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-gamepad"></i> <?php echo htmlspecialchars($game['title']); ?>
            <small>Attempt #<?php echo $attempt_number; ?> of <?php echo $game['max_attempts']; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>user/user/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?php echo site_url('gamebuilder/student-games'); ?>">Games</a></li>
            <li class="active">Play Game</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Game Header -->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-<?php echo $game['game_type'] == 'quiz' ? 'question-circle' : 'puzzle-piece'; ?>"></i>
                            <?php echo htmlspecialchars($game['title']); ?>
                            <span class="label label-<?php 
                                echo $game['difficulty_level'] == 'easy' ? 'success' : 
                                     ($game['difficulty_level'] == 'medium' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo ucfirst($game['difficulty_level']); ?>
                            </span>
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($game['time_limit']) { ?>
                                <div class="timer-display">
                                    <i class="fa fa-clock-o"></i>
                                    <span id="timer"><?php echo $game['time_limit']; ?>:00</span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <?php if ($game['description']) { ?>
                        <div class="box-body">
                            <p class="lead"><?php echo htmlspecialchars($game['description']); ?></p>
                        </div>
                    <?php } ?>
                </div>

                <!-- Game Content -->
                <div class="box" id="gameContainer">
                    <div class="box-header">
                        <h3 class="box-title">
                            <span id="gameProgress">Question 1</span>
                        </h3>
                        <div class="box-tools pull-right">
                            <div class="progress-info">
                                <i class="fa fa-star text-yellow"></i>
                                <span id="currentPoints">0</span> points
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-body">
                        <div id="gameContent">
                            <!-- Game content will be loaded here -->
                        </div>
                        
                        <div class="game-controls text-center" style="margin-top: 30px;">
                            <button type="button" class="btn btn-primary btn-lg" id="nextBtn" onclick="nextQuestion()" style="display: none;">
                                Next <i class="fa fa-arrow-right"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-lg" id="finishBtn" onclick="finishGame()" style="display: none;">
                                Finish Game <i class="fa fa-check"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Game Instructions (initially visible) -->
                <div class="box" id="instructionsBox">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> Instructions</h3>
                    </div>
                    <div class="box-body">
                        <?php if ($game['game_type'] == 'quiz') { ?>
                            <div class="alert alert-info">
                                <h4><i class="fa fa-question-circle"></i> Quiz Game Instructions</h4>
                                <ul>
                                    <li>Answer each question by selecting the correct option</li>
                                    <li>You can't go back to previous questions once answered</li>
                                    <li>Each correct answer earns you <strong><?php echo $game['points_per_question']; ?> points</strong></li>
                                    <?php if ($game['time_limit']) { ?>
                                        <li>Complete the quiz within <strong><?php echo $game['time_limit']; ?> minutes</strong></li>
                                    <?php } ?>
                                    <li>Difficulty multiplier: <strong><?php echo $game['difficulty_level'] == 'hard' ? '1.5x' : ($game['difficulty_level'] == 'medium' ? '1.2x' : '1x'); ?></strong></li>
                                </ul>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <h4><i class="fa fa-puzzle-piece"></i> Matching Game Instructions</h4>
                                <ul>
                                    <li>Match items from the left side with their correct pairs on the right</li>
                                    <li>Type your answer in the text box for each item</li>
                                    <li>Each correct match earns you <strong><?php echo $game['points_per_question']; ?> points</strong></li>
                                    <?php if ($game['time_limit']) { ?>
                                        <li>Complete the matching within <strong><?php echo $game['time_limit']; ?> minutes</strong></li>
                                    <?php } ?>
                                    <li>Difficulty multiplier: <strong><?php echo $game['difficulty_level'] == 'hard' ? '1.5x' : ($game['difficulty_level'] == 'medium' ? '1.2x' : '1x'); ?></strong></li>
                                </ul>
                            </div>
                        <?php } ?>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-success btn-lg" onclick="startGame()">
                                <i class="fa fa-play"></i> Start Game
                            </button>
                            <a href="<?php echo site_url('gamebuilder/student-games'); ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-arrow-left"></i> Back to Games
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Modal -->
        <div class="modal fade" id="resultsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><i class="fa fa-trophy"></i> Game Complete!</h4>
                    </div>
                    <div class="modal-body">
                        <div id="resultsContent">
                            <!-- Results will be loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="<?php echo site_url('gamebuilder/student-games'); ?>" class="btn btn-primary">
                            <i class="fa fa-gamepad"></i> Play More Games
                        </a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
const gameData = <?php echo $game['game_content']; ?>;
const gameType = '<?php echo $game['game_type']; ?>';
const gameId = <?php echo $game['id']; ?>;
const studentId = <?php echo $student_id; ?>;
const studentSessionId = <?php echo $student_session_id; ?>;
const timeLimit = <?php echo $game['time_limit'] ?: 0; ?>;
const pointsPerQuestion = <?php echo $game['points_per_question']; ?>;

let currentQuestion = 0;
let userAnswers = {};
let gameStartTime = null;
let timerInterval = null;
let totalPoints = 0;

$(document).ready(function() {
    $('#gameContainer').hide();
});

function startGame() {
    $('#instructionsBox').hide();
    $('#gameContainer').show();
    
    gameStartTime = new Date();
    
    if (timeLimit > 0) {
        startTimer();
    }
    
    if (gameType === 'quiz') {
        loadQuizQuestion();
    } else if (gameType === 'matching') {
        loadMatchingGame();
    }
}

function startTimer() {
    let timeRemaining = timeLimit * 60; // Convert to seconds
    
    timerInterval = setInterval(function() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        $('#timer').text(
            (minutes < 10 ? '0' : '') + minutes + ':' + 
            (seconds < 10 ? '0' : '') + seconds
        );
        
        if (timeRemaining <= 60) {
            $('#timer').addClass('text-danger');
        }
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            finishGame(true); // true = time's up
        }
        
        timeRemaining--;
    }, 1000);
}

function loadQuizQuestion() {
    if (currentQuestion >= gameData.questions.length) {
        finishGame();
        return;
    }
    
    const question = gameData.questions[currentQuestion];
    $('#gameProgress').text('Question ' + (currentQuestion + 1) + ' of ' + gameData.questions.length);
    
    let html = `
        <div class="question-container">
            <h3 class="question-text">${question.question}</h3>
            <div class="options-container">
    `;
    
    question.options.forEach((option, index) => {
        if (option.trim()) {
            html += `
                <div class="option-item">
                    <label class="option-label">
                        <input type="radio" name="question_${currentQuestion}" value="${index}" 
                               onchange="selectAnswer(${index})">
                        <span class="option-text">${option}</span>
                    </label>
                </div>
            `;
        }
    });
    
    html += `
            </div>
            <div class="text-center" style="margin-top: 20px;">
                <button type="button" class="btn btn-primary" id="submitAnswer" onclick="submitQuizAnswer()" disabled>
                    Submit Answer
                </button>
            </div>
        </div>
    `;
    
    $('#gameContent').html(html);
}

function selectAnswer(answerIndex) {
    userAnswers[currentQuestion] = answerIndex;
    $('#submitAnswer').prop('disabled', false);
}

function submitQuizAnswer() {
    const question = gameData.questions[currentQuestion];
    const userAnswer = userAnswers[currentQuestion];
    const isCorrect = userAnswer === question.correct_answer;
    
    if (isCorrect) {
        totalPoints += pointsPerQuestion;
        $('#currentPoints').text(totalPoints);
    }
    
    // Show answer feedback
    showAnswerFeedback(isCorrect, question);
    
    currentQuestion++;
    
    if (currentQuestion >= gameData.questions.length) {
        $('#finishBtn').show();
    } else {
        $('#nextBtn').show();
    }
    
    $('#submitAnswer').hide();
}

function showAnswerFeedback(isCorrect, question) {
    const feedback = `
        <div class="answer-feedback ${isCorrect ? 'correct' : 'incorrect'}">
            <div class="feedback-header">
                <i class="fa fa-${isCorrect ? 'check-circle text-success' : 'times-circle text-danger'}"></i>
                <h4>${isCorrect ? 'Correct!' : 'Incorrect'}</h4>
            </div>
            <div class="feedback-body">
                ${isCorrect ? 
                    `<p class="text-success">Great job! You earned ${pointsPerQuestion} points.</p>` :
                    `<p class="text-danger">The correct answer was: <strong>${question.options[question.correct_answer]}</strong></p>`
                }
                ${question.explanation ? `<p class="explanation"><strong>Explanation:</strong> ${question.explanation}</p>` : ''}
            </div>
        </div>
    `;
    
    $('#gameContent').append(feedback);
    
    // Disable option selection
    $('input[type="radio"]').prop('disabled', true);
}

function nextQuestion() {
    $('#nextBtn').hide();
    loadQuizQuestion();
}

function loadMatchingGame() {
    $('#gameProgress').text('Matching Game');
    
    let html = `
        <div class="matching-container">
            <h3>Match the items below:</h3>
            <div class="matching-pairs">
    `;
    
    gameData.pairs.forEach((pair, index) => {
        html += `
            <div class="matching-pair">
                <div class="left-item">
                    <strong>${pair.left}</strong>
                </div>
                <div class="right-item">
                    <input type="text" class="form-control" placeholder="Enter matching answer" 
                           id="answer_${index}" onchange="updateMatchingAnswer(${index}, this.value)">
                </div>
            </div>
        `;
    });
    
    html += `
            </div>
            <div class="text-center" style="margin-top: 30px;">
                <button type="button" class="btn btn-success btn-lg" onclick="submitMatchingAnswers()">
                    Submit All Answers
                </button>
            </div>
        </div>
    `;
    
    $('#gameContent').html(html);
}

function updateMatchingAnswer(index, value) {
    userAnswers[index] = value;
}

function submitMatchingAnswers() {
    let correctCount = 0;
    
    gameData.pairs.forEach((pair, index) => {
        const userAnswer = userAnswers[index] || '';
        const isCorrect = userAnswer.trim().toLowerCase() === pair.right.trim().toLowerCase();
        
        if (isCorrect) {
            correctCount++;
            totalPoints += pointsPerQuestion;
        }
        
        // Show feedback for each pair
        const inputElement = $(`#answer_${index}`);
        inputElement.prop('disabled', true);
        
        if (isCorrect) {
            inputElement.addClass('correct-answer');
        } else {
            inputElement.addClass('incorrect-answer');
            inputElement.after(`<small class="text-danger">Correct: ${pair.right}</small>`);
        }
    });
    
    $('#currentPoints').text(totalPoints);
    $('#finishBtn').show();
    $('button[onclick="submitMatchingAnswers()"]').hide();
}

function finishGame(timeUp = false) {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    const gameEndTime = new Date();
    const timeTaken = Math.floor((gameEndTime - gameStartTime) / 1000); // seconds
    
    // Submit results to server
    $.post('<?php echo site_url("gamebuilder/submit_game"); ?>', {
        game_id: gameId,
        student_id: studentId,
        student_session_id: studentSessionId,
        answers: userAnswers,
        time_taken: timeTaken
    })
    .done(function(response) {
        const result = JSON.parse(response);
        if (result.status === 'success') {
            showResults(result, timeUp);
        } else {
            alert('Error saving results: ' + result.message);
        }
    })
    .fail(function() {
        alert('Error submitting game results. Please try again.');
    });
}

function showResults(result, timeUp) {
    const accuracy = result.total_questions > 0 ? 
        Math.round((result.correct_answers / result.total_questions) * 100) : 0;
    
    let performanceLevel = '';
    let performanceClass = '';
    
    if (accuracy >= 90) {
        performanceLevel = 'Excellent!';
        performanceClass = 'text-success';
    } else if (accuracy >= 80) {
        performanceLevel = 'Great Job!';
        performanceClass = 'text-primary';
    } else if (accuracy >= 70) {
        performanceLevel = 'Good Work!';
        performanceClass = 'text-info';
    } else if (accuracy >= 60) {
        performanceLevel = 'Keep Trying!';
        performanceClass = 'text-warning';
    } else {
        performanceLevel = 'Practice More!';
        performanceClass = 'text-danger';
    }
    
    const resultsHtml = `
        <div class="results-summary text-center">
            ${timeUp ? '<div class="alert alert-warning"><i class="fa fa-clock-o"></i> Time\'s up!</div>' : ''}
            
            <div class="performance-badge">
                <h2 class="${performanceClass}">${performanceLevel}</h2>
                <div class="score-circle">
                    <span class="score-percentage">${accuracy}%</span>
                </div>
            </div>
            
            <div class="results-stats">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <i class="fa fa-check-circle text-success"></i>
                            <h4>${result.correct_answers}</h4>
                            <p>Correct</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <i class="fa fa-times-circle text-danger"></i>
                            <h4>${result.total_questions - result.correct_answers}</h4>
                            <p>Incorrect</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <i class="fa fa-star text-warning"></i>
                            <h4>${result.points_earned}</h4>
                            <p>Points Earned</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <i class="fa fa-clock-o text-info"></i>
                            <h4>${Math.floor(${gameStartTime ? '(gameEndTime - gameStartTime) / 1000' : '0'} / 60)}m</h4>
                            <p>Time Taken</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="motivational-message">
                ${result.points_earned > 0 ? 
                    '<p class="text-success"><i class="fa fa-trophy"></i> Great job! You\'re earning points and leveling up!</p>' :
                    '<p class="text-info"><i class="fa fa-lightbulb-o"></i> Don\'t give up! Practice makes perfect!</p>'
                }
            </div>
        </div>
    `;
    
    $('#resultsContent').html(resultsHtml);
    $('#resultsModal').modal('show');
}

// Prevent accidental page leave during game
window.addEventListener('beforeunload', function(e) {
    if (gameStartTime && !$('#resultsModal').is(':visible')) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>

<style>
.timer-display {
    font-size: 18px;
    font-weight: bold;
    color: #3c8dbc;
}

.timer-display.text-danger {
    color: #dd4b39 !important;
}

.question-container {
    max-width: 800px;
    margin: 0 auto;
}

.question-text {
    font-size: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background-color: #f9f9f9;
    border-left: 4px solid #3c8dbc;
}

.options-container {
    margin-bottom: 20px;
}

.option-item {
    margin-bottom: 15px;
}

.option-label {
    display: block;
    padding: 15px 20px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.option-label:hover {
    background-color: #f5f5f5;
    border-color: #3c8dbc;
}

.option-label input[type="radio"] {
    margin-right: 15px;
}

.option-text {
    font-size: 16px;
}

.answer-feedback {
    margin-top: 20px;
    padding: 20px;
    border-radius: 5px;
    border-left: 4px solid;
}

.answer-feedback.correct {
    background-color: #dff0d8;
    border-color: #5cb85c;
}

.answer-feedback.incorrect {
    background-color: #f2dede;
    border-color: #d9534f;
}

.feedback-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.feedback-header i {
    font-size: 24px;
    margin-right: 10px;
}

.matching-container {
    max-width: 800px;
    margin: 0 auto;
}

.matching-pair {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.left-item {
    flex: 1;
    font-size: 16px;
    padding-right: 20px;
}

.right-item {
    flex: 1;
}

.correct-answer {
    border-color: #5cb85c !important;
    background-color: #dff0d8 !important;
}

.incorrect-answer {
    border-color: #d9534f !important;
    background-color: #f2dede !important;
}

.results-summary {
    padding: 20px;
}

.performance-badge {
    margin-bottom: 30px;
}

.score-circle {
    display: inline-block;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(45deg, #3c8dbc, #5cb85c);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px auto;
}

.score-percentage {
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.results-stats {
    margin-bottom: 30px;
}

.stat-item {
    text-align: center;
    padding: 20px;
}

.stat-item i {
    font-size: 30px;
    margin-bottom: 10px;
}

.stat-item h4 {
    font-size: 36px;
    margin: 10px 0 5px 0;
    font-weight: bold;
}

.stat-item p {
    color: #666;
    margin: 0;
}

.motivational-message {
    font-size: 16px;
    margin-top: 20px;
}

.progress-info {
    font-size: 16px;
    font-weight: bold;
}
</style>