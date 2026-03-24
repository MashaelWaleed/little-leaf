<?php
// 1. Start the session so PHP knows which "memory" to clear
session_start();

// 2. Remove all data from the session (user_id, user_fname, etc.)
session_unset();

// 3. Destroy the session entirely on the server
session_destroy();

// 4. Include config to use the BASE_URL for redirection
require_once('../config.php');

// 5. Send the user back to the home page
header("Location: " . BASE_URL . "index.php");
exit();
?>