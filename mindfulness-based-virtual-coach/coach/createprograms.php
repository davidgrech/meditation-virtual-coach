<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];

  //program type is posted from dialoguewellbeing.php when creating a new program. Program type is the program details id. This identifies which program it is.
  $program_type = $_SESSION['program_type'];

  $client_id = htmlentities($client_id);
  $program_type = htmlentities($program_type);

  //get the users wellbeing program length
  $stmtn = $conn->prepare("SELECT program_length FROM user_details WHERE id=?");
  $stmtn -> bind_param("i", $client_id);
  $stmtn -> execute();
  $stmtn -> store_result();
  $stmtn -> bind_result($program_length);
  $stmtn -> fetch();
  $stmtn->close();

  $interval = $program_length -1;

  //create a new wellbeing program with correct type
  $stmt = $conn->prepare("INSERT INTO `program` (`id`, `client_id`, `program_details_id`, `start_date`, `end_date`) VALUES (NULL, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL + ? DAY));");
  $stmt->bind_param('iii', $client_id, $program_type, $interval);
  $stmt->execute();
  $stmt->close();

  //select program id for the program just created
  $stmt1 = $conn->prepare("SELECT id, start_date FROM program WHERE client_id = ? AND program_details_id = ? AND start_date = CURDATE()");
  $stmt1 -> bind_param("ii", $client_id, $program_type);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($program_id, $start_date);
  $stmt1 -> fetch();
  $stmt1->close();

  $stmt2 = $conn->prepare("SELECT id FROM complete_exp_details WHERE client_id = ? AND date = ?");

  $stmt3 = $conn->prepare("INSERT INTO `program_complete` (`id`, `program_id`, `complete_exp_details_id`, `day`) VALUES (NULL, ?, ?, ?)");

  $day = 0;
  //Create wellbeing program details in the program_complete table for a custom number of days.
  while($day<$program_length){

    //subtract a day from a date in PHP reference(4)
    //create a new date for each iteration of the loop.
    $date = strtotime("$start_date +$day days");
    $date = date("Y-m-d", $date);

    $day++;

    /*get the database column complete_exp_details_id for the row that was just inserted into the database. 
    Repeat for every iteration of the loop*/
    $stmt2 -> bind_param("is", $client_id, $date);
    $stmt2 -> execute();
    $stmt2 -> store_result();
    $stmt2 -> bind_result($id);
    $stmt2 -> fetch();

    $stmt3->bind_param('iii', $program_id, $id, $day);
    $stmt3->execute();

  }
  $stmt2->close();
  $stmt3->close();

  header('location:coach.php');
  
?>