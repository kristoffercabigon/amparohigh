<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

$registrar_id = $_SESSION['registrar_id']; 

if (isset($_GET['ids'])) {
    $ids = explode(',', $_GET['ids']);
    $ids = array_map('intval', $ids);

    if (count($ids) > 0) {
        $idsList = implode(',', $ids);

        $sql = "SELECT COUNT(*) AS count
                FROM request 
                WHERE id IN ($idsList) AND (assisted_by_id = ?)";

        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("i", $registrar_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count = $row['count'];

            $total = count($ids);
            $showUnassistButton = ($count === $total);

            echo json_encode(['showUnassistButton' => $showUnassistButton]);
            
            $stmt->close();
        } else {

            echo json_encode(['error' => 'SQL preparation failed']);
        }
    } else {
        echo json_encode(['showUnassistButton' => false]);
    }
} else {
    echo json_encode(['showUnassistButton' => false]);
}

$con->close();
?>
