<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //check to make sure client has started a first meditation, if not navigate to noratingcheck.php to notify them that they must before viewing this page
  $stmt1 = $conn->prepare("SELECT rating_id FROM wellbeing_rating WHERE client_id = ?");
  $stmt1 -> bind_param("i", $client_id);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($rating_id); 
  $numrows = $stmt1->num_rows;
  $stmt1->close();

  if($numrows == 0){
    header('location:noratingcheck.php');
    exit();
   }

  //if a meditation was complete, the meditation_complete session was set and the following block of code executes
  if(isset($_SESSION['meditation_complete'])){

    $time_total =  $_SESSION['meditation_complete'];
    $time_total = htmlentities($time_total);

    //3 exp points for every minute meditating
    $exp_points = $time_total*3;

    //select meditation details from database
    $stmt2 = $conn->prepare("SELECT time_total, sessions, meditation_exp, total_exp, target FROM meditation_complete 
    INNER JOIN complete_exp_details
    ON meditation_complete.complete_exp_details_id = complete_exp_details.id
    WHERE meditation_complete.client_id = ? AND date = CURDATE()");
    $stmt2 -> bind_param("i", $client_id);
    $stmt2 -> execute();
    $stmt2 -> store_result();
    $stmt2 -> bind_result($previous_time_total, $previous_sessions, $previous_exp_points, $previous_total_exp, $target);
    $stmt2 -> fetch();
    $stmt2->close();

    //select grand total from database
    $stmt3 = $conn->prepare("SELECT grand_total FROM client_level WHERE client_id = ?");
    $stmt3 -> bind_param("i", $client_id);
    $stmt3 -> execute();
    $stmt3 -> store_result();
    $stmt3 -> bind_result($grand_total);
    $stmt3 -> fetch();
    $stmt3->close();

    //create new experience point grand total with experience points awarded for the last meditation
    $grand_total += $exp_points;

    //the following code will assign the correct level to the client depending on their new grand total of experience points
    //levels are 1 to 99. If experience points are more than 9899 the level will stay at 99
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

    //add previous experience total to new experience points 
    $total_exp = $exp_points + $previous_total_exp;

    //add previous experience points to new experience points awarded for this meditation only
    $med_exp_points = $exp_points + $previous_exp_points;

    //add previous total meditation minutes to new meditation just completed
    $time_total = $time_total + $previous_time_total;

    //add a session for each time meditating 
    $previous_sessions++;

    //If meditation time total is greater than daily time target insert a 1. 1 is complete, 2 is incomplete.
      $complete = '2';
    if($time_total>=$target){
      $complete = '1';
    }
    
    //update experience points for today and meditation experience points. Update today's time total, sessions, and if time target is complete.
    $stmt5 = $conn->prepare("UPDATE meditation_complete
      INNER JOIN complete_exp_details
      ON meditation_complete.complete_exp_details_id = complete_exp_details.id
      INNER JOIN complete_meditation_boolean
      ON complete_exp_details.meditation_complete_id = complete_meditation_boolean.id
      SET meditation_complete.time_total = ?, meditation_complete.sessions = ?, complete_exp_details.meditation_complete_id = ?, 
      complete_exp_details.meditation_exp = ?, complete_exp_details.total_exp = ?
      WHERE meditation_complete.client_id = ? AND complete_exp_details.date = CURDATE();
    ");

    $stmt5 -> bind_param("iisiii", $time_total, $previous_sessions, $complete, $med_exp_points, $total_exp, $client_id);
    $stmt5 -> execute();
    $stmt5->close();

    $stmt6 = $conn->prepare("UPDATE client_level SET grand_total = ? WHERE client_id = ?");
    $stmt6 -> bind_param("ii", $grand_total, $client_id);
    $stmt6 -> execute();
    $stmt6->close();

  }//end of meditation_complete block

  //get meditation program start date in order to display the current week when logging on
  $stmt7 = $conn->prepare("SELECT start_date FROM meditation_program WHERE client_id = ?");
  $stmt7 -> bind_param("i", $client_id);
  $stmt7 -> execute();
  $stmt7 -> store_result(); 
  $stmt7 -> bind_result($start_date);
  $stmt7 -> fetch();
  $stmt7->close();

  $start_date = new DateTime($start_date);
  $date_now = new DateTime();

  $interval = $start_date->diff($date_now);

  //convert the interval into an intger of days
  $elapsed = $interval->format('%a');

  //divide days elapsed by 7 to get weeks elapsed. Weeks elapsed is the current week of the eight week program
  $elapsed = $elapsed/7;

  //round down and add one to start at 1, instead of zero
  $week = floor($elapsed+1);

  //If a week is chosen, it is displayed
  if(isset($_POST['week'])){
    $week = $_POST['week'];
    $week = htmlentities($week);
  }

  /*After program is complete this stops the displayed week from increasing to a week that is not present.
  it is a program of maximum 8 weeks. If week is greater than 8, set week to 1*/
  if($week>8){
    $week = 1;
  }

  //get meditation program details
  $stmt8 = $conn->prepare("SELECT meditation_program.id, meditation_complete.time_total, meditation_program.start_date, meditation_program.end_date, 
    complete_exp_details.date, meditation_complete.day, meditation_complete.sessions, complete_program_boolean.complete, 
    complete_exp_details.program_exp, complete_meditation_boolean.complete, complete_exp_details.meditation_exp , complete_exp_details.total_exp,
    meditation_complete.target
    FROM meditation_complete 
    INNER JOIN meditation_program ON meditation_complete.meditation_program_id = meditation_program.id 
    INNER JOIN complete_exp_details ON meditation_complete.complete_exp_details_id = complete_exp_details.id
    INNER JOIN complete_meditation_boolean ON complete_exp_details.meditation_complete_id = complete_meditation_boolean.id
    INNER JOIN complete_program_boolean ON complete_exp_details.program_complete_id = complete_program_boolean.id
    WHERE meditation_program.client_id = ? AND meditation_complete.week_level = ? ORDER BY date
  ");

  $stmt8 -> bind_param("ii", $client_id, $week);
  $stmt8 -> execute();
  $stmt8 -> store_result(); 
  $stmt8 -> bind_result($program_id, $time, $start_date, $end_date, $date, $day, $sessions, $program_complete, $program_exp, $meditation_complete, $meditation_exp, $total_exp, $target);
  $stmt8 -> fetch();
  $stmt8 -> data_seek(0);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/myprogress.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('../navbar.php'); ?>

    <div id='mycontainerlarger'>

      <h3 class='myheading ml-2'>Progress</h3>

      <?php 
        //display user name and level
        $stmt11 = $conn->prepare("SELECT grand_total, level FROM client_level WHERE client_id = ?");
        $stmt11 -> bind_param("i", $client_id);
        $stmt11 -> execute();
        $stmt11 -> store_result();
        $stmt11 -> bind_result($grand_total, $level1);
        $stmt11 -> fetch();
        $stmt11->close();

        $remainder = $grand_total%100;
        $incomplete = 100 - $remainder;
        $next_level = $level1 +1;
        //display experience point grand total, current level and experience points needed to reach the next level
        echo"
          <div class='container'>
            <div class='row px-0'>
              <p class='mypre mx-auto'>Level  <span class='badge badge-pill dot'>$level1</span>  Exp $grand_total</p>
            </div>
        ";
        if(isset($_SESSION['meditation_complete'])){
          floor($exp_points);
          echo"
            <div class='row px-0'>
              <p class='mypre mx-auto' style='margin-top:12px'>Congratulations on completing your meditation. $exp_points exp points awarded!</p>
            </div>
          ";
        }
        unset($_SESSION["meditation_complete"]);
        echo"
          <div class='row px-0'>
            <p class='mypre mx-auto' style='margin-top:12px'>$incomplete Exp to reach level $next_level</p>
          </div>
        ";
        echo"
            <div id='mycontainerlarge' class='progress mt-3 mb-5'>
              <div class='progress-bar bg-primary' role='progressbar' style='width: $remainder%' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100'>$remainder Exp</div>
            </div>
          </div>
        ";
      ?>
      <!--choose a week to view-->
      <form class='form-group my-5' action='progress.php' method="post">
        <div class='container'>
          <div class='row'>
            <div class='d-inline' style='width: 135px'>
              <p><pre class='mypre ml-2'>Choose a week:</pre></p>
            </div>
            <div class='d-inline ml-2' style='width: 90px'>
              <select type='text' class='form-control mt-2 myselect' style='width:80px; padding-left:10px' name='week'>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
                <option>7</option>
                <option>8</option>
              </select>
            </div>
            <div class='d-inline ml-2' style='width: 90px'>
              <button class='btn btn-success my-2' type='submit' >Submit</button>
            </div>
          </div>
        </div>
      </form>

      <?php echo"<h4 class='mb-3 ml-2'>Week $week</h4>"; ?>
      
      <table class="table table-bordered text-center mytabletop">
        <thead>
          <tr style='background-color: #5B9BD5'>
            <th class='mytable px-1 py-1' style='width:25%; color: white' scope="col">Date</th>
            <th class='mytable px-1 py-1' style='width:25%; color: white' scope="col">Day</th>
            <th class='mytable px-1 py-1' style='width:25%; color: white' scope="col">Sessions</th>
            <th class='mytable px-1 py-1' style='width:25%; color: white' scope="col">Minutes</th>
          </tr>
        </thead>

      <?php
        $counter =1;
        //display eight week program
        while($stmt8 -> fetch()){
          $date = date("d-m-Y", strtotime($date));
          echo "
            <tbody>
              <tr style=''>
                <th class='px-1 py-1' scope='row'>$date</th><pre class='mypre'></pre>
                <td class='px-1 py-1' >$day</td>
                <td class='px-1 py-1' >$sessions</td>
                <td class='px-1 py-1' >$time/$target</td>
              </tr>
          ";
        }
        
        ?>

        </tbody>
      </table>

      <h5 class='mt-5 mb-4'>Experience Points</h5>

      <table class="table table-bordered text-center mytabletop">
        <thead>
          <tr style='background-color: #5B9BD5'>
            <th class='mytable py-1 px-1' style='width:25%; color: white' scope="col">Date</th>
            <th class='mytable py-1 px-1' style='width:25%; color: white' scope="col">Meditation</th>
            <th class='mytable py-1 px-1' style='width:25%; color: white' scope="col">Wellbeing</th>
            <th class='mytable py-1 px-1' style='width:25%; color: white' scope="col">Total</th>
          </tr>
        </thead>

      <?php
        //display experience points for eight week program
        $stmt8 -> data_seek(0);
        while($stmt8 -> fetch()){
          $date = date("d-m-Y", strtotime($date));

          echo "
            <tbody>
              <tr>
              <th class='px-1 py-1' scope='row'>$date</th><pre class='mypre'></pre>
          ";
          //if experience points equal 0 do not show them
          if($meditation_exp >0){
              echo"<td class='px-1 py-1' >$meditation_exp</td>";
          }else{
              echo"<td class='px-1 py-1'>0</td>";
          }
          if($program_exp >0){
            echo"<td class='px-1 py-1'>$program_exp</td>";
          }else{
            echo"<td class='px-1 py-1'>0</td>";
          }
          if($total_exp >0){
              echo"<td class='px-1 py-1'>$total_exp</td>";
          }else{
              echo"<td class='px-1 py-1'>0</td>";
          }
          echo"</tr>";
        }
        $stmt8->close();
        ?>

        </tbody>
      </table>

    </div><!--end of div mycontainer-->
    <hr class="featurette-divider my-5">
    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>
