<?php
declare(strict_types=1);

// Start or resume the session to access session data
session_start();

// Destroy all data registered to this session
session_destroy();

// Redirect the user to the login page after logging out
header('Location: index.php?page=login');

// Ensure no further code is executed after redirect
exit();
