<?php
    session_start();
    include('../connect.php');

    if(!isset($_SESSION['clientlogin'])){
        header('location:../signin.php');
    }

    $client_id = $_SESSION['clientlogin'];
    $target = $_SESSION['target'];

    $target = htmlentities($target);
    $client_id = htmlentities($client_id);

    //create meditation program for 56 days inbetween start date and finish date
    $stmt = $conn->prepare("INSERT INTO `meditation_program` (`id`, `client_id`, `start_date`, `end_date`) VALUES (NULL, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL +56 DAY));");
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $stmt->close();

    //create the experience level 1 for new client and set experience points to 0
    $stmt1 = $conn->prepare("INSERT INTO `client_level` (`id`, `client_id`, `level`, `grand_total`) VALUES (NULL, ?, 1, 0);");
    $stmt1->bind_param('i', $client_id);
    $stmt1->execute();
    $stmt1->close();

    //select program id and start date for the program just created
    $stmt2 = $conn->prepare("SELECT id, start_date FROM meditation_program WHERE client_id = ?");
    $stmt2 -> bind_param("i", $client_id);
    $stmt2 -> execute();
    $stmt2 -> store_result(); 
    $stmt2 -> bind_result($program_id, $start_date);
    $stmt2 -> fetch();
    $stmt2->close();

    $stmt3 = $conn->prepare("INSERT INTO complete_exp_details (date, client_id, program_complete_id, program_exp, meditation_complete_id, meditation_exp, total_exp, program_length) VALUES ( ?, ?, '2', 0, '2', 0, 0, 7) ");
    $stmt4 = $conn->prepare("SELECT id FROM complete_exp_details WHERE client_id = ? AND date = ?");
    $stmt5 = $conn->prepare("INSERT INTO meditation_complete (client_id, meditation_program_id, time_total, target, complete_exp_details_id, day, week_level, sessions) VALUES ( ?, ?, 0, ?, ?, ?, ?, 0) ");
    $day = 0;
    $week = 0;
    //While loop for 56 days to populate 56 day/8 week meditation program
    while($day<56){
        if($day%7 == 0){
            $week++;
        }
        $date = strtotime("$start_date +$day days");
        $date = date("Y-m-d", $date);
        $day++;
        //insert into complete_exp_details
        $stmt3->bind_param('si', $date, $client_id);
        $stmt3->execute();
        //get the $complete_exp_details_id for the newly inserted row
        $stmt4 -> bind_param("is", $client_id, $date);
        $stmt4 -> execute();
        $stmt4 -> store_result();
        $stmt4 -> bind_result($id);
        $stmt4 -> fetch();
        //insert into meditation_complete
        $stmt5->bind_param('iiiiii', $client_id, $program_id, $target, $id, $day, $week);
        $stmt5->execute();
    }
    $stmt3->close();
    $stmt4->close();
    $stmt5->close();

    /*create a row in the database for the new client to hold booleans on which interventions are available. All interventions are available once for first time client.
    once the intervention has been used it is not available again until the human coach enables it*/
    $stmt6 = $conn->prepare("INSERT INTO `coach_intervene` (`id`, `client_id`, `program_length`, `target_time`, `restart_program`) VALUES (NULL, ?, '1', '1', '1');");
    $stmt6->bind_param('i', $client_id);
    $stmt6->execute();
    $stmt6->close();

    $message = 'Welcome to the Mindfulness-Based Stress Reduction virtual program. How are you getting on?';

    //Insert a welcome message into the chat system for the new client
    $stmt = $conn->prepare("INSERT INTO help_request_message (`id`, `client_id`, `message`, `date`, `time`, `author`, `read`) VALUES (NULL, ?, ?, CURDATE(), CURRENT_TIME(), 'Coach', 0);");
    $stmt->bind_param('is', $client_id, $message);
    $stmt->execute();
    $stmt->close();

    //take client to meditation page
    header('location:../meditate/meditate.php');

?>