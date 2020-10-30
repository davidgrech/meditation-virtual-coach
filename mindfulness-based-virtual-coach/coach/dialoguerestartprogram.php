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

    if($question_id == 4){

      //client has applied the restart program intervention. Delete all their previous program
      $stmt1 = $conn->prepare("DELETE FROM `meditation_complete` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();
      
      $stmt1 = $conn->prepare("DELETE FROM `meditation_program` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      //select every program id
      $stmtp = $conn->prepare("SELECT id FROM program WHERE client_id = ?");
      $stmtp -> bind_param("i", $client_id);
      $stmtp -> execute();
      $stmtp -> store_result();
      $stmtp -> bind_result($program_id);

      //delete all from program_complete details linked to every program id previously retrieved
      while($stmtp -> fetch()){
        $stmt1 = $conn->prepare("DELETE FROM `program_complete` WHERE program_id = ?");
        $stmt1 -> bind_param("i", $program_id);
        $stmt1 -> execute();
        $stmt1->close();
      }
      $stmtp->close();

      //delete all program details
      $stmt1 = $conn->prepare("DELETE FROM `program` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `client_level` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `wellbeing_rating` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `coordinates` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `complete_exp_details` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `coach_intervene` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `intervention_length` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $stmt1 = $conn->prepare("DELETE FROM `intervention_target` WHERE client_id = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      //add an entry to show a restart program intervention has been applied
      $stmt = $conn->prepare("INSERT INTO `intervention_restart` (`id`, `client_id`, `date`) VALUES (NULL, ?, CURDATE());");
      $stmt->bind_param('i', $client_id);
      $stmt->execute();
      $stmt->close();

      //Put wellbeing program length back to 7, if it was changed
      $stmt1 = $conn->prepare("UPDATE `user_details` SET `program_length` = 7 WHERE `id` = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      //send client to create new program, but start them on question 2
      $_SESSION['restart']=2;
      header('location:dialoguemeditation.php');

    }

    //client has chosen to finish dialogue
    if($question_id == 5){
      header('location:coach.php');
    }

  }//End of answer conditions

  //Get questions and answers for display
  $stmt = $conn->prepare("SELECT dialogue_restart_q.question, dialogue_restart_a.next_position, dialogue_restart_a.answer 
  FROM dialogue_restart_a 
  INNER JOIN dialogue_restart_q
  ON dialogue_restart_a.question_id = dialogue_restart_q.id 
  WHERE dialogue_restart_q.id = ?");
  $stmt -> bind_param("i", $question_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($question, $next_position, $answer);
  $stmt -> fetch();
  $stmt -> data_seek(0);

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
        <img id='imgplace' src='../img/restartprogram/1.png' style='width:100px; height:100px; top:-170px;'>
      </div>

      <div class='mytext mb-2'><?php echo"$question"; ?></div>

      <?php
        
        //print out button options with answers from the database
        while($stmt -> fetch()){
          echo"<form method='POST' action='dialoguerestartprogram.php'>";
          echo" <button type='submit' class='btn btn-primary mybtn my-2'>
                  <input type='hidden' value='$next_position' name='answer'>$answer";
          
          echo"</button>";

          echo"</form>";
        }
        $stmt->close();

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
      var image_position =<?php echo json_encode("../img/restartprogram/". $question_id. ".png");?>;

      //replace image
      document.getElementById("imgplace").src = image_position;

    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>