<?php
  session_start();
  include("connect.php");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Meditation Success</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.4/examples/sign-in/">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }
    </style>

    <link href="css/signin.css" rel="stylesheet">
    <link href="css/mystyle.css" rel="stylesheet">

</head>

<body class="text-center">

<?php include('navbar.php'); ?>

  <form class="form-signin" method="POST" action="signin.php">

    <img class="mb-4" src="">
    <h1 class="h3 mb-3 font-weight-normal">Sign in</h1>
    <label class="sr-only">Username</label>
    <input type="text" class="mt-5 form-control" placeholder="Username" name="username">
    <label class="sr-only">Password</label>
    <input type="password" class="form-control" placeholder="Password" name="passfield">
    <button class="mt-4 btn btn-lg btn-success btn-block" type="submit" value="login">Continue</button>
      
  </form>

  <?php
      //client and admin log in
      if(isset($_POST['username'])){

        $user_name = $_POST['username'];
        $passw1 = $_POST['passfield'];

        $user_name = htmlentities($user_name);
        $passw1 = htmlentities($passw1);

        $stmt = $conn->prepare("SELECT authorisation, id FROM user_details WHERE user_name=? AND pass=?");
        $stmt -> bind_param("ss", $user_name, $passw1);
        $stmt -> execute();
        $stmt -> store_result(); 
        $stmt -> bind_result($authorisation, $id);
        $stmt -> fetch();
        $numrows = $stmt->num_rows;
        $stmt->close();
        //if authorisation is 0, log in as client. If authorisation is 1, log in as admin
        if($numrows> 0 && $authorisation==0){
          $_SESSION['clientlogin']=$id;
          header('location:meditate/meditate.php');
        } elseif($numrows> 0 && $authorisation==1){
          $_SESSION['adminlogin']=$id;
          header('location:admin/adminchooseclient.php');
        }
      }
    ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>