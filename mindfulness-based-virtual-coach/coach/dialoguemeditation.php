<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  $question_id = 1;

  //if client is coming here from restart program, session has been set to 2 so they can start on question 2.
  if(isset($_SESSION['restart'])){
    $question_id = 2;
    unset($_SESSION['restart']);
  }

  //if condition for when the hidden variable $next_position is posted to this page
  if(isset($_POST['answer'])){
    $question_id = $_POST['answer'];
    $question_id = htmlentities($question_id);

    //set session dependant on the last answer chosen
    if($question_id == 7){
      $_SESSION['target'] = 20;
      $question_id = 3;
    }
    if($question_id == 8){
      $_SESSION['target'] = 30;
      $question_id = 3;
    }
    if($question_id == 9){
      $_SESSION['target'] = 40;
      $question_id = 3;
    }
    if($question_id == 10){
      $_SESSION['target'] = 50;
      $question_id = 3;
    }
    if($question_id == 11){
      $_SESSION['target'] = 60;
      $question_id = 3;
    }

    //action for when a user decides to create another wellbeing program
    if($question_id == 6){
      header('location:../program/createmeditationprogram.php');
    }

    //client has chosen to finish dialogue
    if($question_id == 5){
      header('location:../index.php');
    }

  }//End of answer conditions

  //Get questions and answers for display
  $stmt = $conn->prepare("SELECT dialogue_meditation_q.question, dialogue_meditation_a.next_position, dialogue_meditation_a.answer 
  FROM dialogue_meditation_a 
  INNER JOIN dialogue_meditation_q 
  ON dialogue_meditation_a.question_id = dialogue_meditation_q.id 
  WHERE dialogue_meditation_q.id = ?");
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

    <?php
      echo"
        <div class='myemojicontainer mt-3'>
          <img id='imgplace' class='mycoachimage' src='../img/createmeditationprogram/1.png'>
        </div>

        <div class='mytext mb-2'>$question</div>
      ";
      
        //display buttons with answers from the database
        while($stmt -> fetch()){
          echo"<form method='POST' action='dialoguemeditation.php'>";
          echo" <button type='submit' class='btn btn-primary mybtn my-2'>
                  <input type='hidden' value='$next_position' name='answer'>$answer";
          
          echo"</button>";

          echo"</form>";
        }
        $stmt->close();
       

      echo"</div>";

    ?>

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
      var image_position =<?php echo json_encode("../img/createmeditationprogram/". $question_id. ".png");?>;

      //replace image
      document.getElementById("imgplace").src = image_position;

    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>