<?php
session_start();

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = array();
    }

    public static function isLoggedIn() {
        return self::get('participant_id') !== null;
    }

    public static function isAdmin() {
        return self::get('is_admin') === true;
    }

    public static function setAdmin($id, $username) {
        self::set('admin_id', $id);
        self::set('admin_username', $username);
        self::set('is_admin', true);
    }

    public static function checkAuth() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login.php');
            exit();
        }
    }

    public static function checkAdminAuth() {
        if (!self::isAdmin()) {
            header('Location: ' . ADMIN_URL . '/login.php');
            exit();
        }
    }

    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }

    public static function flashMessage($key) {
        $message = self::get($key);
        self::set($key, null);
        return $message;
    }
}

// Don't start session here as it's already handled in config.php
// Add any session-specific functions below
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}
?>