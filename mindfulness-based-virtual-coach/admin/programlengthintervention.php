<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
  }

  if(isset($_SESSION['client_session'])){
    $client_id = $_SESSION['client_session'];
  }else{
    header('location:adminchooseclient.php');
  }

  $stmt = $conn->prepare("SELECT program_length FROM coach_intervene WHERE client_id = ?");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($program_length);
  $stmt -> fetch();
  $stmt->close();

  if($program_length == 1){
    $program_length = 0;
  } elseif($program_length == 0){
    $program_length = 1;
  }

  //enable change program length intervention
  $stmt1 = $conn->prepare("UPDATE `coach_intervene` SET `program_length` = ? WHERE `client_id` = ?");
  $stmt1 -> bind_param("ii", $program_length, $client_id);
  $stmt1 -> execute();
  $stmt1->close();

  header('location:admininterventions.php');
?>
