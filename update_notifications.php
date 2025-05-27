<?php
if (!isset($_SESSION)) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$userId = isset($_SESSION['requestor_id']) ? $_SESSION['requestor_id'] : null;
$requestId = isset($_SESSION['request_id']) ? $_SESSION['request_id'] : null;
$notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : null;
$response = ['success' => false];

if ($userId && $notificationId) {
    $sql = "UPDATE Comments 
            SET requestor_seen = 1 
            WHERE id = ? 
            AND requestor_id = ? 
            AND requestor_seen = 0 
            AND user_type = 0";

    $stmt = $con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ii', $notificationId, $userId);
        if ($stmt->execute()) {
            $response['success'] = true;
        }
        $stmt->close();
    } else {
        die("Prepare failed: " . $con->error);
    }
}

if ($notificationId && $requestId) {
    $sql = "UPDATE reqdocu 
            SET note_notif = 1 
            WHERE id = ?
            AND request_id = ? ";

    $stmt = $con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ii', $notificationId, $requestId);
        if ($stmt->execute()) {
            $response['success'] = true;
        }
        $stmt->close();
    } else {
        die("Prepare failed: " . $con->error);
    }
}

$con->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
