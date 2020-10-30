<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //check if client has completed a wellbeing rating, if not advise them to take one on noratingcheck.php
  $stmtg = $conn->prepare("SELECT rating_id FROM wellbeing_rating WHERE client_id = ?");
  $stmtg -> bind_param("i", $client_id);
  $stmtg -> execute();
  $stmtg -> store_result(); 
  $stmtg -> bind_result($rating_id); 
  $numrows = $stmtg->num_rows;
  $stmtg->close();

  if($numrows == 0){
    header('location:noratingcheck.php');
    exit();
   }

   $stmtc = $conn->prepare("SELECT id FROM program WHERE client_id = ?");
   $stmtc -> bind_param("i", $client_id);
   $stmtc -> execute();
   $stmtc -> store_result(); 
   $stmtc -> bind_result($id);
   $numrows1 = $stmtc->num_rows;
   $stmtc->close();
 
   //if client has never created a wellbeing program take them to create one
   if($numrows1 == 0){
     header('location:dialoguewellbeing.php');
     exit();
    }

  //find if client has ever messaged the coach
  $stmtm = $conn->prepare("SELECT message, date, time, author FROM help_request_message WHERE client_id = ? ORDER BY id DESC");
  $stmtm -> bind_param("i", $client_id);
  $stmtm -> execute();
  $stmtm -> store_result(); 
  $numrowsm = $stmtm->num_rows;
  $stmtm->close();

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('../navbar.php');?>

    <div id='mycontainer'>

      <?php

        //get all wellbeing program details for programs that are still active
        $stmtp = $conn->prepare("SELECT program.id, program.start_date, program.end_date, program_details.program_name, program_details.path, 
        SUM(complete_exp_details.program_complete_id)FROM program 
        INNER JOIN program_details ON program.program_details_id = program_details.id 
        INNER JOIN program_complete ON program.id = program_complete.program_id 
        INNER JOIN complete_exp_details ON program_complete.complete_exp_details_id = complete_exp_details.id 
        WHERE program.client_id = ? AND program.end_date >= NOW() GROUP BY program.id");
        $stmtp -> bind_param("i", $client_id);
        $stmtp -> execute();
        $stmtp -> store_result();
        $stmtp -> bind_result($pro_id, $start_date, $end_date, $pro_name, $path, $pro_complete);

        //get all wellbeing program details for programs that have expired
        $stmtp1 = $conn->prepare("SELECT program.id, program.start_date, program.end_date, program_details.program_name, program_details.path, 
        SUM(complete_exp_details.program_complete_id)FROM program 
        INNER JOIN program_details ON program.program_details_id = program_details.id 
        INNER JOIN program_complete ON program.id = program_complete.program_id 
        INNER JOIN complete_exp_details ON program_complete.complete_exp_details_id = complete_exp_details.id 
        WHERE program.client_id = ? AND program.end_date < NOW() GROUP BY program.id");
        $stmtp1 -> bind_param("i", $client_id);
        $stmtp1 -> execute();
        $stmtp1 -> store_result();
        $stmtp1 -> bind_result($pro_id, $start_date, $end_date, $pro_name, $path, $pro_complete);

        //display virtual coach with image change on mouse over
        echo"
          <div class='mycoach myemojicontainer my-4'>
            <a href='dialoguerecurring.php'>
              <img class='imgchange' src='../img/coachpage/big.png' style='width:100px; height:100px'>
            </a>

            ";

            echo"
              </div>
            ";
        echo"
          <h4 class='ml-3' style='margin-top: 300px;'>Wellbeing Programs</h4>
        ";

        //display active wellbeing programs
        while($stmtp -> fetch()){
          echo"
            <div class='row mt-5'>
              <div class='col-sm-2 myemojicontainer'>
                <form method='POST' action='createprogramsession.php'>
                  <input type='hidden' value='$pro_id' name='program_id'>
                  <input type='image' src='$path' border='0' alt='Submit' class='d-block' style='width:60px; height:60px;'>
                </form>
              </div>
              
              <div class='col-sm-6 text-center'>
                <p class='mb-0 font-weight-bold'>$pro_name</p>
                <p class='mb-0'><strong>Start:</strong> $start_date</p>
                <p class='mb-0'><strong>End:</strong> $end_date<p>
              </div>
              <div class='col-sm-4 text-center'>
              <form method='POST' action='createprogramsession.php'>
                <input type='hidden' value='$pro_id' name='program_id'>
                  <button type='submit' class='btn alert-primary mybtn my-2'>Active</button>
              </form>
              </div>
            </div>
          ";
        }
        $stmtp->close();

        //display complete wellbeing programs
        while($stmtp1 -> fetch()){
        echo"
          <div class='row mt-5'>
            <div class='col-sm-2 myemojicontainer'>
              <form method='POST' action='createprogramsession.php'>
                <input type='hidden' value='$pro_id' name='program_id'>
                <input type='image' src='$path' border='0' alt='Submit' class='d-block' style='width:60px; height:60px;'>
              </form>
            </div>
            <div class='col-sm-6 text-center'>
              <p class='mb-0 font-weight-bold'>$pro_name</p>
              <p class='mb-0'><strong>Start:</strong> $start_date</p>
              <p class='mb-0'><strong>End:</strong> $end_date<p>
            </div>
            <div class='col-sm-4 text-center'>
              <form method='POST' action='createprogramsession.php'>
                <input type='hidden' value='$pro_id' name='program_id'>
                <button type='submit' class='btn alert-success mybtn my-2'>Complete</button>
              </form>
            </div>
          </div>
        ";
        }
        $stmtp1->close();

      ?>

    </div>

    <hr class="featurette-divider my-5">

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  
    <script>

      //mouse over change image reference(33)
      $(document).ready(function () {
        $('.imgchange')
            .mouseover(function () {
            $(this).attr("src", "../img/coachpage/small.png");
        })
            .mouseout(function () {
            $(this).attr("src", "../img/coachpage/big.png");
        });
      });
    </script>

  </body>
</html>