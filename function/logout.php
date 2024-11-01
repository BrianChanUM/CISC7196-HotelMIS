<?php
    // Start the session
    session_start();

    // Destroy the session
    if(session_destroy()) {
        echo 'success';
    } else {
        echo 'fail';
    }
?>