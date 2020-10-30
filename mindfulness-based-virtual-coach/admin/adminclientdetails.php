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

  //retrieve client details from database, later print them to screen
  $stmt = $conn->prepare("SELECT email, user_name, first_name, second_name, phone, address, path FROM user_details WHERE id = ?");
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
          <div class='justify-content-center align-items-center flex-wrap d-flex'>
            <div id='mycontainer'>
              <h3 class='mt-5 pb-2'>Client Details<a class="btn btn-outline-success" style='float:right' href="adminuploadimage.php" role="button">Upload image</a></h4>
              <table class="table table-bordered table-sm">
                <tbody>
                  <?php
                    echo"
                      <div class='d-flex justify-content-center mb-3'>
                          <img class='myimg' src='$path''>
                      <div>
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
        </main><!-- end of the main section -->
      </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>

    <script src="../js/dashboard.js"></script></body>
  </body>
</html>