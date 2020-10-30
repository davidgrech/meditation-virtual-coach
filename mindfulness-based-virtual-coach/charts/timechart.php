<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
    header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //load only dates that have a meditation time entry to the date picker
  //fetch the oldest date from the database with a meditation time entry
  $stmt = $conn->prepare("SELECT date FROM complete_exp_details WHERE client_id = ? ORDER BY date ASC");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result(); 
  $stmt -> bind_result($date);
  $stmt -> fetch();

  /*if day and date have been selected and posted use them to display chart data, otherwise date will be the oldest one and the duration of dates will be 7*/
  if(isset($_POST['day'])){

    $day = $_POST['day'];
    $date = $_POST['date'];
    $date = htmlentities($date);
    $day = htmlentities($day);
  
  }else{
    $day = 7;
  }

  $stmt1 = $conn->prepare("SELECT complete_exp_details.date, SUM(time_total), meditation_complete.target FROM meditation_complete 
  INNER JOIN complete_exp_details
  ON meditation_complete.complete_exp_details_id = complete_exp_details.id
  WHERE meditation_complete.client_id = ? AND complete_exp_details.date >= ? GROUP BY complete_exp_details.date ORDER BY complete_exp_details.date ASC LIMIT ?");

  $stmt1 -> bind_param("isi", $client_id, $date, $day);
  $stmt1 -> execute();
  $stmt1 -> store_result(); 
  $stmt1 -> bind_result($date, $time_total, $target);

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/myclientwellbeingchart.css' rel='stylesheet'>

    <title>Meditation Success</title>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 
    <script type="text/javascript">
     google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Time', 'Daily Target'],
          <?php
          while($stmt1 -> fetch()){
            //display Google chart with meditation time data
            $date = date("d-M-Y", strtotime($date));
            echo"['";
            echo"$date";
            echo"', ";
            echo"$time_total";
            echo", ";
            echo"$target";
            echo"], ";
          }
          $stmt1->close();
          ?>
        ]);

        var options = {
          title: 'Daily Meditation Time and Target',
          curveType: 'function',
          legend: { position: 'bottom', minLines: 3 },
          vAxis: {minValue: 0, title: 'Minutes', viewWindowMode: "explicit", viewWindow:{ min: 0 }},
          hAxis: {title: 'Date'},

        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        //custom error message for google chart reference(31)
        google.visualization.events.addListener(chart, 'error', function (googleError) {
        google.visualization.errors.removeError(googleError.id);
        document.getElementById("error_msg").innerHTML = "<p><strong>You have not completed any meditations</strong></p>"; //Message removed = '" + googleError.message + "'
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

  </head>
  <body>

    <?php include('../navbar.php'); ?>

      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h4 class='myheading border-bottom'>Meditation Time Charts</h4>
          </div>
          <div class="col-md-12">
            <table id='test' class='table table-lg d-flex'>
              <tbody>
                <tr>
                  <form method="POST" action="timechart.php">
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
    <div id="curve_chart" class="chart mb-5"></div>

    <hr class="featurette-divider my-5">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/classlist/1.2.20171210/classList.min.js"></script>

    <script>

      var fp = flatpickr(".date", {
      dateFormat: "Y-m-d",

        <?php
          //load only dates within the eight-week meditation program
          echo"enable: [";
          while($stmt -> fetch()){
            echo"'";
            echo"$date";
            echo"', ";
          }
          $stmt->close();
          echo"]";

        ?>
      });

      </script>
  </body>
</html>