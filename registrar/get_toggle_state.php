<?php
$con = new mysqli('localhost', 'root', '', 'amparohigh');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$id = intval($_GET['id']);

$sql = "SELECT upload_requirements FROM request WHERE id = ?";
$stmt = $con->prepare($sql);

if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            echo json_encode($row);
        } else {
            echo json_encode(['upload_requirements' => 0]);
        }
    } else {
        echo json_encode(['upload_requirements' => 0]);
    }

    $stmt->close();
} else {
    echo json_encode(['upload_requirements' => 0]);
}

$con->close();
?>
