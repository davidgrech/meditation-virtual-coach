<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  $question_id = 1;

  //Session from recurring dialogue is equal to 2. Go straight to question 2 from recurring dialogue
  if(isset($_SESSION['recurring'])){
    $question_id = $_SESSION['recurring'];
  }

  //After clicking an answer it is posted to this page in the following block of code
  if(isset($_POST['answer'])){

    $answer_post = $_POST['answer'];
    $question_id = htmlentities($answer_post);

  //check if client allready has an active wellbeing program
  if($question_id == 2||$question_id == 5){

    $stmt = $conn->prepare("SELECT start_date FROM program WHERE client_id = ? AND end_date >= NOW()");
    $stmt -> bind_param("s", $client_id);
    $stmt -> execute();
    $stmt -> store_result(); 
    $stmt -> bind_result($start_date);
    $stmt -> fetch();

    $numrows = $stmt->num_rows;
    $stmt->close();

    //if client allready has an active wellbeing program go to question_id 20. This prints out you allready have an active program.
    if($numrows> 0){
      $question_id = 20;
    }

  }

  if($question_id == 7){
    $_SESSION['program_type'] = 1;
  }

  if($question_id == 8){
    $_SESSION['program_type'] = 2;
  }

  if($question_id == 9){
    $_SESSION['program_type'] = 3;
  }

  if($question_id == 10){
    $_SESSION['program_type'] = 4;
  }

  if($question_id == 11){
    $_SESSION['program_type'] = 5;
  }

  if($question_id == 12){
    $_SESSION['program_type'] = 6;
  }

  if($question_id == 15||$question_id == 16||$question_id == 17||$question_id == 18||$question_id == 19){
    $question_id = 14;
  }

  //client has chosen to start new program, take them to createprograms.php
  if($question_id == 13){
    header('location:createprograms.php');
  }

  //client has chosen to finish dialogue
  if($question_id == 4){
    header('location:../meditate/meditate.php');
  }

  if($question_id == 6){

    //get wellbeing question rating averages for the last 14 days
    $stmt1 = $conn->prepare("SELECT AVG(question_one), AVG(question_two), AVG(question_three), 
    AVG(question_four), AVG(question_five), AVG(question_six) FROM wellbeing_rating WHERE client_id = ?"); //option for interval: AND date >= NOW() - INTERVAL 7 DAY
    $stmt1 -> bind_param("i", $client_id);
    $stmt1 -> execute();
    $result = $stmt1->get_result();

    $stmt1->close();

    //put average ratings into an array
    while ($row = $result->fetch_assoc()) {

      $average_array[0] = $row['AVG(question_one)'];
      $average_array[1] = $row['AVG(question_two)'];
      $average_array[2] = $row['AVG(question_three)'];
      $average_array[3] = $row['AVG(question_four)'];
      $average_array[4] = $row['AVG(question_five)'];
      $average_array[5] = $row['AVG(question_six)'];

    }

    //get the minimum average progress rating
    $min = min($average_array);

    //get the position of the minimum average in the array
    $min_keys = array_keys($average_array, $min);

    //convert minimum average position array to a string
    $min_keys = $min_keys[0];

    //assign the wellbeing rating average to its string name.
    $question_type_array[0] = htmlentities("Social Connectedness = ". round($average_array[0],2));
    $question_type_array[1] = htmlentities("Sleep Quality = ". round($average_array[1],2));
    $question_type_array[2] = htmlentities("Contentment = ". round($average_array[2],2));
    $question_type_array[3] = htmlentities("Resilience = ". round($average_array[3],2));
    $question_type_array[4] = htmlentities("Self Care = ". round($average_array[4],2));
    $question_type_array[5] = htmlentities("Non Reactivity = ". round($average_array[5],2));


  } //End of question 6 conditions

}//End of posted answer conditions

  //Get questions and answers for display
  $stmt2 = $conn->prepare("SELECT dialogue_wellbeing_q.question, dialogue_wellbeing_a.next_position, dialogue_wellbeing_a.answer 
  FROM dialogue_wellbeing_a 
  INNER JOIN dialogue_wellbeing_q
  ON dialogue_wellbeing_a.question_id = dialogue_wellbeing_q.id 
  WHERE dialogue_wellbeing_q.id = ?");

  $stmt2 -> bind_param("i", $question_id);
  $stmt2 -> execute();
  $stmt2 -> store_result(); 
  $stmt2 -> bind_result($question, $next_position, $answer);
  $stmt2 -> fetch();
  $stmt2 -> data_seek(0);

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
        <img id='imgplace' src='../img/createwellbeingprogram/1.png' style='width:100px; height:100px; top:-170px;'>
      </div>

      <div class='mytext mb-2'><?php echo"$question"; ?></div>

      <?php

        //if question equals 6 print out average rating details
        if(isset($_POST['answer'])){
          if($question_id == 6){

            $question_six = "The recommended wellbeing program will improve your lowest average score : $question_type_array[$min_keys]";
            echo"<p class='mytext'>$question_six<p>";
            echo"<div class='myalign'>";
            echo"<div class='myrightblock'>";

            $limit = COUNT($average_array);
            $counter = 0;
            while($counter<$limit){
              echo "<p class='mytext mb-0'>$question_type_array[$counter] </p>";
              $counter++;
            }
            echo"</div>";
            echo"</div>";

          }
        }//End of question 6 conditional print out
        
        //print out button options with answers from the database
        while($stmt2 -> fetch()){
          echo"<form method='POST' action='dialoguewellbeing.php'>";
          echo" <button type='submit' class='btn btn-primary mybtn my-2'>
                  <input type='hidden' value='";
                  
            //set next position after 6 dependant on which average rating is the lowest. If it is not question 6 don't change $next_position
            if(isset($_POST['answer'])){
              if($question_id == 6){
                echo $next_position + $min_keys;
              } else {
                echo $next_position;
              }
              }else{
                echo $next_position;
              }
                  
          echo"' name='answer'>$answer";
          
          echo"</button>";

          echo"</form>";
        }

        $stmt2->close();
        
        //the second stmt->fetch removes the first $question variable and sets a new one. If it is question 6 the $question variable must be set again.
        if(isset($_POST['answer'])){
          if($question_id == 6){
            $question = $question_six;
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
      var image_position =<?php echo json_encode("../img/createwellbeingprogram/". $question_id. ".png");?>;

      //replace image
      document.getElementById("imgplace").src = image_position;

    </script>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>