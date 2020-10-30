<nav id="sidebarMenu" class="mt-2 col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
  <div class="sidebar-sticky pt-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="adminregisterclient.php">
          <span data-feather="user-plus"></span>
          Register client
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="adminchooseclient.php">
          <span data-feather="users"></span>
          Choose client
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="adminclientdetails.php">
          <span data-feather="folder"></span>
          Client details
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="admindashboard.php">
          <span data-feather="user-check"></span>
          Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="adminwellbeingchart.php">
          <span data-feather="smile"></span>
          Wellbeing charts
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="admintimechart.php">
          <span data-feather="activity"></span>
          Meditation charts
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="admininterventions.php">
          <span data-feather="zap"></span>
          Interventions
        </a>
      </li>
      <li class="nav-item">
        <?php
          //Select unread messages 
          $stmtl = $conn->prepare("SELECT `read` FROM help_request_message WHERE client_id = ? and `read` = 1");
          $stmtl -> bind_param("i", $client_id);
          $stmtl -> execute();
          $stmtl -> store_result();
          $numrows = $stmtl->num_rows;

          //if there are unread messages display a notification, else do not.
          if($numrows > 0){
            echo"
            <a style='color:blue' class='nav-link active' href='adminclientmessages.php'>
              <span data-feather='mail'></span>
              Messages
            </a>
            ";
          }else{
            echo"
            <a class='nav-link' href='adminclientmessages.php'>
              <span data-feather='mail'></span>
              Messages
            </a>
            ";
          }   
        ?>
      </li>
    </ul>
  </div>
</nav>