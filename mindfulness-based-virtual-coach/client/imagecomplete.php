<?php

  session_start();
  include("../connect.php");

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);
  
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Meditation Success</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link href="../css/mystyle.css" rel="stylesheet">

</head>

<body>
  <?php include('../navbar.php'); ?>

  <div id='mycontainer'>
    <h4 class='myheading'>Image upload result</h4>

    <?php
      $filedata = $_FILES['myfileupload']['tmp_name'];
      $filename = $_FILES['myfileupload']['name'];
      $filetype = $_FILES['myfileupload']['type'];
      $allowed = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
      if(!in_array($filetype, $allowed)) {
        echo"
          <div class='card'>
            <p class='mt-2 ml-2'><strong>Only jpg, jpeg, gif and png files are allowed. Go <a href='clientdetails.php'>back</a> to try again.</p></strong></p>
          </div>
        ";
      }else{
      
        //upload image to database
        $moved = move_uploaded_file($filedata, "../img/profile_pics/$filename");
  
        if($moved){

          $filename = htmlentities($filename);
          $filetype = htmlentities($filetype);

          $filename = '../img/profile_pics/'.$filename;
          //update path for new profile picture
          $stmt = $conn->prepare("UPDATE user_details SET path = ? WHERE id = ? ");
      
          $stmt->bind_param('si', $filename, $client_id);
          $stmt->execute();

          echo"
            <div class='card'>
              <p class='mt-2 ml-2'><strong>Image uploaded successfully. Go <a href='clientdetails.php'>back</a> to change home page images.</strong></p>
            </div
          ";

        }else{
          echo "$filename could not be uploaded";
        }
      }

    ?>       
  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>