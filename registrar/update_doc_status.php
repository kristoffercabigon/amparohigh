<?php
include("registrar_header.php");

$con = new connec();

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status_id = $_POST['status_id'];
    $docus_id = $_POST['docus_id'];
    $request_id = $_SESSION["request_id"]; 

    $updateSql = "UPDATE reqdocu 
                  SET document_status_id = ? 
                  WHERE docus_id = ? 
                  AND request_id = ?";
    $stmt = $con->prepare($updateSql);
    $stmt->bind_param('iii', $status_id, $docus_id, $request_id);

    if ($stmt->execute()) {
        echo "Status updated successfully!";
    } else {
        echo "Error updating status: " . $con->error;
    }

    $stmt->close();
}
?>
