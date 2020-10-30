<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
  }

  if(isset($_SESSION['client_session'])){
    $client_id = $_SESSION['client_session'];
  }else{
    header('location:adminchooseclient.php');
  }

  $client_id = htmlentities($client_id);

  //mark all messages as read on entering the page
  $stmt = $conn->prepare("UPDATE `help_request_message` SET `read` = 0 WHERE `client_id` = ?");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt->close();

  $stmt1 = $conn->prepare("SELECT first_name, second_name FROM user_details WHERE id = ?");
  $stmt1 -> bind_param("i", $client_id);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($first_name, $second_name);
  $stmt1 -> fetch();
  $stmt1->close();

  $stmt2 = $conn->prepare("SELECT message, date, time, author FROM help_request_message WHERE client_id = ? ORDER BY id DESC");
  $stmt2 -> bind_param("i", $client_id);
  $stmt2 -> execute();
  $stmt2 -> store_result(); 
  $stmt2 -> bind_result($message, $date, $time, $author);

  //get posted message and insert it into the database
  if(isset($_POST['message'])){

    $message = $_POST['message'];
    $message = htmlentities($message);

    $stmt3 = $conn->prepare("INSERT INTO help_request_message (`id`, `client_id`, `message`, `date`, `time`, `author`, `read`) VALUES (NULL, ?, ?, CURDATE(), CURRENT_TIME(), 'Coach', 0);");
    $stmt3->bind_param('is', $client_id, $message);
    $stmt3->execute();
    $stmt3->close();

    header("Refresh:0");

  }

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">
    <script src="https://unpkg.com/feather-icons"></script>
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href='../css/mystyle.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('admintopnavbar.php'); ?>

    <div class="container-fluid">
      <div class="row">

        <?php include('adminleftnavbar.php'); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
          <div id='mycontainerlarge'>
            <h3 class='my-5'>Messages</h3>
            
            <?php

            echo"
              <div class='card mychatcontainer'>
                <div  class= 'mychatwindow'>
              ";
                //print messages out to screen
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

            <form method='POST' action='adminclientmessages.php'>
              <div class="form-group">
                <textarea class="mt-5 form-control mytextarea" placeholder='Type message (max 255 characters)' rows="2" name='message'></textarea>
              </div>
              <button type='submit' class='btn btn-success my-1'>Send</button>
            </form>
          </div>
        </main><!-- end of the main section -->
      </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="../js/dashboard.js"></script></body>
  </body>
</html>