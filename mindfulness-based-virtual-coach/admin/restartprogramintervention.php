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

  $stmt = $conn->prepare("SELECT restart_program FROM coach_intervene WHERE client_id = ?");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($restart_program);
  $stmt -> fetch();
  $stmt->close();

  if($restart_program == 1){
    $restart_program = 0;
  } elseif($restart_program == 0){
    $restart_program = 1;
  }

  //enable restart program intervention
  $stmt1 = $conn->prepare("UPDATE `coach_intervene` SET `restart_program` = ? WHERE `client_id` = ?");
  $stmt1 -> bind_param("ii", $restart_program, $client_id);
  $stmt1 -> execute();
  $stmt1->close();

  header('location:admininterventions.php');
?>
