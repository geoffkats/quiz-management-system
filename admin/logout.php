<?php
require_once '../config/config.php';

// Only destroy admin-specific session variables
Session::set('admin_id', null);
Session::set('admin_username', null);
Session::set('is_admin', null);

// Redirect to admin login page
header('Location: /admin/login.php');
exit();
?>