<?php
// Initialize the session context frame
session_start();

/* ==========================================================================
   SERVER-SIDE SESSION CLEARING
   ========================================================================== */
// 1. Completely unbind all dynamic session variables in server memory
$_SESSION = array();

// 2. Destroy the physical session cookie tracking the user's browser anchor
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, // Forces immediate browser cookie expiration
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. Demolish the structural session container file on the server host
session_destroy();

/* ==========================================================================
   REDIRECTION INTERFACE (FIXED PATH)
   ========================================================================== */
// 4. Cleanly route the visitor back to your login file in the current directory
header("Location: user-login.php");
exit();
?>