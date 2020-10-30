<?php

  session_start();
  include("../connect.php");  

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
  }

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
    <div id="mycontainer">
      <!--display option to upload profile picture image-->
      <h4 class='myheading'>Choose an image to upload</h4>
      <div class='card m-1 p-2'>
        <form enctype="multipart/form-data" action="adminimagecomplete.php"  method="POST" class="frmImageUpload">
          <label>Upload Image File (maximum size 600x600 pixels):</label><br />
          <input name="myfileupload"  type="file" class="inputfile" />
          <input type="submit"  value="Submit" class="btnsubmit" name="fileup" />
        </form>
      </div>
    </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>