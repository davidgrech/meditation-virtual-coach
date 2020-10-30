<?php

  session_start();

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
  }

  //page is redirected here from adminchooseclient.php. Create a session with the chosen client here. Use it to view their details.
  if(isset($_POST['chosen_id'])){

    $chosen_id = $_POST['chosen_id'];
    $chosen_id = htmlentities($chosen_id);
    $_SESSION['client_session']=$chosen_id;

    header('location:admindashboard.php');
  }

?>