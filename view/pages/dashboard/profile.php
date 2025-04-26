<?php
include "../../../config/session.php";


checkSession();
if (isOrganizer()) {
    header("Location: /lama/view/pages/dashboard/organizer-profile.php");
    exit();
} else {
    header("Location: /lama/view/pages/dashboard/attende-profile.php");
    exit();
}
