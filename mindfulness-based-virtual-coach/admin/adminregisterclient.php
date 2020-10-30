<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
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
        
          <div class='justify-content-center align-items-center flex-wrap d-flex'>
            <div class="grid">
              <div class="row">
                <div class="col-lg-12">
                  <h4 class='my-5'>Register New Client</h4>
                </div>
                <div class="card col-lg-12">  
                  <form method='POST' action='adminregisterclient.php'>
                    <h6 class='mt-3'><strong>Enter new client details:</strong></h6>
                    <div class='form-row'>
                      <div class='col-sm-12 my-1'>
                        <label class='mt-1'><strong>Email:</strong></label>
                            <input type='email' class='form-control' value='' name='email'>
                            <p>Must be no more than 30 characters long</p>
                        <label class='mt-1'><strong>Password:</strong></label>
                            <input type='password' class='form-control'value='' name='pass'>
                            <p>Must be no more than 20 characters long</p>
                        <label class='mt-1'><strong>Username:</strong></label>
                            <input type='text' class='form-control'value='' name='user_name'>
                            <p>Must be no more than 20 characters long</p>
                        <label class='mt-1'><strong>First name:</strong></label>
                            <input type='text' class='form-control'value='' name='first_name'>
                            <p>Must be no more than 20 characters long</p>
                        <label class='mt-1'><strong>Second name:</strong></label>
                            <input type='text' class='form-control'value='' name='second_name'>
                            <p>Must be no more than 20 characters long</p>
                        <label class='mt-1'><strong>Phone number:</strong></label>
                            <input type='text' class='form-control'value='' name='phone'>
                            <p>Must be no more than 20 digits long</p>
                        <label class='mt-1'><strong>Address:</strong></label>
                          <input type='text' class='form-control'value='' name='address'>
                          <p>Must be no more than 100 characters long</p>
                      </div>
                      <div class='p-2'>
                          <button type='submit' class='btn btn-success'>Submit</button>
                      </div>
                    </div>
                  </form>
                </div>
                <?php
                  if(isset($_POST['email'])){

                    $email = $_POST['email'];
                    $pass = $_POST['pass'];
                    $user_name = $_POST['user_name'];
                    $first_name = $_POST['first_name'];
                    $second_name = $_POST['second_name'];
                    $phone = $_POST['phone'];
                    $address = $_POST['address'];

                    $email = htmlentities($email);
                    $pass = htmlentities($pass);
                    $user_name = htmlentities($user_name);
                    $first_name = htmlentities($first_name);
                    $second_name = htmlentities($second_name);
                    $phone = htmlentities($phone);
                    $address = htmlentities($address);

                    //Insert new user into database and print confirmation
                    $stmt = $conn->prepare("INSERT INTO user_details (email, pass, user_name, first_name, second_name, phone, address, authorisation, path, signed_up, program_length) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 0, '../img/login.png', NOW(), 7) "); 

                    $stmt->bind_param('sssssss', $email, $pass, $user_name, $first_name, $second_name, $phone, $address);
                    $stmt->execute();

                    echo"<p>Client was added succesfully. Go <a href='admindashboard.php'>back</a> to dashboard</p> ";
                  }
                ?> 
              </div>
            </div>
          </div>
        </main><!-- end of the main section -->
      </div>
    </div>

    <hr class="featurette-divider my-5">

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="../js/dashboard.js"></script></body>
  </body>
</html>