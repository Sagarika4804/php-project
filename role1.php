<?php
session_start();


if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}


$role = $_SESSION['role'] ?? 'user';

$roleRedirects = [
    'admin'  => 'admin.php',
    'editor' => 'editor.php',
    'user'   => 'user4.php'
];

if (array_key_exists($role, $roleRedirects)) {
    header("Location: " . $roleRedirects[$role]);
    exit;
} else {
    echo "⛔ Access Denied. Unknown role.";
    session_destroy();
    exit;
}
