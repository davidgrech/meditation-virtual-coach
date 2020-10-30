<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  //before going to viewprogram.php create a session that holds the program id so that the correct program is viewed
  if(isset($_POST['program_id'])){
    
    $program_id = $_POST['program_id'];
    $program_id = htmlentities($program_id);
    $_SESSION['view_program'] = $program_id;

    header('location:viewprogram.php');
  
  }else{
    header('location:coach.php');
  }

?>