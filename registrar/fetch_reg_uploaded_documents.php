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
$uploadeddocu = [];
$sessions1 = [];

$_SESSION['request_ids'] = $_SESSION['request_ids'] ?? [];
$_SESSION['requestor_ids'] = $_SESSION['requestor_ids'] ?? [];
$_SESSION['reference_nos'] = $_SESSION['reference_nos'] ?? [];

// Fetch uploaded documents
if ($request_id && $registrarId) {
    $sql = "SELECT id, `filename`, filesize, filetype, upload_date, request_id 
            FROM uploaded_docus 
            WHERE reg_seen = 0 
            AND request_id = ? 
            AND registrar_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $con->error);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    $stmt->bind_param('ii', $request_id, $registrarId);
    $stmt->execute();
    $uploadeddocu = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch session data for uploaded documents
if ($request_id && $registrarId) {
    $sql = "SELECT request.id AS request_id, request.reference_no AS reference_no, request.requestor_id AS requestor_id
            FROM uploaded_docus 
            JOIN request ON uploaded_docus.request_id = request.id
            WHERE reg_seen = 0 
            AND request_id = ? 
            AND registrar_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("SQL Prepare failed: " . $con->error);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    $stmt->bind_param('ii', $request_id, $registrarId);
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
    'uploadeddocus' => $uploadeddocu,
    'sessions1' => $sessions1
];

header('Content-Type: application/json');
echo json_encode($response);
