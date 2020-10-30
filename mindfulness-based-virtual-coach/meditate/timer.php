<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  //if time is not set go back to meditate.php
  if(!isset($_SESSION['time'])){
    header('location:meditate.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //get client IP address reference(14,15)
  function get_client_ip() {
    $ip_address = '';
    if(isset($_SERVER['REMOTE_ADDR']))
        $ip_address = $_SERVER['REMOTE_ADDR'];
    else
        $ip_address = NULL;
    return $ip_address;
  }

  $ip_address = get_client_ip();
  $ip_address = htmlentities($ip_address);

  //get geolocation reference(19)
  require "WorldMap/autoload.php";

  use GeoIp2\Database\Reader;

  $reader = new Reader("WorldMap/worldmap.dat");
  $record = $reader->city($ip_address);

  //The geo location API gets the following details using the public IP address
  $latitude_complete = $record->location->latitude;
  $longitude_complete = $record->location->longitude;
  $city = $record->city->name;
  $country = $record->country->name;

  $seconds = $_SESSION['seconds'];
  $time = $_SESSION['time'];
  $time = htmlentities($time);
  $seconds = htmlentities($seconds);

  //create an interval in seconds
  $insert_time = $seconds + $time*60;
  //display time with seconds and minutes
  $display_time = $seconds/60 + $time;

  $latitude_complete = htmlentities($latitude_complete);
  $longitude_complete = htmlentities($longitude_complete);
  $city = htmlentities($city);
  $country = htmlentities($country);
  $city = $city.' '.$country;
  $length = ceil($time);

  //insert geo location data from IP address and length of time meditating into database. Client location can now be displayed on map for length of time meditating
  //inserting row into database with expiration time reference(18)
  $insert_sql = "INSERT INTO `coordinates` (`client_id`, `long`, `lat`, `meditation_time`, `length`, `city`) VALUES (?, ?, ?, NOW() + INTERVAL ? second, ?, ?)";
  $stmt = $conn->prepare($insert_sql);
  $stmt->bind_param("issiis", $client_id, $longitude_complete, $latitude_complete, $insert_time, $length, $city);
  $stmt->execute();
  $stmt->close();

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src="../js/jquery.simple.timer.js"></script>
    <link href='../css/mytimer.css' rel='stylesheet'>

    <?php
      //show hours if time is greater than or equal to 60 minutes
      if($time>=60){
        echo"
          <style>
          .jst-hours {
            display: inline;
          }
          </style>
        ";
      }
    ?>

    <title>Meditation Success</title>
  </head>
  <body>

    <div class='container'>

      <div class='center'>
        <span class='timer mytitle' data-minutes-left='<?php echo"$display_time"; ?>'></span>
      </div>
      <!--display timer-->
      <div class='centeroutput'>
        <p id='output'></p>
        <p id='output1'></p>
      </div>
      <!--when meditation is complete display save button-->
      <div class='centersave'>
        <form action='meditationcompletesession.php' method='post'>
          <button id='save' class='btn btn-outline-primary' type='submit' style='display:none;' value='<?php echo"$time"; ?>' name='completed'>Save</button>
        </form>
      </div>
      <!--display option to end meditation-->
      <div class='centernolog'>
        <a id='nolog' href='destroymeditation.php' class='btn btn-link' role='button' aria-pressed='true' style='display:black;'>End</a>
      <div>
    </div>

    <!--gong sound on finishing meditation-->
    <audio id="audio" src="http://dgrech01.web.eeecs.qub.ac.uk/project/audio/Metal_Gong-Dianakc-109711828.mp3" style='display:none;'></audio>

    <?php
      $location = 'silence.mp3';
      //the guided meditation string set to a session is used here to assign the correct path to the audio file
      if(isset($_SESSION['guided'])){

        $guided = $_SESSION['guided'];
        unset($_SESSION["guided"]);
        
        if($guided == 'bodyscan'){
          $location = '02-Body-Scan.mp3';
        }
        if($guided == 'sitting'){
          $location = '03-Sitting-Meditation.mp3';
        }
        if($guided == 'movement'){
          $location = 'JodyMardulaMovement.mp3';
        }
        if($guided == 'coping'){
          $location = '04-3-Minutes-Coping-with-Difficulty-Space.mp3';
        }
        if($guided == 'mountain'){
          $location = 'mountain.mp3';
        }
        if($guided == 'walking'){
          $location = 'walking.mp3';
        }

        $time = floor($time);

      }

      echo"
        <audio id='guided' src='http://dgrech01.web.eeecs.qub.ac.uk/project/audio/$location' style='display:none;'></audio>
      ";

      //output time in seconds if under a minute. Output time in mins if over 1 minute
      $time_type = '';
      if($time<1){
        $time_type = 'sec';
        $output = $seconds;
      } else {
        $time_type = 'min';
        $output = $time;
      }

    ?>

    <script>
      $(function(){
        $('#guided').get(0).play();
      $('.timer').startTimer({
        elementContainer: 'span',
        onComplete: function(){
          document.getElementById('output').innerHTML = '<?php echo"$output $time_type"; ?>';
          document.getElementById('output1').innerHTML = 'complete';
          //make button hidden reference(9)
          document.getElementById('save').style.display = 'block';
          document.getElementById('nolog').style.display = 'block';
          $('#audio').get(0).play();
        },
      });
    })
      
    </script>

    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>

