<?php
// includes/auth.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: dashboard.php"); // Or show an unauthorized page
        exit();
    }
}

function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}

function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
