<?php
/**
 * Logout Handler
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/includes/auth.php';

logoutUser();
setFlash('info', 'You have been signed out successfully.');
redirect(BASE_URL . '/login.php');
