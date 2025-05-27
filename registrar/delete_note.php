<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_POST["docus_id"]) && isset($_POST["request_id"])) {
    $docus_id = $_POST["docus_id"];
    $request_id = $_POST["request_id"];

    $stmt = $con->prepare("UPDATE reqdocu SET notes = NULL WHERE request_id = ? AND docus_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $request_id, $docus_id);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }
}

$con->close();
?>
