<?php

    session_start();
    include('../connect.php');

    if(!isset($_SESSION['clientlogin'])){
        header('location:../signin.php');
    }

    $client_id = $_SESSION['clientlogin'];
    $client_id = htmlentities($client_id);

    //delete the last live meditation
    $stmt1 = $conn->prepare("DELETE FROM `coordinates` WHERE client_id = ? ORDER BY meditation_time DESC LIMIT 1");
    $stmt1 -> bind_param("i", $client_id);
    $stmt1 -> execute();
    $stmt1->close();

    //take client to meditation page
    header('location:meditate.php');

?>