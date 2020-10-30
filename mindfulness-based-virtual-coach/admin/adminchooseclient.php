<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['adminlogin'])){
    header('location:../signin.php');
  }

  //select all clients so the admin can view them and select one
  $read = "SELECT * FROM user_details WHERE authorisation = 0 ORDER BY first_name ASC";

  $result = $conn->query($read);

  if(!$result){
    echo $conn->error;
  }else{
    
  }

  $counter = 0;

  while($row = $result->fetch_assoc()){

    $get_first_name = $row['first_name'];
    $get_second_name = $row['second_name'];
    $get_id = $row['id'];

    $name_array[$counter] = $get_first_name.' '.$get_second_name;
    $id_array[$counter] = $get_id;

    $counter++;

  }

  $start=0;
  $max=count($name_array);

  //store client name and id in arrays
  while ($start < $max) {
    $id = $id_array[$start];
    $name = $name_array[$start];
    $start++;
  }

  //when cleint_id = 100 no client is selected
  $client_id = 100;

  //the selected client's id is stored in a session
  if(isset($_SESSION['view_client'])){
    $client_id = $_SESSION['view_client'];
  }

  $client_id = htmlentities($client_id);

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
                  <h3 class="mt-5">Choose Client</h3>
                </div>
                <div class="col-lg-12">
                  <form class='mt-5' method='POST' action='admincreateclientsession.php'>
                    <div class='form-row'>
                      <div class='col-sm-5'>
                        <?php
                          echo"<select class='form-control mt-2' name='chosen_id'>";

                          //select client messages from database. Only select the first group of messages that equal 1. A 1 representes any unread messages
                          $stmt = $conn->prepare("SELECT `read` FROM `help_request_message` 
                          INNER JOIN user_details
                          ON help_request_message.client_id = user_details.id
                          WHERE client_id = ?
                          GROUP BY `read`
                          ORDER BY `read` DESC
                          LIMIT 1");

                          $start=0;
                          $max=count($name_array);

                          while ($start < $max) {
                            $id = $id_array[$start];
                            $name = $name_array[$start];

                            $stmt->bind_param('i', $id);
                            $stmt -> execute();
                            $stmt -> store_result();
                            $stmt -> bind_result($read);
                            $stmt -> fetch();

                            echo"read $read";
                            //if equal to 1 print out a unread message notification next to the corresponding client
                            if($read == 1){
                              echo "<option style='color:blue' value='$id' >$name &#x2709;</option>";
                            }else{
                            echo "<option value='$id'>$name</option>";
                            }
                            $start++;
                          }
                          $stmt->close();
                        ?>

                        </select>
                      </div>
                      <div class='col-4'>      
                        <button type='submit' class='btn btn-success mt-2'>Select</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
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