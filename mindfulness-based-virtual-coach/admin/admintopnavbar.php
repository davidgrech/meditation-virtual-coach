<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
<?php
  //display the client's name when they are selected on adminchooseclient.php
  $stmt0 = $conn->prepare("SELECT first_name, second_name FROM user_details WHERE id = ?");
  $stmt0 -> bind_param("i", $client_id);
  $stmt0 -> execute();
  $stmt0 -> store_result(); 
  $stmt0 -> bind_result($first_name, $second_name);
  $stmt0 -> fetch();
  $stmt0->close();
?>
  <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/index.php">
    <img style='height:30px; width:30px;' src='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/img/logoeight.png'>
  </a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <h5 style='color:white; margin-bottom:0px' class='ml-3'><?php echo"$first_name $second_name"; ?></h5>
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      <a class="nav-link" href="../signout.php">Sign out</a>
    </li>
  </ul>
</nav>