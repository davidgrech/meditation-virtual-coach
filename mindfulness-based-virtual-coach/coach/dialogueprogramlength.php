<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  $question_id = 1;

  //if condition for when an answer is posted to this page
  if(isset($_POST['answer'])){

    $question_id = $_POST['answer'];
    $question_id = htmlentities($question_id);

    //Client chooses how many days to set the wellbeing program to
    if($question_id == 4){
      $days = 2;
    }
    if($question_id == 5){
      $days = 3;
    }
    if($question_id == 6){
      $days = 4;
    }
    if($question_id == 7){
      $days = 5;
    }
    if($question_id == 8){
      $days = 6;
    }
    if($question_id == 12){
      $days = 7;
    }

    if($question_id == 4 || $question_id == 5 || $question_id == 6 || $question_id == 7 || $question_id == 8 || $question_id == 12){

      $stmt = $conn->prepare("SELECT program_length FROM user_details WHERE id = ?");
      $stmt -> bind_param("i", $client_id);
      $stmt -> execute();
      $stmt -> store_result(); 
      $stmt -> bind_result($program_length);
      $stmt -> fetch();
      $stmt->close();

      //store previous length in a session
      $_SESSION['previous_length']=$program_length;
      //store new length in a session
      $_SESSION['new_length']=$days;

      $stmt1 = $conn->prepare("UPDATE `user_details` SET `program_length` = ? WHERE `id` = ?");
      $stmt1 -> bind_param("ii", $days, $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $question_id = 9;
    }

    //client has chosen to finish dialogue
    if($question_id == 10){
      header('location:coach.php');
    }

    //client has chosen to finish dialogue with changes
    if($question_id == 11){

      $previous_length = $_SESSION['previous_length'];
      $new_length = $_SESSION['new_length'];

      //update program length for all dates from today
      $stmt1 = $conn->prepare("UPDATE meditation_complete INNER JOIN complete_exp_details 
      ON meditation_complete.complete_exp_details_id = complete_exp_details.id 
      SET program_length = ? 
      WHERE meditation_complete.client_id = ? 
      AND complete_exp_details.date >= CURDATE()");
      $stmt1 -> bind_param("ii", $new_length, $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      //add an entry to show a program length intervention has been applied
      $stmt = $conn->prepare("INSERT INTO `intervention_length` (`id`, `client_id`, `date`, `length`, `previous_length`) VALUES (NULL, ?, CURDATE(), ?, ?);");
      $stmt->bind_param('iii', $client_id, $new_length, $previous_length);
      $stmt->execute();
      $stmt->close();

      //remove the option to change program length again, until human coach enables another one
      $stmt1 = $conn->prepare("UPDATE `coach_intervene` SET `program_length` = 0 WHERE `client_id` = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      header('location:coach.php');
    }

  }//End of answer conditions

  //Get questions and answers for display
  $stmt = $conn->prepare("SELECT dialogue_length_q.question, dialogue_length_a.next_position, dialogue_length_a.answer 
  FROM dialogue_length_a 
  INNER JOIN dialogue_length_q
  ON dialogue_length_a.question_id = dialogue_length_q.id 
  WHERE dialogue_length_q.id = ?");

  $stmt -> bind_param("i", $question_id);
  $stmt -> execute();
  $stmt -> store_result(); 

  $stmt -> bind_result($question, $next_position, $answer);
  $stmt -> fetch();
  $stmt -> data_seek(0);

  if($question_id == 9){
    //let the client know how many days the wellbeing program length was changed to
    $question = $question.' '.$days.' days. Once confirmed, you will not be able to change it again until your coach reviews your progress.';
  }

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/mydialogue.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <div id='mycontainer'>

      <div class="myemojicontainer mt-3">
        <img id='imgplace' src='../img/programlength/1.png' style='width:100px; height:100px; top:-170px;'>
      </div>

      <div class='mytext mb-2'><?php echo"$question"; ?></div>

      <?php
        
        //print out button options with answers from the database
        while($stmt -> fetch()){
          echo"<form method='POST' action='dialogueprogramlength.php'>";
          echo" <button type='submit' class='btn btn-primary mybtn my-2'>
                  <input type='hidden' value='$next_position' name='answer'>$answer";
          
          echo"</button>";

          echo"</form>";
        }
        $stmt->close();

        //the last stmt->fetch removes the first $question variable and sets a new one, therefor $question must be set again.
        if(isset($_POST['answer'])){
          if($question_id == 9){
            $question = $question.' '.$days.' days. Once confirmed, you will not be able to change it again until your coach reviews your progress.';
          }
        }

      ?>

    </div>

    <script>
      //end speech from previous question
      window.speechSynthesis.cancel();

      //Turn question into speech
      var speech =<?php echo json_encode($question);?>;
      const msg = new SpeechSynthesisUtterance(speech);
      msg.volume = 1; // 0 to 1
      msg.rate = 1; // 0.1 to 10
      msg.pitch = 0; //0 to 2
      msg.lang = 'en-US';
      window.speechSynthesis.speak(msg);

      //set img position
      var image_position =<?php echo json_encode("../img/programlength/". $question_id. ".png");?>;

      //replace image
      document.getElementById("imgplace").src = image_position;

    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>