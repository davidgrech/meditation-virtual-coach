<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];

  /*if guided meditation is chosen insert the correct minutes and seconds into sessions to be used on timer.php.
  minutes are transfered into hours if 60 mins or over on timer.php*/
  if(isset($_POST['guided'])){

    $guided = $_POST['guided'];
    $guided = htmlentities($guided);

    $length = 0;
    if($guided == 'bodyscan'){
      $length = 20;
      $seconds_guided = 16;
    }
    if($guided == 'sitting'){
      $length = 16;
      $seconds_guided = 51;
    }
    if($guided == 'movement'){
      $length = 41;
      $seconds_guided = 0;
    }
    if($guided == 'coping'){
      $length = 5;
      $seconds_guided = 21;
    }
    if($guided == 'mountain'){
      $length = 14;
      $seconds_guided = 0;
    }
    if($guided == 'walking'){
      $length = 10;
      $seconds_guided = 5;
    }

    $_SESSION['seconds']=$seconds_guided;
    $_SESSION['time']=$length;
    $_SESSION['guided']=$guided;

  }

  //get mins and seconds posted from meditate.php and store them in a session variable to be used on timer.php
  if(isset($_POST['mins'])){

    $hours = $_POST['hours'];
    $mins = $_POST['mins'];
    $seconds = $_POST['seconds'];
    $hours = htmlentities($hours);
    $mins = htmlentities($mins);
    $seconds = htmlentities($seconds);

    $total_time = $hours + $mins;

    //if timer is set to zero go back to meditate.php
    $zero_check = $total_time + $seconds;

    if($zero_check == 0){
      header('location:meditate.php');
      exit();
    }

    $_SESSION['time']=$total_time;
    $_SESSION['seconds']=$seconds;

  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //select date to see if wellbeing questions have been answered allready today. Client can answer wellbeing questions once a day.
  $stmt = $conn->prepare("SELECT date FROM wellbeing_rating WHERE client_id = ?  ORDER BY date DESC");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($date); 
  $stmt -> fetch();
  $numrows = $stmt->num_rows;
  $stmt->close();

  $system_date = date("Y-m-d");

  //if wellbeing rating allready present for today go straight to timer.php
  if($numrows> 0 && $system_date == $date ){
    header('location:timer.php');
    exit();
  }

  //when ratings are confirmed they are posted to this page, then inserted into the database. The page then navigates to timer.php
  if(isset($_POST['one'])){

    $one = $_POST['one'];
    $two = $_POST['two'];
    $three = $_POST['three'];
    $four = $_POST['four'];
    $five = $_POST['five'];
    $six = $_POST['six'];
    $one = htmlentities($one);
    $two = htmlentities($two);
    $three = htmlentities($three);
    $four = htmlentities($four);
    $five = htmlentities($five);
    $six = htmlentities($six);  

    $stmt1 = $conn->prepare("INSERT INTO wellbeing_rating (rating_id, client_id, question_one, question_two, question_three, question_four, question_five, question_six, date) 
    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, CURDATE()) "); 
    $stmt1->bind_param('iiiiiii', $client_id, $one, $two, $three, $four, $five, $six);
    $stmt1->execute();
    $stmt1->close();

    header('location:timer.php');

  }

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/myrating.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('../navbar.php'); ?>

    <div id='mycontainer'>

      <h3 class='myheading ml-1'>Wellbeing Rating</h3>
      <form method="POST" action="rating.php">

        <div class="myemojicontainer my-3">
              <img class='myemoji' src='../img/ratingbig.png'>
        </div>
        <!--display options to choose wellbeing ratings-->
        <div class='container'>
          <div class='row'>
            <div class='col-9 p-0'>
              <p class='mt-2 ml-1'><strong>1.</strong> I have been feeling close to other people</p>
            </div>
            <div class='col-3 p-0'>
              <div>
                <select type='text' class='form-control ml-1 my-2 myselect' name='one'>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class='container'>
          <div class='row'>
            <div class='col-9 p-0'>
              <p class='mt-2 ml-1'><strong>2.</strong> I’ve been satisfied by my sleep</p>
            </div>
            <div class='col-3 p-0'>
              <div>
                <select type='text' class='form-control ml-1 my-2 myselect' name='two'>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class='container'>
          <div class='row'>
            <div class='col-9 p-0'>
              <p class='mt-2 ml-1'><strong>3.</strong> I am at peace with myself</p>
            </div>
            <div class='col-3 p-0'>
              <div>
                <select type='text' class='form-control ml-1 my-2 myselect' name='three'>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class='container'>
          <div class='row'>
            <div class='col-9 p-0'>
              <p class='mt-2 ml-1'><strong>4.</strong> I cope well with difficulties, pain or suffering</p>
            </div>
            <div class='col-3 p-0'>
              <div>
                <select type='text' class='form-control ml-1 my-2 myselect' name='four'>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class='container'>
          <div class='row'>
            <div class='col-9 p-0'>
              <p class='mt-2 ml-1'><strong>5.</strong> I take good care of myself </p>
            </div>
            <div class='col-3 p-0'>
              <div>
                <select type='text' class='form-control ml-1 my-2 myselect' name='five'>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class='container'>
          <div class='row'>
            <div class='col-9 p-0'>
              <p class='mt-2 ml-1'><strong>6.</strong> I have noticed my reactions without having to react to them</p>
            </div>
            <div class='col-3 p-0'>
              <div>
                <select type='text' class='form-control ml-1 my-2 myselect' name='six'>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="myemojicontainer mb-3">
          <button class="btn btn-success" type="submit">Continue</button>
        </div>

      </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>
