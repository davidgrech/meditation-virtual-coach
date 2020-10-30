<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  if(!isset($_SESSION['view_program'])){
    header('location:progress.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  date_default_timezone_set('Europe/London');

  $date_now = date("Y-m-d");

  //select if program is complete
  $stmt = $conn->prepare("SELECT program_complete_id, total_exp FROM complete_exp_details WHERE client_id=? AND date = ?");
  $stmt -> bind_param("is", $client_id, $date_now);
  $stmt -> execute();
  $stmt -> store_result();
  $stmt -> bind_result($program_complete, $total_exp);
  $stmt -> fetch();
  $stmt->close();

  //if allready complete send the user back. If not complete change it to complete.
  if($program_complete == '1'){
    header('location:viewprogram.php');
  } elseif ($program_complete == '2'){
    $program_complete = '1';
  }

    //Get grand total, then update it with 15 experience points for completing the wellbeing program for today.
    $stmt2 = $conn->prepare("SELECT grand_total, level FROM client_level WHERE client_id = ?");
    $stmt2 -> bind_param("i", $client_id);
    $stmt2 -> execute();
    $stmt2 -> store_result();
    $stmt2 -> bind_result($grand_total, $level);
    $stmt2 -> fetch();
    $stmt2->close();

    $grand_total +=15;

    $stmt3 = $conn->prepare("UPDATE client_level SET grand_total = ? WHERE client_id = ?");
    $stmt3 -> bind_param("ii", $grand_total, $client_id);
    $stmt3 -> execute();
    $stmt3->close();

    /*the following code will assign the correct level to the client depending on their new grand total of experience points.
    levels are 1 to 99. If experience is more than 9899 the level will stay at 99.*/
    $stmt4 = $conn->prepare("UPDATE client_level SET level = ? WHERE client_id = ?");

    $level = 1;
    $min = 0;
    $max= 99;
    while($grand_total>=0 && $grand_total<=9899){

      if($grand_total >= $min && $grand_total <= $max){

        $stmt4 -> bind_param("ii", $level, $client_id);
        break;
      }
      $level++;
      $min +=100;
      $max +=100;
    }

    $stmt4 -> execute();
    $stmt4->close();

    //add previous total experience points to new experience points earned for completing program
    $total_exp += 15;

    //update the program to show it is complete with today's experience points
    $stmt5 = $conn->prepare("UPDATE complete_exp_details SET program_complete_id = ?, program_exp = 15, total_exp = ? WHERE client_id = ? AND date = ?");
    $stmt5 -> bind_param("siis", $program_complete, $total_exp, $client_id, $date_now);
    $stmt5 -> execute();
    $stmt5->close();

    header('location:viewprogram.php');

?>