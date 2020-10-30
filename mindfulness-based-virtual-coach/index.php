<?php
    session_start();
    include('connect.php');
  
    if(isset($_SESSION['clientlogin'])){
      $client_id = $_SESSION['clientlogin'];
      $client_id = htmlentities($client_id);
    }

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>

    <link href='css/myhero.css' rel='stylesheet'>
    <link href='css/mystyle.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('navbar.php'); ?>
    <!--Hero header reference(1) -->
    <header>
      <div class='mytitle'>
        <h1 style='text-align:center'>Mindfulness Based<br>Stress Reduction</h1>
      </div>
      <div class='mybutton'>
        <a href='signin.php' class='mybtn'><p class='mb-0'>Sign in</p></a>
      </div>
    </header>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>