<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  $question_id = 1;

  //when client selected a new time target with the select box it is posted here and turned into $_POST['answer'] = 8
  if(isset($_POST['custom'])){
    $custom = $_POST['custom'];
    $custom = htmlentities($custom);
    $target = $custom;
    $_POST['answer'] = 8;
  }

  //if condition for when an answer is posted to this page
  if(isset($_POST['answer'])){

    $question_id = $_POST['answer'];
    $question_id = htmlentities($question_id);

    //client choose to create a new time target
    if($question_id == 8){

      //get previous target from the database
      $stmt = $conn->prepare("SELECT target FROM meditation_complete WHERE client_id = ? ORDER BY target ASC LIMIT 1");
      $stmt -> bind_param("i", $client_id);
      $stmt -> execute();
      $stmt -> store_result(); 
      $stmt -> bind_result($previous_target);
      $stmt -> fetch();
      $stmt->close();

      //store previous target in session variable
      $_SESSION['previous_target']=$previous_target;
      //store new target in session variable
      $_SESSION['new_target']=$target;

      $stmt1 = $conn->prepare("UPDATE meditation_complete INNER JOIN complete_exp_details 
      ON meditation_complete.complete_exp_details_id = complete_exp_details.id 
      SET target = ? 
      WHERE meditation_complete.client_id = ? 
      AND complete_exp_details.date >= CURDATE()");
      $stmt1 -> bind_param("ii", $target, $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      $question_id = 9;
    }//end of changing time target

    //client has chosen to finish dialogue
    if($question_id == 10){
      header('location:coach.php');
    }

    //client has chosen to finish dialogue with changes
    if($question_id == 11){

      $previous_target = $_SESSION['previous_target'];
      $new_target = $_SESSION['new_target'];

      //add an entry to show a program length intervention has been applied
      $stmt = $conn->prepare("INSERT INTO `intervention_target` (`id`, `client_id`, `date`, `target`, `previous_target`) VALUES (NULL, ?, CURDATE(), ?, ?);");
      $stmt->bind_param('iii', $client_id, $new_target, $previous_target);
      $stmt->execute();
      $stmt->close();

      //remove the option to change program length again, until human coach decides to change it again
      $stmt1 = $conn->prepare("UPDATE `coach_intervene` SET `target_time` = 0 WHERE `client_id` = ?");
      $stmt1 -> bind_param("i", $client_id);
      $stmt1 -> execute();
      $stmt1->close();

      header('location:coach.php');
    }

  }//End of answer conditions

  //Get questions and answers for display
  $stmt = $conn->prepare("SELECT dialogue_target_q.question, dialogue_target_a.next_position, dialogue_target_a.answer 
  FROM dialogue_target_a 
  INNER JOIN dialogue_target_q
  ON dialogue_target_a.question_id = dialogue_target_q.id 
  WHERE dialogue_target_q.id = ?");
  $stmt -> bind_param("i", $question_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($question, $next_position, $answer);
  $stmt -> fetch();
  $stmt -> data_seek(0);

  if($question_id == 9){
    //let the client know how many days the wellbeing program length was changed to
    $question = $question.' '.$target.' minutes. Once confirmed, you will not be able to change it again until your coach reviews your progress.';  
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
        $counter =2;
        while($stmt -> fetch()){

          if($question_id == 3){
            echo"<form method='POST' action='dialoguetargettime.php'>";
            echo "<label style='color:white'>Select a target:</label>";
            echo"<select class='form-control' name='custom'>
                    <option>5</option>
                    <option>10</option>
                    <option>15</option>
                    <option>20</option>
                    <option>25</option>
                    <option>30</option>
                    <option>35</option>
                    <option>40</option>
                    <option>45</option>
                    <option>50</option>
                    <option>55</option>
                    <option>60</option>
                  </select>";
            echo"<button type='submit' class='btn btn-primary mybtn my-2'>Submit";
            echo"</button>";
            echo"</form>";
          }else{
            echo"<form method='POST' action='dialoguetargettime.php'>";
            echo" <button type='submit' class='btn btn-primary mybtn my-2'>
                    <input type='hidden' value='$next_position' name='answer'>$answer";
            echo" </button>";
            echo"</form>";
          }
        }
        $stmt->close();

        //$question must be set again, becuase the last stmt->fetch removed it.
        if(isset($_POST['answer'])){
          if($question_id == 9){
            $question = $question.' '.$target.' minutes. Once confirmed, you will not be able to change it again until your coach reviews your progress.';
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