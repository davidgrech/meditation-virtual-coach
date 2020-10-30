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

  //get intervention status for the three interventions from the database, program status is enabled or disabled. Enabled = 1, disabled = 0.
  $stmt = $conn->prepare("SELECT program_length, target_time, restart_program FROM coach_intervene WHERE client_id = ?");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($program_length, $target_time, $restart_program);
  $stmt -> fetch();
  $stmt->close();

  //get if program length intervention is enabled or disabled, set strings accordingly
  if($program_length == 1){
    $program_checked = 'checked';
    $program_enabled = 'Enabled';
  }else{
    $program_checked = '';
    $program_enabled = 'Disabled';
  }

  //get if target time intervention is enabled or disabled, set strings accordingly
  if($target_time == 1){
    $target_checked = 'checked';
    $target_enabled = 'Enabled';
  }else{
    $target_checked = '';
    $target_enabled = 'Disabled';
  }

  //get if restart program intervention is enabled or disabled, set strings accordingly
  if($restart_program == 1){
    $restart_checked = 'checked';
    $restart_enabled = 'Enabled';
  }else{
    $restart_checked = '';
    $restart_enabled = 'Disabled';
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
    <link href='../css/togglebutton.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('admintopnavbar.php'); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include('adminleftnavbar.php'); ?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
          <div id='mycontainer'>
            <div class="col-lg-12">
              <h3 class="mt-5">Interventions</h3>
            </div>
            <hr class="featurette-divider my-3">
            <div class="col-lg-12">
              <h5 class="mt-5">Target time change</h5>
            </div>
            <div class="col-lg-12">
              <p class="mt-4">If client meditation performance is weak give them the option to change the meditation target.</p>
            </div>
            <div class="col-lg-12">
              <label class="switch mt-3">
                <input type="checkbox" onclick='timetarget()' <?php echo"$target_checked"; ?> >
                <span class="slider round"></span>
              </label>
              <p class='mb-4'><strong><?php echo"$target_enabled"; ?></strong></p>
            </div>
            <?php
              //print out the details of any previous meditation time target interventions
              $stmt3 = $conn->prepare("SELECT date, target, previous_target FROM intervention_target WHERE client_id = ?");
              $stmt3 -> bind_param('i', $client_id);
              $stmt3 -> execute();
              $stmt3 -> store_result();
              $stmt3 -> bind_result($date, $target, $previous_target);
              $counter =1;
              while($stmt3 -> fetch()){
                echo"
                  <div class='col-lg-12'>
                    <p class='ml-3'><strong>$counter.</strong> Intervention accepted on $date. Target changed from $previous_target mins to $target mins.</p>
                  </div>
                ";
                $counter++;
              }
              $stmt3->close();
            ?>
            <hr class="featurette-divider my-3">
            <div class="col-lg-12">
              <h5 class="mt-5">Wellbeing program length change</h5>
            </div>
            <div class="col-lg-12">
              <p class="mt-4">If recommended program performance is week give them the option to change the length of recommended programs.</p>
            </div>
            <div class="col-lg-12">
              <label class="switch mt-3">
                <input type="checkbox" onclick='programlength()' <?php echo"$program_checked"; ?> >
                <span class="slider round"></span>
              </label>
              <p class='mb-4'><strong><?php echo"$program_enabled"; ?></strong></p>
            </div>
            <?php
              //print out the details of any previous wellbeing program length intervention
              $stmt3 = $conn->prepare("SELECT date, length, previous_length FROM intervention_length WHERE client_id = ?");
              $stmt3 -> bind_param('i', $client_id);
              $stmt3 -> execute();
              $stmt3 -> store_result();
              $stmt3 -> bind_result($date, $length, $previous_length);
              $counter = 1;
              while($stmt3 -> fetch()){
                echo"
                  <div class='col-lg-12'>
                    <p class='ml-3'><strong>$counter.</strong> Intervention accepted on $date. Wellbeing program length changed from $previous_length days to $length days.</p>
                  </div>
                ";
                $counter++;
              }
              $stmt3->close();
            ?>
            <hr class="featurette-divider my-3">
            <div class="col-lg-12">
              <h5 class="mt-5">Restart entire program</h5>
            </div>
            <div class="col-lg-12">
              <p class="mt-4">If client has very minimal engagement with the course give them the option to have a fresh start from the beginning.</p>
            </div>
            <div class="col-lg-12">
              <label class="switch mt-3">
                <input type="checkbox" onclick='restartprogram()' <?php echo"$restart_checked"; ?>>
                <span class="slider round"></span>
              </label>
              <p class='mb-4'><strong><?php echo"$restart_enabled"; ?></strong></p>
            </div>
            <?php
              //print out the details of any previous program restart interventions
              $stmt3 = $conn->prepare("SELECT date FROM intervention_restart WHERE client_id = ?");
              $stmt3 -> bind_param('i', $client_id);
              $stmt3 -> execute();
              $stmt3 -> store_result();
              $stmt3 -> bind_result($date);
              $counter = 1;
              while($stmt3 -> fetch()){
                echo"
                  <div class='col-lg-12'>
                    <p class='ml-3'><strong>$counter.</strong> Intervention accepted for a complete program restart on $date.</p>
                  </div>
                ";
                $counter++;
              }
              $stmt3->close();
            ?>
            <hr class="featurette-divider my-5">
          </div>
        </main><!-- end of the main section -->
      </div>
    </div>

    <script>
      //When intervention button is selected go to one of the following pages to update the database to enabled, or disabled
      function programlength() { 
        window.location.href = "programlengthintervention.php"; 
      }
      function timetarget() { 
        window.location.href = "targettimeintervention.php"; 
      }
      function restartprogram() { 
        window.location.href = "restartprogramintervention.php"; 
      }
    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="../js/dashboard.js"></script></body>
  </body>
</html>