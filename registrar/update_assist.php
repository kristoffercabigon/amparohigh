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
    $reg_id = intval($_SESSION['registrar_id']);
    $id = intval($_POST['request_id']); 

    $sqlassistupdate = "UPDATE request SET assisted_by_id = $reg_id, status_id = 2 WHERE id = $id";
    $resultassistupdate = $con->query($sqlassistupdate); 

    $sqlupdatedocustat = "UPDATE reqdocu SET document_status_id = 2 WHERE request_id = $id";
    $resultupdatedocustat = $con->query($sqlupdatedocustat);

    if ($resultassistupdate && $resultupdatedocustat) {
        echo 'success'; 
    } else {
        echo 'error'; 
    }
    exit;
}
?>
