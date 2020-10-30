<?php

  session_start();
  include('../connect.php');

  if(!isset($_SESSION['clientlogin'])){
      header('location:../signin.php');
  }

  $client_id = $_SESSION['clientlogin'];
  $client_id = htmlentities($client_id);

  //check to see if the current client allready has a meditation program
  $stmt = $conn->prepare("SELECT id FROM meditation_program WHERE client_id = ? ORDER BY id DESC");
  $stmt -> bind_param("i", $client_id);
  $stmt -> execute();
  $stmt -> store_result();
  $numrows = $stmt->num_rows;
  $stmt->close();

  //if they don't have a meditation program go to dialoguemeditation.php to create one
  if($numrows == 0){
    header('location:../coach/dialoguemeditation.php');
  }

?>

<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'>
    <link href='../css/mystyle.css' rel='stylesheet'>
    <link href='../css/mymeditate.css' rel='stylesheet'>
    <title>Meditation Success</title>
  </head>
  <body>

  <?php include('../navbar.php');?>

    <div id='mycontainermeditate'>

      <h3 class='myheading ml-1'>Meditate</h4>
      <form class='form-group' action='rating.php' method="post">
      <h4 class='mt-4 mb-3 ml-1'>Pure Meditation</h4>
        <div class='container'>
          <div class='row ' style='height: 50px;'>
            <div class='d-inline ml-1' style='width: 90px'>
              <p><pre class='mypre'><strong>Hours : </strong></pre></p>
            </div>
            <div class='d-inline'>
              <!--display option to select number of hours to meditate-->
              <select type='text' class='form-control mt-3 ml-1 myselect' style='width: 70px; padding-left: 14px' name='hours'>
                <option value='0'>0</option>
                <option value='60'>1</option>
                <option value='120'>2</option>
                <option value='180'>3</option>
                <option value='240'>4</option>
                <option value='300'>5</option>
                <option value='360'>6</option>
                <option value='420'>7</option>
                <option value='480'>8</option>
                <option value='540'>9</option>
                <option value='600'>10</option>
              </select>
            </div>
          </div>
          <div class='row' style='height: 50px;'>
            <div class='d-inline ml-1' style='width: 90px'>
              <p><pre class='mypre'>Minutes : </pre></p>
            </div>
            <div class='d-inline'>
              <!--display option to select number of minutes to meditate-->
              <select type='text' class='form-control mt-3 ml-1 myselect' style='width: 70px; padding-left: 14px' name='mins'>
                <option>0</option>
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
                <option>31</option>
                <option>32</option>
                <option>33</option>
                <option>34</option>
                <option>35</option>
                <option>36</option>
                <option>37</option>
                <option>38</option>
                <option>39</option>
                <option>40</option>
                <option>41</option>
                <option>42</option>
                <option>43</option>
                <option>44</option>
                <option>45</option>
                <option>46</option>
                <option>47</option>
                <option>48</option>
                <option>49</option>
                <option>50</option>
                <option>51</option>
                <option>52</option>
                <option>53</option>
                <option>54</option>
                <option>55</option>
                <option>56</option>
                <option>57</option>
                <option>58</option>
                <option>59</option>
              </select>
            </div>
          </div>
          <div class='row' style='height: 50px;'>
            <div class='d-inline ml-1' style='width: 90px'>
              <p><pre class='mypre'><strong>Seconds : </strong></pre></p>
            </div>
            <div class='d-inline' >
              <!--display option to select number of seconds to meditate-->
              <select type='text' class='form-control mt-3 ml-1 myselect text-center' style='width: 70px; padding-left: 14px' name='seconds'>
                <option>0</option>
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
                <option>31</option>
                <option>32</option>
                <option>33</option>
                <option>34</option>
                <option>35</option>
                <option>36</option>
                <option>37</option>
                <option>38</option>
                <option>39</option>
                <option>40</option>
                <option>41</option>
                <option>42</option>
                <option>43</option>
                <option>44</option>
                <option>45</option>
                <option>46</option>
                <option>47</option>
                <option>48</option>
                <option>49</option>
                <option>50</option>
                <option>51</option>
                <option>52</option>
                <option>53</option>
                <option>54</option>
                <option>55</option>
                <option>56</option>
                <option>57</option>
                <option>58</option>
                <option>59</option>
              </select>
            </div>
          </div>
        </div>
        <button id='save' class='btn btn-success mt-4 mb-3 ml-1' type='submit' >Start</button>
        <a href='meditationbrief.php' class='btn btn-outline-primary mt-2 ml-2' role='button'>Guide</a>
      </form>

      <h4 class='mt-4 mb-3 ml-1'>Guided Meditations by Jody-Mardula</h4>

      <form class='form-group' action='rating.php' method="post">
        <div class='row'>
            <div class='col-sm-12'>
              <!--display guided meditations to choose from. The hidden value is posted to rating.php-->
              <select style='width:300px' type='text' class='form-control mt-3 ml-1 myselect' name='guided'>
                <option value='coping'>Coping with difficulty, 5:21 mins</option>
                <option value='bodyscan'>Body scan, 20:16 mins</option>
                <option value='sitting'>Sitting meditation 16:51 mins</option>
                <option value='movement'>Mindful movement 41:00 mins</option>
                <option value='mountain'>Mountain meditation 14:00 mins</option>
                <option value='walking'>Walking meditation 10:05 mins</option>
              </select>
            </div>
            <div class='col-sm-12'>
              <button id='save' class='btn btn-success mt-3 ml-1' type='submit' >Start</button>
            </div>
          </div>
      </form>
    </div>

    <hr class="featurette-divider my-5">
    
    <script src='https://code.jquery.com/jquery-3.4.1.slim.min.js' integrity='sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n' crossorigin='anonymous'></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
  
  </body>
</html>