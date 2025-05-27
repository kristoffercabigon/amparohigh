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

    $sqlsent = "SELECT sent_email_at FROM request WHERE id = ?";
    $stmt = $con->prepare($sqlsent);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        if ($row["sent_email_at"] !== NULL) {
            $sentDateTime = new DateTime($row["sent_email_at"]);
            
            $formattedDate = $sentDateTime->format('F j, Y'); 
            $formattedTime = $sentDateTime->format('g:i A'); 
            
            echo '<span style="color: black;">' . $formattedDate . ' at ' . $formattedTime . '</span>';
        } else {
            echo 'Timestamp not found.';
        }
    } else {
        echo 'Timestamp not found.';
    }

    $stmt->close();
}

$con->close();
?>
