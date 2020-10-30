<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  $stmt = $conn->prepare("SELECT rating_id FROM wellbeing_rating WHERE client_id = ?");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 

  $numrows = $stmt->num_rows;
  $stmt->close();

  if($numrows == 0){
    header('location:noratingcheck.php');
    exit();
  }

  $question_id = 1;

  //if client is coming here from restart program, use session variable to start them on question 2, skipping introduction question.
  if(isset($_SESSION['restart'])){
    $question_id = 2;
    unset($_SESSION['restart']);
  }

  //if condition for when an answer is posted to this page
  if(isset($_POST['answer'])){

    $question_id = $_POST['answer'];
    $question_id = htmlentities($question_id);

    //action for when a user decides to start new program
    if($question_id == 6){
      $_SESSION['recurring'] = 5;
      header('location:dialoguewellbeing.php');
    }

    //go to write own message
    if($question_id == 13){
      header('location:dialoguehelpmessage.php');
      exit();
    }

    //Go to write message with prewrote message: change program length
    if($question_id == 3){
      $_SESSION['help'] = 'I want help with the program length.';
      header('location:dialoguehelpmessage.php');
      exit();
    }

    //Go to write message with prewrote message: change target time
    if($question_id == 4){
      $_SESSION['help'] = 'I want help with the meditation time target.';
      header('location:dialoguehelpmessage.php');
      exit();
    }

    //Go to write message with prewrote message: restart program
    if($question_id == 5){
      $_SESSION['help'] = 'I want to restart the program.';
      header('location:dialoguehelpmessage.php');
      exit();
    }

    //If client chooses to change program length take them to that dialogue system
    if($question_id == 7){
      header('location:dialogueprogramlength.php');
    }
    //If client chooses to change target time take them to that dialogue system
    if($question_id == 8){
      header('location:dialoguetargettime.php');
    }
    //If client chooses to change restart program take them to that dialogue system
    if($question_id == 9){
      header('location:dialoguerestartprogram.php');
    }
    
    //Client choose to end dialogue
    if($question_id == 11){
      header('location:coach.php');
    }

  }//End of answer conditions

  //Get questions and answers for display
  $stmt1 = $conn->prepare("SELECT dialogue_recurring_q.question, dialogue_recurring_a.next_position, dialogue_recurring_a.answer 
  FROM dialogue_recurring_a 
  INNER JOIN dialogue_recurring_q
  ON dialogue_recurring_a.question_id = dialogue_recurring_q.id 
  WHERE dialogue_recurring_q.id = ?");

  $stmt1 -> bind_param("i", $question_id);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($question, $next_position, $answer);
  $stmt1 -> fetch();
  $stmt1 -> data_seek(0);

  //check if coach has enabled these interventions.
  $stmt2 = $conn->prepare("SELECT program_length, target_time, restart_program 
  FROM `coach_intervene` WHERE client_id = ?");
  $stmt2 -> bind_param("i", $client_id);
  $stmt2 -> execute();
  $stmt2 -> store_result();
  $stmt2 -> bind_result($program_length, $target_time, $restart_program);
  $stmt2 -> fetch();

  //check if there is an active program.
  $stmt3 = $conn->prepare("SELECT id FROM program WHERE client_id = ? AND end_date >= NOW()");
  $stmt3 -> bind_param("i", $client_id);
  $stmt3 -> execute();
  $stmt3 -> store_result();
  $numrows_program = $stmt3->num_rows;
  $stmt3->close();

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
        <img id='imgplace' src='../img/recurringdialogue/1.png' style='width:100px; height:100px; top:-170px;'>
      </div>

      <div class='mytext mb-2'><?php echo"$question"; ?></div>

      <?php
        
        //print out button options with answers from the database
        while($stmt1 -> fetch()){

          if($question_id == 1){
            //if there is an active wellbeing program, set button to hidden.
            if($next_position == 6 && $numrows_program == 1){
              $display = 'none';
            //If an intervention is disabled, set button to hidden.
            }elseif($next_position == 7 && $program_length == 0){
              $display = 'none';
            }elseif($next_position == 8 && $target_time == 0){
              $display = 'none';
            }elseif($next_position == 9 && $restart_program == 0){
              $display = 'none';
            }else{
              //if no wellbeing program, or an intervention is enabled, display the button
              $display = 'block';
            }
          }else{
            $display = 'block';
          }     
            echo"<form method='POST' action='dialoguerecurring.php'>";
            echo" <button type='submit' style='display: $display;' class='btn btn-primary mybtn my-2'>
                    <input type='hidden' value='$next_position' name='answer'>";
            echo"$answer";
            echo"</button>";

            echo"</form>";
        }
        $stmt1->close();
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
      var image_position =<?php echo json_encode("../img/recurringdialogue/". $question_id. ".png");?>;

      //replace image
      document.getElementById("imgplace").src = image_position;

    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>