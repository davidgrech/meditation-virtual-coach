<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //get client meditation details
  $stmt = $conn->prepare("SELECT time_total FROM meditation_complete WHERE client_id=?");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($med_time_total);
  $numcheck = $stmt->num_rows;
  
  //get total minutes meditating
  $total_mins = 0;
  while($stmt -> fetch()){
    $total_mins+=$med_time_total;
  }
  $stmt->close();

  //convert minutes to hours reference(25)
  function convertToHoursMins($time1, $format = '%02d:%02d') {
    if ($time1 < 1) {
        return;
    }
    $hours = floor($time1 / 60);
    $minutes = ($time1 % 60);
    return sprintf($format, $hours, $minutes);
  }
  $total_time_med = convertToHoursMins($total_mins, "%02d hours %02d minutes"); 

  $complete = '1';

  //get total days meditating
  $stmt1 = $conn->prepare("SELECT COUNT(DISTINCT(date)) FROM complete_exp_details WHERE client_id = ? AND meditation_complete_id = ?");
  $stmt1 -> bind_param("is", $client_id, $complete);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($total_days_med); 
  $stmt1 -> fetch();
  $stmt1->close();

  //get total meditation sessions
  $stmt2 = $conn->prepare("SELECT SUM(sessions) FROM meditation_complete WHERE client_id = ?");

  $stmt2 -> bind_param("i", $client_id);
  $stmt2 -> execute();
  $stmt2 -> store_result(); 
  $stmt2 -> bind_result($total_med_sessions); 
  $stmt2 -> fetch();
  $stmt2->close();

  //get most consecutive days meditating from database reference(26)
  $stmt3 = $conn->prepare("
    SELECT COUNT(*) max_streak 
      FROM
        ( SELECT x.*
              , CASE WHEN @prev = date - INTERVAL 1 DAY THEN @i:=@i ELSE @i:=@i+1 END i 
              , @prev:=date 
          FROM 
              ( SELECT DISTINCT date FROM complete_exp_details WHERE client_id = ? AND meditation_complete_id = ? ) x 
          JOIN 
              ( SELECT @prev:=null,@i:=0 ) vars 
          ORDER 
              BY date 
        ) a 
    GROUP 
      BY i 
    ORDER 
      BY max_streak DESC LIMIT 1");

  $stmt3 -> bind_param("is", $client_id, $complete);
  $stmt3 -> execute();
  $stmt3 -> store_result(); 
  $stmt3 -> bind_result($highest_med_streak); 
  $stmt3 -> fetch();
  $stmt3->close();

  //show a zero instead of nothing, if no meditations done
  if(is_null($total_time_med)){
    $total_time_med = 0;
  }

  if(is_null($highest_med_streak)){
    $highest_med_streak = 0;
  }

  if(is_null($total_med_sessions)){
    $total_med_sessions = 0;
  }

  //convert date/time format to time only and date only
  $stmt = $conn->prepare("SELECT length,
  DATE_FORMAT(meditation_time, '%Y-%m-%d') DATEONLY,
  DATE_FORMAT(meditation_time,'%H:%i:%s') TIMEONLY
  FROM coordinates WHERE client_id = ? ORDER BY meditation_time DESC");

  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result();
  $stmt -> bind_result($med_time_total, $date, $time);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" wfd-invisible="true"></script>
    <link href="../css/timeline.min.css" rel="stylesheet" />
    <link href='../css/mystyle.css' rel='stylesheet'>

    <title>Meditation Success</title>

  </head>
  <body style='background-color:#fafafa;'>

    <?php include('../navbar.php'); ?>

    <div id='mycontainerlarge'>
      <h3 class='myheading ml-3'>Charts</h3>
      <div class="container">
        <div class="row justify-content-center">
          <div class="d-inline">
            <form method="POST" action="timechart.php">
              <button type='submit' class='btn btn-success my-1 mx-3'>Meditation time</button>
            </form>
          </div>
          <div class="d-inline">
            <form method="POST" action="wellbeingchart.php">
              <button type='submit' class='btn btn-success my-1 mx-3'>Wellbeing ratings</button>
            </form>
          </div>
        </div>
        <hr class="featurette-divider my-3">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3 mydiv">
              <p>Total time meditating : <strong><?php echo"$total_time_med"; ?></strong></p>
              <p>Total meditation sessions : <strong><?php echo"$total_med_sessions"; ?></strong></p>
              <p>Total days meditating : <strong><?php echo"$total_days_med"; ?></strong></p>
              <p>Most consecutive days : <strong><?php echo"$highest_med_streak"; ?></strong></p>
            </div>
          </div>
        </div>
      </div><!--end of container div-->

      <hr class="featurette-divider mt-3">
      <!--display meditation time line. vertical timeline reference(20)-->
      <div class="timeline" data-vertical-start-position="right" data-vertical-trigger="50px">
        <div class="timeline">
          <div class="timeline__wrap">
            <div class="timeline__items">
              <?php     
                while($stmt -> fetch()){
                  echo"<div class='timeline__item py-1'>";
                    echo"<div class='timeline__content p-1'>";
                    $date = date("d-M-Y", strtotime($date));
                    $time = date('g:ia', strtotime($time));
                      echo"<p class='m-0' style='font-weight:bold'>$date</p>";
                      echo"<p class='m-0' style=''>$time</p>";
                      echo"<p class='m-0' style='font-weight:bold; color:#0089ff'>$med_time_total mins</p>";
                    echo"</div>";
                  echo"</div>";
                }
                $stmt->close();
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="../js/timeline.min.js"></script>

    <!-- vertical timeline reference(20) -->
    <script>
      $('.timeline').timeline({
        verticalStartPosition: 'right',
        verticalTrigger: '150px'
      });
    </script>
  </body>
</html>