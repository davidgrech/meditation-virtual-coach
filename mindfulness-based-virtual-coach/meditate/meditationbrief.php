<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/mybrief.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('../navbar.php'); ?>

    <div id='mycontainerlargest'>
      <!--display a technique for pure meditation-->
      <h3 class='myheading'>Pure Meditation Guidance</h3>

      <a href='meditate.php' class='btn btn-success mb-4 m-2' role='button'>Back</a>
      <h4 class='px-1'>Suggested format</h4>
      <p class='px-1'>
      The following guidance is a suggested mindfulness meditation format, but feel free to use your own technique. 
      </p>
      <h4 class='px-1'>Preparation</h4>
      <p class='px-1'>
      It is best to find somewhere quiet where you will not be disturbed. Make sure your clothing is loose fitting and 
      does not restrict your breathing. If sitting, sit in an upright and dignified posture, or try lying down.
      </p>
      <h4 class='px-1'>The Breath</h4>
      <p class='px-1'>
        Notice the sensations of breathing. Try focusing on the rise and fall of your chest, or the sensation at the 
        nostrils. Witness the breath as it happens without making a judgment.
      </p>
      <h4 class='px-1'>Ending the exercise</h4>
      <p class='px-1'>
        When the final gong goes take a few moments to experience the fullness of the present moment and when you feel 
        comfortable to do so opening your eyes and bringing the exercise to a close.
      </p>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
 
  </body>
</html>
