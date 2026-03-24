<?php
// 1. ABSOLUTE PATH (For the Server/PHP)
// __DIR__ gets the current folder of this file. 
// Use this for including files like nav.php or footer.php.
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/');
}

// 2. BASE URL (For the Browser/HTML)
// Use this for <a> tags, <img> tags, and <link> tags.
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/store/');

//3. Debug commands to use 
    // echo "<pre>"; // Makes the output easy to read
    // print_r($_SESSION); 
    // echo "</pre>";

    // Force PHP to show errors
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
    // echo "<script>console.log('PHP says: Redirect would happen here');</script>";   
    


}
?>