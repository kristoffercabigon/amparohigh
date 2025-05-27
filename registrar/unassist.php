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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unassist'])) {
    $id = intval($_POST['request_id']); 
    
    $sqlUnassistUpdate = "UPDATE request SET assisted_by_id = NULL, status_id = 1 WHERE id = $id";
    $resultUnassistUpdate = $con->query($sqlUnassistUpdate);

    $sqlUpdateDocuStat = "UPDATE reqdocu SET document_status_id = 1 WHERE request_id = $id";
    $resultUpdateDocuStat = $con->query($sqlUpdateDocuStat);

    if ($resultUnassistUpdate && $resultUpdateDocuStat) {
        echo 'success'; 
    } else {
        echo 'error'; 
    }
    exit;
}
?>
