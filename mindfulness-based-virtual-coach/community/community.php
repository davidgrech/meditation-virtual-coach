<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //select all live mediations: meditations that time has not expired
  $details = "SELECT coordinates.long, coordinates.lat FROM coordinates WHERE coordinates.meditation_time > NOW()";
  $result = $conn->query($details);

  if(!$result){
    echo $conn->error;
  }

  $counter = 0;
  //create longitude and latitude arrays to display locations of each live meditation on the map
  while($row = $result->fetch_assoc()){
    $get_long = $row['long'];
    $get_lat = $row['lat'];
    $long_array[$counter] = $get_long;
    $lat_array[$counter] = $get_lat;
    $counter++;
  }

  //get total live meditators to display
  $live_meditators = mysqli_num_rows($result) -1;

  $details1 = "SELECT id FROM user_details WHERE authorisation = 0";
  $result1 = $conn->query($details1);

  if(!$result1){
    echo $conn->error;
  }
  //get total registered users to display
  $total_clients = mysqli_num_rows($result1);

  //get user details to display with the live meditation
  $stmt1 = $conn->prepare("SELECT user_details.path, user_details.user_name, coordinates.city, client_level.level, coordinates.length
  FROM coordinates 
  INNER JOIN user_details
  ON coordinates.client_id = user_details.id
  INNER JOIN client_level
  ON user_details.id = client_level.client_id
  WHERE coordinates.meditation_time > NOW();");
  $stmt1 -> execute();
  $stmt1 -> store_result();
  $stmt1 -> bind_result($path, $user_name, $city, $level, $length);
  $stmt1 -> fetch();
  $numrows1 = $stmt1->num_rows;
  $stmt1 -> data_seek(0);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/mycommunity.css' rel='stylesheet'>
    <title>Meditation Success</title>

  </head>
  <body>

    <?php include('../navbar.php'); ?>
    <div id='mycontainer'>
      <?php
        //show how many clients registered on the program and how many are meditating right now.
        echo"<h3 class='myheading ml-1'>Community</h3>
        <p style='text-align:center;'>Total meditators : $total_clients Meditating right now : $live_meditators</p>
        <p></p>

        ";
      ?>
      <!--Geo location map with vanilla dimensions-->
      <!--<canvas id="demo1" width="400" height="225"></canvas>-->
      <div id="canvas-wrap center">
        <canvas id="demo1" width="520" height="292.5"></canvas>
      </div>

      <?php
        if($numrows1>0){
          echo"
            <h5 class='my-4 ml-1'>Meditating Now</h5>
          ";
        }
        echo"
        <table class='table table-sm d-flex justify-content-center'>
          <tbody>
        ";
            while($stmt1 -> fetch()){
              $min_or_mins = 'minutes';
              if($length == 1){
                $min_or_mins = 'minute';
              }
              //display live meditation information
              echo"
                <tr>
                  <td style='border: none'><img class='myimg' src='$path''></td>
                  <td style='vertical-align: middle; border: none'><strong>$user_name</strong> is meditating in <strong>$city</strong> for <strong>$length $min_or_mins</strong></td>
                  <td style='vertical-align: middle; border: none'><span class='badge badge-pill ml-2 dot'>$level</span></td>
                </tr>
                ";
            }
            $stmt1->close();
          ?>

        </tbody>
      </table>

    </div>

    <hr class="featurette-divider my-5">

		<script src="WorldMap.js"></script>
    <script>
      //convert php array to javascript array reference(17)
      var js_long_array =<?php echo json_encode($long_array);?>;
      var js_lat_array =<?php echo json_encode($lat_array);?>;

      //create new marker on map reference(19)
			var map = new Map();
      map.draw(document.getElementById("demo1"));

      var locationLatitude = [];
      var locationLongitude = [];
      var locationX = [];
      var locationY = [];

      for (i = 0; i < <?php echo count($long_array); ?>; i++) {

        //dynamic variable inside loop reference(16)  
        locationLatitude[i] = js_lat_array[i];
        locationLongitude[i] = js_long_array[i];
        locationX[i] = ((locationLongitude[i] / 360) + 0.5) * document.getElementById("demo1").width;
        locationY[i] = (1 - ((locationLatitude[i] / 180) + 0.5)) * document.getElementById("demo1").height;
        
        document.getElementById("demo1").getContext("2d").beginPath();
        /*changing the last number in the next line of code changes the size of the marker on the map. If the system is scaled up the 
        map marker should be reduced in size so that many thoasands of live meditations can appear on the map*/
        document.getElementById("demo1").getContext("2d").arc(locationX[i], locationY[i], 2, 0, 1 * Math.PI);
        document.getElementById("demo1").getContext("2d").fillStyle = "#ffbf4f";
        document.getElementById("demo1").getContext("2d").fill();
      }

		</script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>