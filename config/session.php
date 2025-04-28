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


function isOrganizer(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user_role'] === true;
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout();
}
