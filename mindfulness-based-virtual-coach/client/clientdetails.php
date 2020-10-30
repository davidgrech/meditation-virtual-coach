<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  $stmt = $conn->prepare("SELECT email, user_name, first_name, second_name, phone, address, path FROM user_details WHERE id = ?");

  //get client details from database
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($email, $user_name, $first_name, $second_name, $phone, $address, $path);
  $stmt -> fetch();
  $stmt->close();
?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/myclient.css' rel='stylesheet'>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('../navbar.php');?>

    <div id='mycontainer'>

      <h3 class='myheading pb-2'>Client Details</h3>

      <?php
        //display sign out button, upload image button, client profile picture and details
        echo"
          <div class='d-flex justify-content-end mb-3'>
            <a class='btn btn-primary mr-2'  href='../signout.php' role='button'>Sign out</a>
            <a class='btn btn-success mr-2' href='uploadimage.php' role='button'>Upload image</a>
          </div>
          <div class='d-flex justify-content-center my-3'>
            <img class='myimg' src='$path'>
          </div>

          <table class='table table-bordered table-sm'>
            <tbody>
              <tr>
                <td>
                  <p><strong>E-mail:</strong> $email</p>
                </td>
              </tr>
              <tr>
                <td>
                  <p><strong>Username:</strong> $user_name</p>
                </td>
              </tr>
              <tr>
                <td>
                  <p><strong>Name:</strong> $first_name $second_name</p>
                </td>
              </tr>
              <tr>
                <td>
                  <p><strong>Phone:</strong> $phone</p>
                </td>
              </tr>
              <tr>
                <td>
                <p><strong>Address:</strong> $address</p>
                </td>
              </tr>
            ";
          ?>
        </tbody>
      </table>
    </div>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  </body>
</html>