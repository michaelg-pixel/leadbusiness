<?php
/**
 * Admin Logout
 */

session_start();

// Remember-Cookie löschen
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/', '', true, true);
}

// Session löschen
$_SESSION = [];
session_destroy();

// Redirect zum Login
header('Location: /admin/login.php');
exit;
