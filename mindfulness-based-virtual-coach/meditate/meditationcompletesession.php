<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  /*the completed meditation time is posted here from timer.php. A session is created to hold the mediation time.
  This session is used on the progress.php page, then destroyed to avoid duplicates.*/
  
  if(isset($_POST['completed'])){
    $completed = $_POST['completed'];
    $completed = htmlentities($completed);
    $_SESSION['meditation_complete'] = $completed;
  }

  header('location:../program/progress.php');

?>