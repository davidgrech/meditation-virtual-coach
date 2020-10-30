<nav class='navbar navbar-expand-lg navbar-dark bg-dark navbar-trans fixed-top'> <!--fixed-top-->
  <a class='navbar-brand' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/index.php'>
    <img class='mylogo' src='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/img/logoeight.png'>
  </a>
  <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
    <span class='navbar-toggler-icon'></span>
  </button>
  <div class='collapse navbar-collapse' id='navbarNav'>
    <ul class='navbar-nav mr-auto'>
      <li class='nav-item active'>
        <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/meditate/meditate.php'>Meditate</a>
      </li>
      <?php
        //make sure client has completed a wellbeing rating before giving them all options on the navbar
        $stmtr = $conn->prepare("SELECT rating_id FROM wellbeing_rating WHERE client_id = ?");
        $stmtr -> bind_param("i", $client_id);
        $stmtr -> execute();
        $stmtr -> store_result();

        $numrows = $stmtr->num_rows;
        $stmtr->close();
      
        if($numrows > 0){
          echo"
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/program/progress.php'>Progress</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/coach/coach.php'>Wellbeing</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/coach/dialoguerecurring.php'>Coach</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/charts/chart.php'>Charts</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/community/community.php'>Community</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/coach/dialoguehelpmessage.php'>Chat</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/client/clientdetails.php'>Account</a>
            </li>
          ";
        }
      ?>
    </ul>
    <?php 
      //if client is logged in show name and account logo 
      $stmtfs = $conn->prepare("SELECT first_name, second_name FROM user_details WHERE id=?");
      $stmtfs -> bind_param("i", $client_id);
      $stmtfs -> execute();
      $stmtfs -> store_result();
      $stmtfs -> bind_result($first_name, $second_name);
      $stmtfs -> fetch();

      $numrows = $stmtfs->num_rows;
      $stmtfs->close();

      if($numrows > 0){
        echo"
          <p class='mb-0 py-2' style='color:white'>$first_name $second_name</p>
          <a class='navbar-brand' href='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/client/clientdetails.php'>
            <img class='mylogo' src='http://dgrech01.lampt.eeecs.qub.ac.uk/mindfulness-based-virtual-coach/img/logogreen.png'>
          </a>
      ";
      }
    ?>
  </div>
</nav>