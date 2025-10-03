<?php
// config.php
session_start();

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = ''; // change if you set a MySQL password
$DB_NAME = 'sms_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}

function is_logged_admin() { return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'); }
function is_logged_teacher() { return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher'); }
function is_logged_student() { return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student'); }
?>
