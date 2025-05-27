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
$registrar_id = isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : null;
$notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : null;
$response = ['success' => false];

if ($registrar_id && $notificationId) {
    $sql = "UPDATE comments 
            SET registrar_seen = 1 
            WHERE id = ? 
            AND registrar_id = ? 
            AND registrar_seen = 0 
            AND user_type = 1";

    $stmt = $con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ii', $notificationId, $registrar_id);
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
