<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
  }

  //the selected client's id is stored in a session
  if(isset($_SESSION['client_session'])){
    $client_id = $_SESSION['client_session'];
  }else{
    header('location:adminchooseclient.php');
  }

  $client_id = htmlentities($client_id);

  //get meditation program start date and daily meditation target
  $stmt = $conn->prepare("SELECT start_date FROM user_details INNER JOIN meditation_program ON user_details.id = meditation_program.client_id INNER JOIN meditation_complete ON meditation_program.id = meditation_complete.meditation_program_id WHERE user_details.id = ? GROUP BY meditation_program.id");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($start_date);
  $stmt -> fetch();
  $stmt -> data_seek(0);
  $stmt->close();

  //get date client started the meditation program
  $stmt1 = $conn->prepare("SELECT signed_up FROM user_details WHERE user_details.id = ?");
  $stmt1 -> bind_param("i", $client_id);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($signed_up);
  $stmt1 -> fetch();
  $stmt1->close();

  //get all time wellbeing rating averages
  $stmt2 = $conn->prepare("SELECT ROUND((AVG(question_one)), 1), ROUND((AVG(question_two)), 1), ROUND((AVG(question_three)), 1),
  ROUND((AVG(question_four)), 1), ROUND((AVG(question_five)), 1), ROUND((AVG(question_six)), 1) FROM wellbeing_rating WHERE client_id = ?");
  $stmt2 -> bind_param("i", $client_id);
  $stmt2 -> execute();
  $stmt2 -> store_result();
  $stmt2 -> bind_result($q_one_average, $q_two_average, $q_three_average, $q_four_average, $q_five_average, $q_six_average);
  $stmt2 -> fetch();
  $stmt2->close();

  //get meditation program details
  $stmt3 = $conn->prepare("SELECT complete_exp_details.program_complete_id, complete_exp_details.program_exp, 
  complete_exp_details.meditation_complete_id, complete_exp_details.meditation_exp, complete_exp_details.date 
  FROM complete_exp_details 
  WHERE complete_exp_details.client_id = ?
  AND complete_exp_details.date <= NOW()");

  $stmt3 -> bind_param("i", $client_id);
  $stmt3 -> execute();
  $stmt3 -> store_result();
  $numrows_meditation = $stmt3->num_rows;
  $stmt3 -> bind_result($program_complete, $program_exp, $meditation_complete, $meditation_exp, $date);

  $program_complete_total = 0;
  $meditation_complete_total = 0;

  while($stmt3 -> fetch()){

    //get total number of days wellbeing program complete
    if($program_complete=='1'){
      $program_complete_total++;
    }

    //get total number of days meditation target reached
    if($meditation_complete=='1'){
      $meditation_complete_total++;
    }

  }
  $stmt3->close();

  //avoid division by zero
  if($numrows_meditation > 0){
    //divide days program complete by how many days since starting the entire program
    $percentage_program_complete = round(($program_complete_total/$numrows_meditation)*100);
    //divide days meditation complete by how many days since meditation program start
    $percentage_meditation_complete = round(($meditation_complete_total/$numrows_meditation)*100);
  }else{
    $percentage_program_complete = 0;
    $percentage_meditation_complete = 0;
  }

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">
    <script src="https://unpkg.com/feather-icons"></script>

    <link href="../css/dashboard.css" rel="stylesheet">
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/myadmindash.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('admintopnavbar.php'); ?>

    <div class="container-fluid">
      <div class="row">

        <?php include('adminleftnavbar.php'); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
          <div id='mycontainer'>
            <div class="grid">
              <div class="row">
                    <div class='col-sm-6'>
                      <h3 class='mt-5 mx-auto'>Dashboard</h3>
                    </div>
                <div class="col-lg-12">
                  <h5 class="mt-5 mb-3">Meditation target reached</h5>
                </div>
                <div class='col-12'>
                  <?php
                    //depending on percentage of meditation target reached set the colour of the progress bar. Red = < 24%, orange = 25-49%, blue = 50-74%, green = 75-100%
                      $progress_bar_colour = '';
                    if($percentage_meditation_complete>=0&&$percentage_meditation_complete<=24){
                      $progress_bar_colour = 'danger';
                    }elseif($percentage_meditation_complete>=25&&$percentage_meditation_complete<=49){
                      $progress_bar_colour = 'warning';
                    }elseif($percentage_meditation_complete>=50&&$percentage_meditation_complete<=74){
                      $progress_bar_colour = 'info';
                    }elseif($percentage_meditation_complete>=75&&$percentage_meditation_complete<=100){
                      $progress_bar_colour = 'success';
                    }
                    $day_or_days = 'days';
                    if($meditation_complete_total==1){
                      $day_or_days = 'day';
                    }

                    //display progress bar with the correct colour, width and percentage complete
                    echo"
                      <div class='progress'>
                        <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width: $percentage_meditation_complete%' aria-valuemin='0' aria-valuemax='100'>$percentage_meditation_complete%</div>
                      </div>
                    ";
                    if($percentage_meditation_complete == 0) {
                      echo"
                        <div class='col-sm-12 pl-0'>
                          <p class='mt-3 mb-2'>Client has not reached any meditation targets since they signed up on $signed_up.</p>
                        </div>
                      ";
                    }else{
                      echo"
                        <div class='col-sm-12 pl-0'>
                          <p class='mt-3 mb-2'>$meditation_complete_total $day_or_days target reached out of $numrows_meditation since program start date $start_date.</p>
                        </div>
                      ";
                    }
                  ?>
                </div>
                <div class="col-lg-12">
                  <h5 class="mt-5 mb-3">Wellbeing program engagement</h5>
                </div>
                <div class='col-12'>
                  <?php
                    $progress_bar_colour = '';
                    if($percentage_program_complete>=0&&$percentage_program_complete<=24){
                      $progress_bar_colour = 'danger';
                    }elseif($percentage_program_complete>=25&&$percentage_program_complete<=49){
                      $progress_bar_colour = 'warning';
                    }elseif($percentage_program_complete>=50&&$percentage_program_complete<=74){
                      $progress_bar_colour = 'info';
                    }elseif($percentage_program_complete>=75&&$percentage_program_complete<=100){
                      $progress_bar_colour = 'success';
                    }
                    $day_or_days1 = 'days';
                    if($program_complete_total==1){
                      $day_or_days1 = 'day';
                    }
                    $day_or_days2 = 'days';
                    if($numrows_meditation==1){
                      $day_or_days2 = 'day';
                    }

                    echo"
                      <div class='progress'>
                        <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width: $percentage_program_complete%' aria-valuemin='0' aria-valuemax='100'>$percentage_program_complete%</div>
                      </div>
                    ";
                    if($percentage_program_complete == 0) {
                      echo"
                        <div class='col-sm-12 pl-0'>
                          <p class='mt-3 mb-2'>Client has not completed any wellbeing programs since they signed up on $signed_up.</p>
                        </div>
                      ";
                    }else{
                      echo"
                        <div class='col-sm-12 pl-0'>
                          <p class='mt-3 mb-2'>$program_complete_total $day_or_days1 wellbeing program complete out of $numrows_meditation $day_or_days2 since program start date $start_date.</p>
                        </div>
                      ";
                    }
                  ?>
                </div>
                <div class="col-lg-12">
                  <h5 class="mt-5">Wellbeing all time average ratings:</h5>
                </div>
                <div class='col-sm-12'>
                  <p class='mt-3 mb-2'><strong>1. I’ve been feeling close to other people</strong></p>
                </div>
                <div class='col-12'>
                  <?php
                    //$q_one_average is an average out of 1 - 9. Change the average into a percentage by multiplying by 11.11
                    $q_one_percent = $q_one_average*11.11;

                    //set a colour for the progress bar dependant on wellbeing rating average
                    $progress_bar_colour = '';
                    if($q_one_percent>=0&&$q_one_percent<=24.9){
                      $progress_bar_colour = 'danger';
                    }elseif($q_one_percent>=25&&$q_one_percent<=49.9){
                      $progress_bar_colour = 'warning';
                    }elseif($q_one_percent>=50&&$q_one_percent<=74.9){
                      $progress_bar_colour = 'info';
                    }elseif($q_one_percent>=75&&$q_one_percent<=100){
                      $progress_bar_colour = 'success';
                    }

                    //display the progress bar with appropriate colour, width and wellbeing rating average value
                    echo"
                      <div class='progress'>
                        <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width:$q_one_percent%' aria-valuemin='0' aria-valuemax='100'>$q_one_average</div>
                      </div>
                    ";
                  ?>
                </div>
                <div class='col-sm-12'>
                  <p class='mt-3 mb-2'><strong>2. I’ve been satisfied by my sleep</strong></p>
                </div>
                <div class='col-12 '>
                  <?php
                    $q_two_percent = $q_two_average*11.11;

                    $progress_bar_colour = '';
                    if($q_two_percent>=0&&$q_two_percent<=24.9){
                      $progress_bar_colour = 'danger';
                    }elseif($q_two_percent>=25&&$q_two_percent<=49.9){
                      $progress_bar_colour = 'warning';
                    }elseif($q_two_percent>=50&&$q_two_percent<=74.9){
                      $progress_bar_colour = 'info';
                    }elseif($q_two_percent>=75&&$q_two_percent<=100){
                      $progress_bar_colour = 'success';
                    }

                    echo"
                    <div class='progress'>
                      <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width:$q_two_percent%' aria-valuemin='0' aria-valuemax='100'>$q_two_average</div>
                    </div>
                    ";
                  ?>
                </div>
                <div class='col-sm-12'>
                  <p class='mt-3 mb-2'><strong>3. I am at peace with myself</strong></p>
                </div>
                <div class='col-12 '>
                  <?php
                    $q_three_percent = $q_three_average*11.11;

                    $progress_bar_colour = '';
                    if($q_three_percent>=0&&$q_three_percent<=24.9){
                      $progress_bar_colour = 'danger';
                    }elseif($q_three_percent>=25&&$q_three_percent<=49.9){
                      $progress_bar_colour = 'warning';
                    }elseif($q_three_percent>=50&&$q_three_percent<=74.9){
                      $progress_bar_colour = 'info';
                    }elseif($q_three_percent>=75&&$q_three_percent<=100){
                      $progress_bar_colour = 'success';
                    }

                    echo"
                      <div class='progress'>
                        <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width:$q_three_percent%' aria-valuemin='0' aria-valuemax='100'>$q_three_average</div>
                      </div>
                    ";
                  ?>
                </div>
                <div class='col-sm-12'>
                  <p class='mt-3 mb-2'><strong>4. I cope well with difficulties, pain or suffering</strong></p>
                </div>
                <div class='col-12 '>
                  <?php
                    $q_four_percent = $q_four_average*11.11;

                    $progress_bar_colour = '';
                    if($q_four_percent>=0&&$q_four_percent<=24.9){
                      $progress_bar_colour = 'danger';
                    }elseif($q_four_percent>=25&&$q_four_percent<=49.9){
                      $progress_bar_colour = 'warning';
                    }elseif($q_four_percent>=50&&$q_four_percent<=74.9){
                      $progress_bar_colour = 'info';
                    }elseif($q_four_percent>=75&&$q_four_percent<=100){
                      $progress_bar_colour = 'success';
                    }

                    echo"
                      <div class='progress'>
                        <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width:$q_four_percent%' aria-valuemin='0' aria-valuemax='100'>$q_four_average</div>
                      </div>
                    ";
                  ?>
                </div>
                <div class='col-sm-12'>
                  <p class='mt-3 mb-2'><strong>5. I take good care of myself</strong></p>
                </div>
                <div class='col-12 '>
                  <?php
                    $q_five_percent = $q_five_average*11.11;

                    $progress_bar_colour = '';
                    if($q_five_percent>=0&&$q_five_percent<=24.9){
                      $progress_bar_colour = 'danger';
                    }elseif($q_five_percent>=25&&$q_five_percent<=49.9){
                      $progress_bar_colour = 'warning';
                    }elseif($q_five_percent>=50&&$q_five_percent<=74.9){
                      $progress_bar_colour = 'info';
                    }elseif($q_five_percent>=75&&$q_five_percent<=100){
                      $progress_bar_colour = 'success';
                    }

                    echo"
                      <div class='progress'>
                        <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width:$q_five_percent%' aria-valuemin='0' aria-valuemax='100'>$q_five_average</div>
                      </div>
                    ";
                  ?>
                </div>
                <div class='col-sm-12'>
                  <p class='mt-3 mb-2'><strong>6. I have noticed my reactions without having to react to them</strong></p>
                </div>
                <div class='col-12 '>
                  <?php
                    $q_six_percent = $q_six_average*11.11;

                    $progress_bar_colour = '';
                    if($q_six_percent>=0&&$q_six_percent<=24.9){
                      $progress_bar_colour = 'danger';
                    }elseif($q_six_percent>=25&&$q_six_percent<=49.9){
                      $progress_bar_colour = 'warning';
                    }elseif($q_six_percent>=50&&$q_six_percent<=74.9){
                      $progress_bar_colour = 'info';
                    }elseif($q_six_percent>=75&&$q_six_percent<=100){
                      $progress_bar_colour = 'success';
                    }

                    echo"
                    <div class='progress'>
                      <div class='progress-bar bg-$progress_bar_colour' role='progressbar' style='width:$q_six_percent%' aria-valuemin='0' aria-valuemax='100'>$q_six_average</div>
                    </div>
                    ";
                  ?>
                </div>
                </div><!-- end of main row -->
              </div><!-- end of main row -->
            </div><!-- end of grid -->
          </div><!-- end of justify-content-center -->
          <hr class="featurette-divider my-5">
        </main><!-- end of the main section -->
      </div><!-- end of container fluid row -->
    </div><!-- end of container fluid -->

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="../js/dashboard.js"></script></body>
  </body>
</html>