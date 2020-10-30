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

  //program id is selected on coach.php, turned into a session in createprogramsession.php then used here to view program
  $program_id = $_SESSION['view_program'];
  $program_id = htmlentities($program_id);

  //get all wellbeing program details
  $stmt = $conn->prepare("SELECT program_details.id, complete_exp_details.date, program_complete.day, complete_program_boolean.complete, program.start_date, program_details.program_name, program_details.program_description
  FROM program_complete
  INNER JOIN program
  ON program_complete.program_id = program.id
  INNER JOIN program_details
  ON program.program_details_id = program_details.id
  INNER JOIN complete_exp_details
  ON program_complete.complete_exp_details_id = complete_exp_details.id
  INNER JOIN complete_program_boolean
  ON complete_exp_details.program_complete_id = complete_program_boolean.id
  WHERE program_complete.program_id=? ORDER BY complete_exp_details.date ASC");
  $stmt -> bind_param("i", $program_id);
  $stmt -> execute();
  $stmt -> store_result();
  $stmt -> bind_result($program_details_id, $date, $day, $program_complete, $start_date, $program_name, $program_description);
  $stmt -> fetch();
  $stmt -> data_seek(0);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link rel="stylesheet" href="https://uicdn.toast.com/tui-grid/latest/tui-grid.css" />
    <script src="https://uicdn.toast.com/tui-grid/latest/tui-grid.js"></script>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/myviewprogram.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('../navbar.php');?>

    <div id='mycontainerlarge'>

      <h4 class='myheading pb-1'><?php echo"$program_name ";?>Program</h4>

      <?php echo"<p>$program_description</p>";?>
      
      <div id="grid"></div>

      <table style='text-align:center' class="table table-bordered">
        <thead>
          <tr style='background-color: #5B9BD5'>
            <th scope="col" style='color: white'>Date</th>
            <th scope="col" style='color: white'>Day</th>
            <th scope="col" style='color: white' colspan="2">Complete</th>
          </tr>
        </thead>

        <?php
          //default time zone must be set, otherwise the date created in PHP will be incorrect
          date_default_timezone_set('Europe/London');

          $todays_date = date("d-m-Y");
          //display wellbeing program details and an option to complete it for today
          while($stmt -> fetch()){
            $date = date("d-m-Y", strtotime($date));

            echo"<tbody>";
            if($date == $todays_date){
              echo"<tr style='background-color: #fff187'>";
              echo"
              <th style='width:33.33%;' scope='row'>$date</th>
                <td style='width:33.33%;'><strong>$day, Today</strong></td>
              ";
              if($program_complete == '&#x2705;'){
                echo"<td style='width:16.66%;'></td>";
              }else{
                echo"<td style='width:16.66%;'><input type='checkbox' style='margin-top:5px' onclick='updatecomplete()'></td>";
              }
              echo"
                <td style='width:16.66%;'>$program_complete</td>
              </tr>
            ";
            }else{
              echo"<tr>";
              echo"
              <th scope='row'><pre class='mypre'>$date</pre></th>
                <td>$day</td>
                <td></td>
                <td>$program_complete</td>
              </tr>
              ";
            }
          }
          $stmt->close();
        ?>
        </tbody>
      </table>
      <p>Click <a href="coach.php">here</a> to go back to the Coach page.</p>
    </div>

    <hr class="featurette-divider my-5">
    
    <script>
      //Client selects program for today to show it is complete. go to updateviewprogram.php to update the database.
      function updatecomplete() { 
        window.location.href = "updateviewprogram.php"; 
      }
    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>