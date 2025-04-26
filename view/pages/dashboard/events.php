<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

if (isOrganizer()) {
    header("Location: /lama/view/pages/dashboard/organiser-events.php");
    exit();
} else {
    header("Location: /lama/view/pages/dashboard/attende-events.php");
    exit();
}
