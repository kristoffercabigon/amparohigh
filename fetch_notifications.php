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
$unseenComments = [];
$uploadRequirementStatusChanged = false;
$noteNotification = null;
$documentNotification = null;

if ($userId) {
    $sql = "SELECT id, commentor, comment_text, commented_at 
            FROM Comments 
            WHERE requestor_seen = 0 
            AND user_type = 0
            AND requestor_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $unseenComments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $sql = "SELECT reqdocu.notes, docus.documents 
            FROM reqdocu 
            JOIN docus ON reqdocu.docus_id = docus.id 
            WHERE reqdocu.request_id = ? 
            AND reqdocu.notes IS NOT NULL 
            AND reqdocu.note_notif = 0";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $noteNotification = $row['notes'];
        $documentNotification = $row['documents'];
    }
    $stmt->close();

    $sql = "SELECT upload_requirements FROM request WHERE requestor_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($uploadRequirements);
    $stmt->fetch();
    $stmt->close();

    if ($uploadRequirements == 1) {
        $uploadRequirementStatusChanged = true;
    }
}

$con->close();

$response = [
    'unseenComments' => $unseenComments,
    'uploadRequirementStatusChanged' => $uploadRequirementStatusChanged,
    'noteNotification' => $noteNotification,
    'documentNotification' => $documentNotification
];

header('Content-Type: application/json');
echo json_encode($response);
?>
