<?php

include("registrar_header.php");

$con = new connec();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status_id = $_POST['status_id'];
    $request_id = $_POST['request_id'];

    $updateSql = "UPDATE request SET status_id = ? WHERE id = ?";
    $stmt = $con->prepare($updateSql);
    $stmt->bind_param('ii', $status_id, $request_id);

    if ($stmt->execute()) {
        echo "Status updated successfully!";
    } else {
        echo "Error updating status: " . $con->error;
    }

    $stmt->close();
    $con->close();
}
?>
