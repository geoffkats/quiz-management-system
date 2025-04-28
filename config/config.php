<?php
// Constants
define('APP_NAME', 'NSC Quiz Arena');
define('BASE_PATH', dirname(dirname(__FILE__)));

define('MINIMUM_PARTICIPANTS_PER_DIVISION', 2);
define('MAXIMUM_PARTICIPANTS_PER_DIVISION', 4);


// URL Constants
define('BASE_URL', '/NSC-Platform');
define('ADMIN_URL', BASE_URL . '/admin');

// Include required files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
