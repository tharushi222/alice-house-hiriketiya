<?php
/**
 * Authentication Guards & Session Management
 * Alice House Hiriketiya
 */

require_once __DIR__ . '/functions.php';

/**
 * Require user login to access page
 */
function requireLogin($redirectBack = true) {
    if (!isLoggedIn()) {
        if ($redirectBack) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        setFlash('warning', 'Please log in to your account to continue.');
        redirect(BASE_URL . '/login.php');
    }
}

/**
 * Require admin access to view page
 */
function requireAdmin() {
    if (!isAdmin()) {
        setFlash('danger', 'Access denied. Administrator privileges required.');
        redirect(BASE_URL . '/login.php');
    }
}

/**
 * Log user in and initialize session
 */
function loginUser($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

/**
 * Log user out and destroy session
 */
function logoutUser() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
