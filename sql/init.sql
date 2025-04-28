-- Create administrators table
CREATE TABLE IF NOT EXISTS administrators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    division ENUM('JUNIOR', 'SENIOR', 'BOTH') NOT NULL DEFAULT 'BOTH',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create participants table
CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    participant_code VARCHAR(8) UNIQUE NOT NULL,
    division ENUM('JUNIOR', 'SENIOR') NOT NULL,
    ready BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create questions table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NOT NULL,
    option_d TEXT NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    division ENUM('JUNIOR', 'SENIOR') NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create quiz_sessions table
CREATE TABLE IF NOT EXISTS quiz_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    division ENUM('JUNIOR', 'SENIOR') NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP NULL
);

-- Create participant_answers table
CREATE TABLE IF NOT EXISTS participant_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer CHAR(1),
    is_correct BOOLEAN,
    response_time INT, -- in seconds
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Create quiz_results table
CREATE TABLE IF NOT EXISTS quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    quiz_session_id INT NOT NULL,
    division ENUM('JUNIOR', 'SENIOR') NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    total_time INT NOT NULL, -- in seconds
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (quiz_session_id) REFERENCES quiz_sessions(id)
);

-- Create indices for better performance
CREATE INDEX idx_participant_code ON participants(participant_code);
CREATE INDEX idx_division ON participants(division);
CREATE INDEX idx_quiz_session_division ON quiz_sessions(division, active);
CREATE INDEX idx_participant_answers ON participant_answers(participant_id, question_id);
CREATE INDEX idx_quiz_results ON quiz_results(participant_id, quiz_session_id);

-- Insert default categories
INSERT IGNORE INTO categories (name, description, division) VALUES
('Science', 'General science questions including physics, chemistry, and biology', 'BOTH'),
('Mathematics', 'Mathematics questions covering algebra, geometry, and arithmetic', 'BOTH'),
('History', 'Historical events, figures, and developments', 'BOTH'),
('Geography', 'World geography, maps, and geological features', 'BOTH'),
('Current Events', 'Recent world events and developments', 'SENIOR'),
('Literature', 'Books, authors, and literary works', 'BOTH'),
('Technology', 'Computer science, internet, and modern technology', 'SENIOR'),
('General Knowledge', 'Miscellaneous knowledge questions', 'BOTH');