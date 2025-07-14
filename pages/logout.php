<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();

/* stop dashboard from showing out of cache */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* ✅ point back to the front-controller in *this* folder */
header('Location: /billtracker/index.php?page=login');
exit;
