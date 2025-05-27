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

$registrar_id = isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : '';

if (isset($_GET['ids'])) {
    $ids = explode(',', $_GET['ids']);
    $ids = array_map('intval', $ids);

    if (count($ids) > 0) {
        $idsList = implode(',', $ids);

        $sql = "SELECT COUNT(*) AS count
                FROM request 
                WHERE id IN ($idsList) AND (assisted_by_id IS NOT NULL AND assisted_by_id <> ?)";

        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("i", $registrar_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count = $row['count'];

            $showDeleteButton = ($count === 0); 

            echo json_encode(['showDeleteButton' => $showDeleteButton]);
            
            $stmt->close();
        } else {
            echo json_encode(['error' => 'SQL preparation failed']);
        }
    } else {
        echo json_encode(['showDeleteButton' => false]);
    }
} else {
    echo json_encode(['showDeleteButton' => false]);
}

$con->close();
?>
