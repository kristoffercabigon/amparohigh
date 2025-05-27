<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;

$sql = "UPDATE request SET date_released = NOW() WHERE id = ?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param('i', $request_id);

if ($stmt->execute()) {
    echo 'Request released successfully!';
} else {
    echo 'Failed to release request.';
}

$stmt->close();
$con->close();
?>
