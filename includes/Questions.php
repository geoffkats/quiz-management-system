<?php
class Questions {
    private static $juniorQuestions = [
        [
            'id' => 1,
            'question_text' => 'Which block category is used to control when a script runs?',
            'option_a' => 'Motion',
            'option_b' => 'Events',
            'option_c' => 'Looks',
            'option_d' => 'Sound',
            'correct_answer' => 'B'
        ],
        [
            'id' => 2,
            'question_text' => 'What color are the Motion blocks in Scratch?',
            'option_a' => 'Blue',
            'option_b' => 'Purple',
            'option_c' => 'Green',
            'option_d' => 'Orange',
            'correct_answer' => 'A'
        ],
        [
            'id' => 3,
            'question_text' => 'Which block makes a sprite say something?',
            'option_a' => 'move 10 steps',
            'option_b' => 'say "Hello!" for 2 secs',
            'option_c' => 'play sound meow',
            'option_d' => 'change size by 10',
            'correct_answer' => 'B'
        ],
        [
            'id' => 4,
            'question_text' => 'Where do you go to choose a new sprite?',
            'option_a' => 'Code tab',
            'option_b' => 'Sounds tab',
            'option_c' => 'Costumes tab',
            'option_d' => 'Sprite library (bottom-right)',
            'correct_answer' => 'D'
        ],
        [
            'id' => 5,
            'question_text' => 'Which block makes a sprite move forward?',
            'option_a' => 'turn 15 degrees',
            'option_b' => 'move 10 steps',
            'option_c' => 'go to x: 0 y: 0',
            'option_d' => 'glide 1 secs to x: 100 y: 100',
            'correct_answer' => 'B'
        ],
        [
            'id' => 6,
            'question_text' => 'What does the "green flag" do?',
            'option_a' => 'Stops the project',
            'option_b' => 'Starts the project',
            'option_c' => 'Deletes a sprite',
            'option_d' => 'Changes the background',
            'correct_answer' => 'B'
        ],
        [
            'id' => 7,
            'question_text' => 'Which block changes a sprite\'s costume?',
            'option_a' => 'next costume',
            'option_b' => 'change size by 10',
            'option_c' => 'play sound pop',
            'option_d' => 'move 10 steps',
            'correct_answer' => 'A'
        ],
        [
            'id' => 8,
            'question_text' => 'Where can you change the background of your project?',
            'option_a' => 'Backdrop library (bottom-right)',
            'option_b' => 'Sound editor',
            'option_c' => 'Code blocks',
            'option_d' => 'Sprite list',
            'correct_answer' => 'A'
        ],
        [
            'id' => 9,
            'question_text' => 'Which block makes a sprite disappear?',
            'option_a' => 'show',
            'option_b' => 'hide',
            'option_c' => 'say "Hello"',
            'option_d' => 'change color effect by 25',
            'correct_answer' => 'B'
        ],
        [
            'id' => 10,
            'question_text' => 'What does the "forever" block do?',
            'option_a' => 'Runs code once',
            'option_b' => 'Repeats code forever',
            'option_c' => 'Stops the script',
            'option_d' => 'Changes the sprite\'s size',
            'correct_answer' => 'B'
        ],
        [
            'id' => 11,
            'question_text' => 'The "when green flag clicked" block starts a script.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'A'
        ],
        [
            'id' => 12,
            'question_text' => 'The "stop all" block only stops one sprite.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'B'
        ],
        [
            'id' => 13,
            'question_text' => 'You can record your own sounds in Scratch.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'A'
        ],
        [
            'id' => 14,
            'question_text' => 'The "pen" blocks are used for drawing.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'A'
        ],
        [
            'id' => 15,
            'question_text' => 'A sprite can only have one costume.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'B'
        ],
        [
            'id' => 16,
            'question_text' => 'The "wait 1 second" block pauses the script.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'A'
        ],
        [
            'id' => 17,
            'question_text' => 'Variables can store numbers and words.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'A'
        ],
        [
            'id' => 18,
            'question_text' => 'The "broadcast" block sends messages to other sprites.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Not specified',
            'option_d' => 'Sometimes',
            'correct_answer' => 'A'
        ],
        [
            'id' => 19,
            'question_text' => 'What block makes a sprite point in a specific direction?',
            'option_a' => 'point in direction',
            'option_b' => 'turn clockwise',
            'option_c' => 'move steps',
            'option_d' => 'change x by',
            'correct_answer' => 'A'
        ],
        [
            'id' => 20,
            'question_text' => 'How do you make a sprite bounce off the edge?',
            'option_a' => 'if on edge, bounce',
            'option_b' => 'move steps',
            'option_c' => 'turn degrees',
            'option_d' => 'point towards',
            'correct_answer' => 'A'
        ],
        [
            'id' => 21,
            'question_text' => 'Which block checks if two sprites are touching?',
            'option_a' => 'touching mouse-pointer?',
            'option_b' => 'touching (sprite)?',
            'option_c' => 'touching edge?',
            'option_d' => 'touching color?',
            'correct_answer' => 'B'
        ],
        [
            'id' => 22,
            'question_text' => 'What does the "repeat 10" block do?',
            'option_a' => 'Repeats code 10 times',
            'option_b' => 'Waits for 10 seconds',
            'option_c' => 'Moves 10 steps',
            'option_d' => 'Creates 10 clones',
            'correct_answer' => 'A'
        ],
        [
            'id' => 23,
            'question_text' => 'How do you make a sprite follow the mouse?',
            'option_a' => 'point towards mouse-pointer',
            'option_b' => 'go to mouse-pointer',
            'option_c' => 'move to mouse',
            'option_d' => 'glide to mouse',
            'correct_answer' => 'B'
        ],
        [
            'id' => 24,
            'question_text' => 'Which sensing block detects mouse clicks?',
            'option_a' => 'mouse down?',
            'option_b' => 'touching mouse?',
            'option_c' => 'mouse x',
            'option_d' => 'mouse y',
            'correct_answer' => 'A'
        ],
        [
            'id' => 25,
            'question_text' => 'What block shows text on the stage?',
            'option_a' => 'think',
            'option_b' => 'say',
            'option_c' => 'ask',
            'option_d' => 'write',
            'correct_answer' => 'B'
        ],
        [
            'id' => 26,
            'question_text' => 'How do you make a sprite appear?',
            'option_a' => 'show',
            'option_b' => 'unhide',
            'option_c' => 'appear',
            'option_d' => 'visible',
            'correct_answer' => 'A'
        ],
        [
            'id' => 27,
            'question_text' => 'Which block makes noise?',
            'option_a' => 'make sound',
            'option_b' => 'start sound',
            'option_c' => 'play sound',
            'option_d' => 'begin sound',
            'correct_answer' => 'C'
        ],
        [
            'id' => 28,
            'question_text' => 'What happens when sprites touch the edge?',
            'option_a' => 'They stop',
            'option_b' => 'They bounce',
            'option_c' => 'They hide',
            'option_d' => 'Nothing happens',
            'correct_answer' => 'D'
        ],
        [
            'id' => 29,
            'question_text' => 'Which block changes sprite size?',
            'option_a' => 'set size',
            'option_b' => 'change size',
            'option_c' => 'make bigger',
            'option_d' => 'grow',
            'correct_answer' => 'B'
        ],
        [
            'id' => 30,
            'question_text' => 'What color are the Sound blocks?',
            'option_a' => 'Purple',
            'option_b' => 'Green',
            'option_c' => 'Blue',
            'option_d' => 'Pink',
            'correct_answer' => 'A'
        ]
    ];

    private static $seniorQuestions = [
        [
            'id' => 1,
            'question_text' => 'What is the purpose of variables in programming?',
            'option_a' => 'To store data',
            'option_b' => 'To make sprites move',
            'option_c' => 'To play sounds',
            'option_d' => 'To change backgrounds',
            'correct_answer' => 'A'
        ],
        [
            'id' => 2,
            'question_text' => 'Which loop block repeats code a specific number of times?',
            'option_a' => 'forever',
            'option_b' => 'repeat until',
            'option_c' => 'repeat (10)',
            'option_d' => 'wait until',
            'correct_answer' => 'C'
        ],
        [
            'id' => 3,
            'question_text' => 'Which block is NOT a conditional statement?',
            'option_a' => 'if <> then',
            'option_b' => 'if <> then else',
            'option_c' => 'repeat until <>',
            'option_d' => 'forever',
            'correct_answer' => 'D'
        ],
        [
            'id' => 4,
            'question_text' => 'What happens if you nest a "forever" loop inside another "forever" loop?',
            'option_a' => 'The outer loop runs once',
            'option_b' => 'The inner loop never runs',
            'option_c' => 'It causes an infinite loop',
            'option_d' => 'Scratch gives an error',
            'correct_answer' => 'C'
        ],
        [
            'id' => 5,
            'question_text' => 'Which operator checks if two values are NOT equal?',
            'option_a' => '=',
            'option_b' => '>',
            'option_c' => '<',
            'option_d' => '≠',
            'correct_answer' => 'D'
        ],
        [
            'id' => 6,
            'question_text' => 'What does the "mod" operator do?',
            'option_a' => 'Multiplies two numbers',
            'option_b' => 'Finds the remainder after division',
            'option_c' => 'Rounds a number down',
            'option_d' => 'Converts text to numbers',
            'correct_answer' => 'B'
        ],
        [
            'id' => 7,
            'question_text' => 'Which block is used to detect keyboard input?',
            'option_a' => 'when [space] key pressed',
            'option_b' => 'key [space] pressed?',
            'option_c' => 'Both a and b',
            'option_d' => 'None of the above',
            'correct_answer' => 'C'
        ],
        [
            'id' => 8,
            'question_text' => 'What is the correct order of script execution?',
            'option_a' => 'Top to bottom',
            'option_b' => 'Random order',
            'option_c' => 'Bottom to top',
            'option_d' => 'Depends on the project',
            'correct_answer' => 'A'
        ],
        [
            'id' => 9,
            'question_text' => '"Change x by 10" and "set x to 10" do the same thing.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on x value',
            'correct_answer' => 'B'
        ],
        [
            'id' => 10,
            'question_text' => 'Clones inherit all properties of their parent sprite.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on settings',
            'correct_answer' => 'A'
        ],
        [
            'id' => 11,
            'question_text' => 'The "pen down" block draws only when the sprite moves.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on pen color',
            'correct_answer' => 'A'
        ],
        [
            'id' => 12,
            'question_text' => '"Repeat until" loops can replace "forever" loops.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on condition',
            'correct_answer' => 'B'
        ],
        [
            'id' => 13,
            'question_text' => 'Variables can only store numbers.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on project',
            'correct_answer' => 'B'
        ],
        [
            'id' => 14,
            'question_text' => '"When I receive" blocks can trigger without a broadcast.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on project',
            'correct_answer' => 'B'
        ],
        [
            'id' => 15,
            'question_text' => '"Timer" resets automatically when the project starts.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on settings',
            'correct_answer' => 'A'
        ],
        [
            'id' => 16,
            'question_text' => 'What is a custom block used for?',
            'option_a' => 'Creating new functions',
            'option_b' => 'Drawing shapes',
            'option_c' => 'Making sprites',
            'option_d' => 'Playing sounds',
            'correct_answer' => 'A'
        ],
        [
            'id' => 24,
            'question_text' => 'What does the "stop [this script]" block do?',
            'option_a' => 'Stops all scripts',
            'option_b' => 'Stops only the current script',
            'option_c' => 'Pauses the project',
            'option_d' => 'Deletes the sprite',
            'correct_answer' => 'B'
        ],
        [
            'id' => 25,
            'question_text' => 'What happens if you use "broadcast and wait"?',
            'option_a' => 'The script waits for all receivers to finish',
            'option_b' => 'The script runs faster',
            'option_c' => 'The message is not sent',
            'option_d' => 'It works the same as broadcast',
            'correct_answer' => 'A'
        ],
        [
            'id' => 26,
            'question_text' => '"Go to front" and "go back 1 layer" do the same thing.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on sprite position',
            'correct_answer' => 'B'
        ],
        [
            'id' => 27,
            'question_text' => '"Touching color" detects exact color matches only.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on color',
            'correct_answer' => 'A'
        ],
        [
            'id' => 28,
            'question_text' => '"Loudness" sensing works without a microphone.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Sometimes',
            'option_d' => 'Depends on device',
            'correct_answer' => 'B'
        ],
        [
            'id' => 29,
            'question_text' => 'What is the maximum value for sprite size?',
            'option_a' => '100%',
            'option_b' => '200%',
            'option_c' => '400%',
            'option_d' => 'No limit',
            'correct_answer' => 'D'
        ],
        [
            'id' => 30,
            'question_text' => 'Which block creates multiple copies of a sprite?',
            'option_a' => 'duplicate',
            'option_b' => 'clone',
            'option_c' => 'copy',
            'option_d' => 'create sprite',
            'correct_answer' => 'B'
        ]
    ];

    public static function getQuestions($division) {
        if ($division === 'JUNIOR') {
            return self::$juniorQuestions;
        }
        if ($division === 'SENIOR') {
            return self::$seniorQuestions;
        }
        return [];
    }

    public static function getRandomQuestions($division, $limit = 30) {
        $questions = self::getQuestions($division);
        if (empty($questions)) {
            return [];
        }
        shuffle($questions);
        return array_slice($questions, 0, $limit);
    }
}
