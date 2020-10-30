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

  $client_id = htmlentities($client_id);

  //load only dates that have a wellbeing entry onto the calendar date picker
  $stmt1 = $conn->prepare("SELECT date FROM wellbeing_rating WHERE client_id = ? ORDER BY date ASC");
  $stmt1 -> bind_param("i", $client_id);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($date);
  $stmt1 -> fetch();
  $stmt1 -> data_seek(0);

  if(isset($_POST['day'])){

    $date = $_POST['date'];
    $day = $_POST['day'];
    $date = htmlentities($date);
    $day = htmlentities($day);

  }else{
    $day = 7;
  }

  $stmt2 = $conn->prepare("SELECT question_one, question_two, question_three, question_four, question_five, question_six, date FROM wellbeing_rating WHERE client_id = ? AND date >= ? ORDER BY date ASC LIMIT ?");
  $stmt2 -> bind_param("isi", $client_id, $date, $day);
  $stmt2 -> execute();
  $stmt2 -> store_result(); 
  $stmt2 -> bind_result($question_one, $question_two, $question_three, $question_four, $question_five, $question_six, $date1);

  $stmtp = $conn->prepare("SELECT program.id, program.start_date, program.end_date, program_details.program_name, 
  program_details.path, SUM(complete_exp_details.program_complete_id), complete_exp_details.program_length 
  FROM program 
  INNER JOIN program_details ON program.program_details_id = program_details.id 
  INNER JOIN program_complete ON program.id = program_complete.program_id 
  INNER JOIN complete_exp_details ON program_complete.complete_exp_details_id = complete_exp_details.id 
  INNER JOIN user_details ON program.client_id = user_details.id 
  WHERE program.client_id = ? GROUP BY program_id");
  $stmtp -> bind_param("i", $client_id);
  $stmtp -> execute();
  $stmtp -> store_result();
  $stmtp -> bind_result($pro_id, $start_date, $end_date, $pro_name, $path, $pro_complete, $pro_length);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/dashboard/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/myclientwellbeingchart.css' rel='stylesheet'>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
 
    <script type="text/javascript">
     google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Social', 'Sleep', 'Contentment', 'Resilience', 'Self-care', 'Non-reactive',],
          <?php
          while($stmt2 -> fetch()){

            $date1 = date("d-M-Y", strtotime($date1));
            echo"['";
            echo"$date1";
            echo"', ";
            echo"$question_one";
            echo", ";
            echo"$question_two";
            echo", ";
            echo"$question_three";
            echo", ";
            echo"$question_four";
            echo", ";
            echo"$question_five";
            echo", ";
            echo"$question_six";
            echo"], ";
          }
          $stmt2->close();
          ?>
        ]);

        var options = {
          title: 'Wellbeing Ratings',
          curveType: 'function',
          legend: { position: 'bottom', minLines: 3 },
          vAxis: {minValue: 0, maxValue: 9, title: 'Rating'},
          hAxis: {title: 'Date'},

        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        //custom error message for google chart reference(31)
        google.visualization.events.addListener(chart, 'error', function (googleError) {
          google.visualization.errors.removeError(googleError.id);
          document.getElementById("error_msg").innerHTML = "<p class='ml-5'><strong>Client has not done any wellbeing ratings</strong></p>"; //Message removed = '" + googleError.message + "'
        });

        chart.draw(data, options);

      //speed up resizing of chart reference(24)   
      $(window).resize(function() {
          if(this.resizeTO) clearTimeout(this.resizeTO);
          this.resizeTO = setTimeout(function() {
              $(this).trigger('resizeEnd');
          }, 500);
      });

      $(window).on('resizeEnd', function() {
          drawChart(data);
      });

      }

      </script>

    <title>Meditation Success</title>
  </head>
  <body>

    <?php include('admintopnavbar.php'); ?>

    <div class="container-fluid">
      <div class="row">
        
        <?php include('adminleftnavbar.php'); ?>

          <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pl-0">
            <div class="ml-md-5">
              <h3 class="ml-3 mt-5" >Wellbeing Rating Charts</h3>
            </div>
            <hr class='featurette-divider mb-3 mt-3'>
            <div class="container">
              <div class="row">
                <div class="col-md-12">
                  <table id='test' class='table table-lg d-flex'>
                    <tbody>
                      <tr>
                        <form method="POST" action="adminwellbeingchart.php">
                        <td class='p-1' style='border: none'><label class=''><strong><pre class='mypre'>Start Date:</pre></strong></label></td>
                        <td class='p-1' style='middle; border: none'><input placeholder='Select' class='date form-control bg-white myinputone' name='date'/></td>
                      </tr>
                      <tr>
                        <td class='p-1' style='middle; border: none'><label class=''><strong><pre class='mypre'>Duration (days):</pre></strong></label></td>
                        <td class='p-1' style='middle; border: none'>
                          <select class='form-control myinputtwo' name='day'>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                            <option>9</option>
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                            <option>13</option>
                            <option>14</option>
                            <option>15</option>
                            <option>16</option>
                            <option>17</option>
                            <option>18</option>
                            <option>19</option>
                            <option>20</option>
                            <option>21</option>
                            <option>22</option>
                            <option>23</option>
                            <option>24</option>
                            <option>25</option>
                            <option>26</option>
                            <option>27</option>
                            <option>28</option>
                            <option>29</option>
                            <option>30</option>
                          </select>
                        </td>
                        <td class='p-1' style='border: none'><button type='submit' class='btn btn-success ml-3'>Go</button></td>
                        </form>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class='mt-3' id="error_msg"></div>
              </div>
            </div>
            <div id='curve_chart' class='chart mb-5'></div>
            <hr class="featurette-divider my-5">
            <?php
              //if wellbeing programs are present print a title, then display wellbeing programs
              $numrows = $stmtp->num_rows;
              if($numrows > 0){
                echo"
                  <div id='mycontainer'>
                    <div class='col-sm-12'>
                      <h4 class='mt-3' >Wellbeing Programs</h4>
                    </div>
                ";
                
                while($stmtp -> fetch()){

                  $modulas = $pro_complete % $pro_length;
                  $total_complete = $pro_length - $modulas;

                  /*if the program total complete is equal to the program length then all programs are complete, 
                  else if modulas is eqaul to zero non are complete*/
                  if($pro_complete == $pro_length){
                    $total_complete = $pro_length;
                  }elseif($modulas == 0){
                    $total_complete = 0;
                  }

                  echo"
                    <div class='row mt-5'>
                      <div class='col-sm-2 myemojicontainer'>
                        <img class='d-block' src='$path' style='width:60px; height:60px;'>
                      </div>
                      <div class='col-sm-6 text-center'>
                        <p class='mb-0 font-weight-bold'>$pro_name</p>
                        <p class='mb-0'><strong>Start:</strong> $start_date</p>
                        <p class='mb-0'><strong>End:</strong> $end_date<p>
                      </div>
                      <div class='col-sm-4 text-center'>
                        <p><strong>$total_complete/$pro_length</strong> complete</p>
                      </div>
                    </div>
                  ";
                }//end of print programs
                $stmtp->close();
              
                $stmt3 = $conn->prepare("SELECT date, length, previous_length FROM intervention_length WHERE client_id = ?");
                $stmt3 -> bind_param('i', $client_id);
                $stmt3 -> execute();
                $stmt3 -> store_result();
                $stmt3 -> bind_result($date, $length, $previous_length);

                $numrows3 = $stmt3->num_rows;
                if($numrows3 > 0){
                  echo"
                    <div class='col-lg-12'>
                      <h4 class='my-3'>Interventions</h4>
                    </div>
                  ";
                
                  while($stmt3 -> fetch()){
                    echo"
                      <div class='col-lg-12'>
                        <p>Wellbeing program length changed from <strong>$previous_length</strong> days to <strong>$length</strong> days on <strong>$date</strong></p>
                      </div>
                    ";
                  }

                }//end of if condition for interventions present

                echo"</div>";//end of mycontainer
                echo"
                  <hr class='featurette-divider my-5'>
                ";
              }//end of if condition. Print if programs present

              $stmt3->close();
            ?>
          </main><!-- end of the main section -->
        </div>
      </div>

    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="../js/dashboard.js"></script></body>
    <!-- classList polyfill for IE9 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/classlist/1.2.20171210/classList.min.js"></script>

    <script>

      var fp = flatpickr(".date", {
      dateFormat: "Y-m-d",

      <?php
        //load only dates with a wellbeing rating onto the date picker
        echo"enable: [";
        while($stmt1 -> fetch()){
          echo"'";
          echo"$date";
          echo"', ";
        }
        $stmt1->close();
        echo"]";
      ?>
      });

    </script>
  </body>
</html>
