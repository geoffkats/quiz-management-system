document.addEventListener('DOMContentLoaded', () => {
    const questionText = document.getElementById('questionText');
    const optionsContainer = document.getElementById('optionsContainer');
    const feedbackContainer = document.getElementById('feedbackContainer');
    const feedbackMessage = document.getElementById('feedbackMessage');
    const countdown = document.getElementById('countdown');
    const questionNumber = document.getElementById('questionNumber');
    const progressFill = document.getElementById('progressFill');
    const timer = document.getElementById('timer');

    let currentQuestion = 0;
    let totalQuestions = 10;
    let startTime;
    let questionStartTime;
    let optionsLocked = false;

    const buttonClasses = {
        default: 'p-4 text-left border-2 border-primary-200 rounded-lg hover:bg-primary-50 transition-colors',
        correct: 'p-4 text-left border-2 bg-green-600 border-green-600 text-white hover:bg-green-700 transition-colors',
        incorrect: 'p-4 text-left border-2 bg-red-600 border-red-600 text-white hover:bg-red-700 transition-colors',
        disabled: 'p-4 text-left border-2 border-gray-200 text-gray-500 cursor-not-allowed'
    };

    // Initialize quiz
    const initializeQuiz = async () => {
        try {
            const response = await fetch('/NSC-Platform/api/get-question.php');
            if (!response.ok) throw new Error('Failed to start quiz');
            
            startTime = Date.now();
            updateTimer();
            setInterval(updateTimer, 1000);
            loadQuestion();
        } catch (error) {
            console.error('Error initializing quiz:', error);
        }
    };

    // Load question
    const loadQuestion = async () => {
        try {
            const response = await fetch('/NSC-Platform/api/get-question.php');
            if (!response.ok) throw new Error('Failed to load question');
            
            const data = await response.json();
            if (data.quizComplete) {
                window.location.href = '/NSC-Platform/results.php';
                return;
            }

            displayQuestion(data);
            currentQuestion++;
            questionStartTime = Date.now();
            optionsLocked = false;
            updateProgress();
        } catch (error) {
            console.error('Error loading question:', error);
        }
    };

    // Display question
    const displayQuestion = (data) => {
        questionText.textContent = data.question_text;
        questionNumber.textContent = `Question: ${currentQuestion + 1}/${totalQuestions}`;

        const options = [
            { letter: 'A', text: data.option_a },
            { letter: 'B', text: data.option_b },
            { letter: 'C', text: data.option_c },
            { letter: 'D', text: data.option_d }
        ];

        optionsContainer.innerHTML = options
            .map(option => `
                <button class="option-btn ${buttonClasses.default}" data-option="${option.letter}">
                    <span class="font-semibold">${option.letter}.</span> ${option.text}
                </button>
            `).join('');

        feedbackContainer.classList.add('hidden');
    };

    // Handle answer submission
    const submitAnswer = async (selectedOption) => {
        if (optionsLocked) return;
        optionsLocked = true;

        const responseTime = Math.floor((Date.now() - questionStartTime) / 1000);
        
        try {
            const response = await fetch('/NSC-Platform/api/submit-answer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    answer: selectedOption,
                    responseTime: responseTime
                })
            });

            const data = await response.json();
            showFeedback(data.correct, selectedOption, data.correctAnswer);
            
            // Start countdown for next question
            startCountdown();
        } catch (error) {
            console.error('Error submitting answer:', error);
        }
    };

    // Show feedback
    const showFeedback = (isCorrect, selected, correct) => {
        const selectedBtn = optionsContainer.querySelector(`[data-option="${selected}"]`);
        const correctBtn = optionsContainer.querySelector(`[data-option="${correct}"]`);

        // Update button classes
        selectedBtn.className = `option-btn ${isCorrect ? buttonClasses.correct : buttonClasses.incorrect}`;
        if (!isCorrect) {
            correctBtn.className = `option-btn ${buttonClasses.correct}`;
        }

        // Show feedback message
        feedbackContainer.classList.remove('hidden');
        feedbackMessage.className = `text-lg font-semibold mb-2 ${isCorrect ? 'text-green-600' : 'text-red-600'}`;
        feedbackMessage.textContent = isCorrect ? 'Correct!' : 'Incorrect';

        // Disable all options
        optionsContainer.querySelectorAll('.option-btn').forEach(btn => {
            if (btn !== selectedBtn && btn !== correctBtn) {
                btn.className = `option-btn ${buttonClasses.disabled}`;
            }
            btn.disabled = true;
        });
    };

    // Start countdown for next question
    const startCountdown = () => {
        let timeLeft = 3;
        countdown.textContent = timeLeft;

        const countdownInterval = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                loadQuestion();
            }
        }, 1000);
    };

    // Update progress bar
    const updateProgress = () => {
        const progress = (currentQuestion / totalQuestions) * 100;
        progressFill.style.width = `${progress}%`;
    };

    // Update timer
    const updateTimer = () => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        timer.textContent = `Time: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    // Event delegation for option buttons
    optionsContainer.addEventListener('click', (e) => {
        const button = e.target.closest('.option-btn');
        if (button && !optionsLocked) {
            submitAnswer(button.dataset.option);
        }
    });

    // Initialize the quiz
    initializeQuiz();
});