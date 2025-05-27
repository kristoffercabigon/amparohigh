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
$request_id = isset($_SESSION['request_id']) ? $_SESSION['request_id'] : null;
$unseenComments = [];
$uploadeddocu = [];
$sessions = [];
$sessions1 = [];

$_SESSION['request_ids'] = $_SESSION['request_ids'] ?? [];
$_SESSION['requestor_ids'] = $_SESSION['requestor_ids'] ?? [];
$_SESSION['reference_nos'] = $_SESSION['reference_nos'] ?? [];

if ($registrarId) {
    $sql = "SELECT id, commentor, comment_text, commented_at, request_id, requestor_id, reference_no
            FROM comments 
            WHERE registrar_seen = 0 
            AND user_type = 1 
            AND registrar_id = ?
            ORDER BY commented_at DESC"; 
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

    foreach ($unseenComments as &$comment) {
        $dateTime = new DateTime($comment['commented_at']);
        $comment['commented_at'] = $dateTime->format('F j, Y, g:i A'); 
    }
}

if ($registrarId) {
    $sql = "SELECT request_id, COUNT(*) AS file_count, GROUP_CONCAT(`filename` SEPARATOR ', ') AS filenames, upload_date, req_name, reference_no 
            FROM uploaded_docus 
            WHERE reg_seen = 0 
            AND registrar_id = ?
            GROUP BY request_id
            ORDER BY upload_date DESC"; 
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $con->error);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    $stmt->bind_param('i', $registrarId);
    $stmt->execute();
    $uploadeddocu = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($uploadeddocu as &$doc) {
        $dateTime = new DateTime($doc['upload_date']);
        $doc['upload_date'] = $dateTime->format('F j, Y, g:i A'); 
    }
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

if ($registrarId) {
    $sql = "SELECT request.id AS request_id, request.reference_no AS reference_no, request.requestor_id AS requestor_id
            FROM uploaded_docus 
            JOIN request ON uploaded_docus.request_id = request.id
            JOIN requestor ON request.requestor_id = requestor.id
            WHERE reg_seen = 0 
            AND registrar_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $con->error);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    $stmt->bind_param('i', $registrarId);
    $stmt->execute();
    $sessions1 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($sessions1 as $session) {
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
    'sessions' => array_merge($sessions, $sessions1),
    'uploadeddocus' => $uploadeddocu
];

header('Content-Type: application/json');
echo json_encode($response);