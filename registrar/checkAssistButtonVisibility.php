<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

$registrar_id = $_SESSION['registrar_id']; 
$registrar_name = $_SESSION['registrar_name']; 

if (isset($_GET['ids'])) {
    $ids = explode(',', $_GET['ids']);
    $ids = array_map('intval', $ids); 

    if (count($ids) > 0) {
        $idsList = implode(',', $ids);

        $sql = "SELECT COUNT(*) AS count
                FROM request 
                WHERE id IN ($idsList) AND (assisted_by_id IS NOT NULL)";

        if ($stmt = $con->prepare($sql)) {
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count = $row['count'];

            echo json_encode(['showAssistButton' => ($count === 0)]);
            
            $stmt->close();
        } else {
            echo json_encode(['error' => 'SQL preparation failed']);
        }
    } else {
        echo json_encode(['showAssistButton' => true]);
    }
} else {
    echo json_encode(['showAssistButton' => true]);
}

$con->close();
?>
