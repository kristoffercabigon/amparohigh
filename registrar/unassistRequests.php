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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ids'])) {

    $ids = explode(',', $_GET['ids']);
    $reg_id = intval($_SESSION['registrar_id']); 

    foreach ($ids as $id) {
        $id = intval($id); 

        $sqlUnassistUpdate = "UPDATE request SET assisted_by_id = NULL, status_id = 1 WHERE id = $id";
        $resultUnassistUpdate = $con->query($sqlUnassistUpdate);

        $sqlUpdateDocuStat = "UPDATE reqdocu SET document_status_id = 1 WHERE request_id = $id";
        $resultUpdateDocuStat = $con->query($sqlUpdateDocuStat);

        if (!$resultUnassistUpdate || !$resultUpdateDocuStat) {
            echo 'error'; 
            exit;
        }
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo 'Invalid request';
    exit;
}

$con->close();
?>
