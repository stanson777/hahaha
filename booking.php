<?php
session_start();
require_once 'klasy.php'; 
if(!isset($_SESSION['user'])){
    header("Location: loginPage.php");
}


if (!isset($_SESSION['all_appointments'])) {
    $_SESSION['all_appointments'] = [];
}

if (!isset($_SESSION['upcoming_visits'])) {
    $_SESSION['upcoming_visits'] = [];
}

if (!isset($_SESSION['visit_history'])) {
    $_SESSION['visit_history'] = [];
}

$lekarzId = $_GET['id'];

function znajdzLekarzaPoId($id) {
    global $array; 
    foreach ($array as $lekarz) {
        if ($lekarz->numerLegitymacji() == $id) {
            return $lekarz;
        }
    }
    return null;
}

$lekarz = znajdzLekarzaPoId($lekarzId);
$bookedSlots = array_column($_SESSION['all_appointments'], 'dateTime');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['appointment-date'];
    $time = $_POST['appointment-time'];
    $description = isset($_POST['appointment-description']) ? $_POST['appointment-description'] : '';
    $recommendations = isset($_POST['appointment-recommendations']) ? $_POST['appointment-recommendations'] : '';
    $today = date('Y-m-d');
    $dateTime = $date . ' ' . $time;

    if (!in_array($dateTime, $bookedSlots)) {
        if ($date && $time) {
            if ($date < $today) {
                $_SESSION['visit_history'][] = [
                    'doctor' => $lekarz->pelneNazwisko(),
                    'date' => $date,
                    'time' => $time,
                    'dateTime' => $dateTime,
                    'description' => $description,
                    'recommendations' => $recommendations
                ];
                $_SESSION['all_appointments'][] = [
                    'doctor' => $lekarz->pelneNazwisko(),
                    'date' => $date,
                    'time' => $time,
                    'dateTime' => $dateTime,
                    'description' => $description,
                    'recommendations' => $recommendations
                ];
                echo "<script>alert('Wizyta zarezerwowana na $date o godzinie $time i dodana do historii wizyt');</script>";
            } else {
                $_SESSION['upcoming_visits'][] = [
                    'doctor' => $lekarz->pelneNazwisko(),
                    'date' => $date,
                    'time' => $time,
                    'dateTime' => $dateTime,
                    'description' => $description,
                    'recommendations' => $recommendations
                ];
                $_SESSION['all_appointments'][] = [
                    'doctor' => $lekarz->pelneNazwisko(),
                    'date' => $date,
                    'time' => $time,
                    'dateTime' => $dateTime,
                    'description' => $description,
                    'recommendations' => $recommendations
                ];
                echo "<script>alert('Wizyta zarezerwowana na $date o godzinie $time');</script>";
            }
        } else {
            echo "<script>alert('Proszę wybrać datę i godzinę wizyty');</script>";
        }
    } else {
        echo "<script>alert('Ten slot jest już zajęty. Wybierz inną datę lub godzinę.');</script>";
    }
}

$reviews = [
    ["name" => "Anonimowy", "date" => "2024-06-16", "content" => "Polecam"],
    ["name" => "Angelika", "date" => "2024-06-16", "content" => "Polecam serdecznie Pana doktora"],
    ["name" => "Anonimowy", "date" => "2024-06-15", "content" => "Świetny lekarz polecam"],
    ["name" => "Joanna Plotnicka", "date" => "2024-06-15", "content" => "Polecam!"],
    ["name" => "Adam", "date" => "2024-06-15", "content" => "Bardzo dobry, empatyczny i wyrozumiały lekarz. Dziękuję za pomoc."],
    ["name" => "Mateusz", "date" => "2024-06-14", "content" => "Wszystko super"]
];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezerwacja Wizyty</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" type="text/css" href="navigator.css">
    <style>
        .booking-container {
            text-align:center;
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .calendar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .timeslots {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .timeslot {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .timeslot.selected {
            background-color: #007bff;
            color: #fff;
        }
        
        .timeslot.disabled {
            background-color: #ddd;
            color: #888;
            cursor: not-allowed;
        }

        .appointment-details {
            margin-top: 20px;
        }

        .appointment-details textarea {
            width: 100%;
            margin-bottom: 10px;
        }

        .visit-history {
            margin-top: 30px;
            text-align: left;
        }

        .visit {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    
    <div class="booking-container">
        <h1>Rezerwuj e-Wizytę</h1>
        <h2>Wybierz z kalendarza dogodną dla Ciebie datę i godzinę e-wizyty</h2>
        <form method="post" action="">
            <div class="calendar">
                <input type="date" id="appointment-date" name="appointment-date">
            </div>
            <div class="timeslots">
                <?php
                if (isset($_POST['appointment-date'])) {
                    $date = $_POST['appointment-date'];
                } else {
                    $date = '';
                }
                $times = ["15:30", "15:45", "16:00", "16:15", "16:30", "16:45", "17:00", "17:15", "17:30", "17:45", "18:00", "18:15", "18:30", "18:45", "19:00", "19:15", "19:30", "19:45", "20:00", "20:15", "20:30", "20:45"];
                foreach ($times as $time) {
                    $dateTime = $date . ' ' . $time;
                    $disabled = !empty($date) && in_array($dateTime, $bookedSlots);
                    echo "<label class='timeslot" . ($disabled ? ' disabled' : '') . "'><input type='radio' name='appointment-time' value='$time'" . ($disabled ? ' disabled' : '') . "> $time</label>";
                }
                ?>
            </div>
            
            <button type="submit">Potwierdź Wizytę</button>
        </form>
    </div>

    
</body>
</html>