<?php
    //destroy all sessions
    session_start();
    session_destroy();
    header('location:index.php');
?>
