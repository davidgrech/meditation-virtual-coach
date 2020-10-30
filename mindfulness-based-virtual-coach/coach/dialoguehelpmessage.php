<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //create session variable for when client goes back to previous dialogue. They will go straight to question 10 the last question to end dialogue.
  $_SESSION['go_back'] = 10;

  if(isset($_SESSION['help'])){
    $help_message = $_SESSION['help'];
  }

  //the message the client wrote is posted to this page
  if(isset($_POST['message'])){

    $stmt = $conn->prepare("SELECT first_name, second_name FROM user_details WHERE id = ?");
    $stmt -> bind_param("i", $client_id);
    $stmt -> execute();
    $stmt -> store_result(); 
    $stmt -> bind_result($first_name, $second_name);
    $stmt -> fetch();
    $stmt->close();

    $name = $first_name.' '.$second_name;

    $message = $_POST['message'];
    $message = htmlentities($message);

    //the message is inserted into the database
    $stmt1 = $conn->prepare("INSERT INTO help_request_message (`id`, `client_id`, `message`, `date`, `time`, `author`, `read`) VALUES (NULL, ?, ?, CURDATE(), CURRENT_TIME(), ?, 1);");
    $stmt1->bind_param('iss', $client_id, $message, $name);
    $stmt1->execute();
    $stmt1->close();

    header("Refresh:0");

  }

  //Get all messages for the logged in client from the database
  $stmt2 = $conn->prepare("SELECT message, date, time, author FROM help_request_message WHERE client_id = ? ORDER BY id DESC");
  $stmt2 -> bind_param("i", $client_id);
  $stmt2 -> execute();
  $stmt2 -> store_result(); 
  $stmt2 -> bind_result($message, $date, $time, $author);

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
    
    <div class='px-4' id='mycontainermessages'>

      <h4 class='my-3 text-center'>Messages</h4>

      <?php

        echo"
          <div class='card mychatcontainer'>
            <div  class= 'mychatwindow'>
        ";
            //display all messages
            while($stmt2 -> fetch()){

              $newdate = date("d-m-Y", strtotime($date));
              $newtime = date('g:ia', strtotime($time));
            
              echo"<div class='card mychatmessage'>";
              echo"<h6><strong>$author</strong></h6>";
              echo"<p>$message</p>";
              echo"<p><strong>$newtime $newdate</strong></p>";
              echo"</div>
                  <br>
                  ";
            }
            $stmt2->close();
        echo"
          </div>
        </div>
        ";
      ?>
      <!--display option to write and send message-->
      <form method='POST' action='dialoguehelpmessage.php'>
        <div class="form-group">
          <textarea class="mt-5 form-control mytextarea" placeholder='Type message (max 255 characters)' rows="2" name='message'><?php if(isset($_SESSION['help'])){echo"$help_message";}?></textarea>
        </div>
        <input type='hidden' value='12' name='sent'>
        <button type='submit' class='btn btn-success my-1'>Send</button>
        <a href='coach.php' class='btn btn-primary my-1'>Finish</a>
      </form>

    </div>
    
    <?php
    unset($_SESSION['help']);
    ?>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>