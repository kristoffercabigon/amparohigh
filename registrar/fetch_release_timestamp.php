<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestId = $_POST['request_id'];

    $sql = "SELECT date_released FROM request WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        if ($row["date_released"] !== NULL && $row["date_released"] !== '') {
            $releasedDateTime = new DateTime($row["date_released"]);
            $formattedDate = $releasedDateTime->format('F j, Y');
            $formattedTime = $releasedDateTime->format('g:i A');
            echo $formattedDate . ' at ' . $formattedTime;
        } else {
            echo 'Request is not being released yet.';
        }
    } else {
        echo 'No request record found.';
    }

    $stmt->close();
}

$con->close();
?>
