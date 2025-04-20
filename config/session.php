<?php
session_start();

function checkSession()
{
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        header("Location: /lama/view/pages/auth/sign-in.php");
        exit();
    }
}

function logout()
{
    session_unset();
    session_destroy();
    header("Location: /lama/view/pages/auth/sign-in.php");
    exit();
}


function isSignedIn(): bool
{
    return isset($_SESSION);
}

// Handle logout action if requested via URL
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout();
}
