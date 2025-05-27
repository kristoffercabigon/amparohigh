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

$registrarId = isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : null;
$unseenComments = [];
$sessions = [];

$_SESSION['request_ids'] = $_SESSION['request_ids'] ?? [];
$_SESSION['requestor_ids'] = $_SESSION['requestor_ids'] ?? [];
$_SESSION['reference_nos'] = $_SESSION['reference_nos'] ?? [];

if ($registrarId) {
    $sql = "SELECT id, commentor, comment_text, commented_at, request_id, requestor_id 
            FROM comments 
            WHERE registrar_seen = 0 
            AND user_type = 1 
            AND registrar_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $con->error);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    $stmt->bind_param('i', $registrarId);
    $stmt->execute();
    $unseenComments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

if ($registrarId) {
    $sql = "SELECT request.id AS request_id, request.reference_no AS reference_no, request.requestor_id AS requestor_id
            FROM comments 
            JOIN request ON comments.request_id = request.id
            WHERE registrar_seen = 0 
            AND user_type = 1 
            AND registrar_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $con->error);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    $stmt->bind_param('i', $registrarId);
    $stmt->execute();
    $sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($sessions as $session) {
        if (!in_array($session['request_id'], $_SESSION['request_ids'])) {
            $_SESSION['request_ids'][] = $session['request_id'];
            $_SESSION['requestor_ids'][] = $session['requestor_id'];
            $_SESSION['reference_nos'][] = $session['reference_no'];
        }
    }
}

$con->close();

$response = [
    'unseenComments' => $unseenComments,
    'sessions' => $sessions
];

header('Content-Type: application/json');
echo json_encode($response);
?>