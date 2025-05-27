<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_POST["saveNoteText"])) {
    $note = $_POST["noteText"];
    $request_id = $_POST["request_id"];
    $docus_id = $_POST["docus_id"];

    $stmt = $con->prepare("UPDATE reqdocu SET notes = ? WHERE request_id = ? AND docus_id = ?");
    if ($stmt) {

        $stmt->bind_param("sii", $note, $request_id, $docus_id);

        if ($stmt->execute()) {
            echo "Note updated successfully";
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
