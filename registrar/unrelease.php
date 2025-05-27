<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assist'])) {
    $id = isset($_SESSION["request_id"]) ? intval($_SESSION["request_id"]) : null;

    if ($id) {
        $sqlunrelease = "UPDATE request SET date_released = NULL WHERE id = $id";
        $resultunrelease = $con->query($sqlunrelease);

        if ($resultunrelease) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }

    exit;
}
?>
