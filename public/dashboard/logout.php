<?php
/**
 * Leadbusiness - Logout
 */

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

$auth = new Auth();
$auth->logout();

redirect('/dashboard/login.php?logout=1');
