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

        $sqlassistupdate = "UPDATE request SET assisted_by_id = $reg_id, status_id = 2 WHERE id = $id";
        $resultassistupdate = $con->query($sqlassistupdate);

        $sqlupdatedocustat = "UPDATE reqdocu SET document_status_id = 2 WHERE request_id = $id";
        $resultupdatedocustat = $con->query($sqlupdatedocustat);

        if (!$resultassistupdate || !$resultupdatedocustat) {
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
